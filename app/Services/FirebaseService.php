<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    /**
     * Get Firebase Web SDK client configuration.
     *
     * @return array<string, string|null>
     */
    public function getWebConfig(): array
    {
        return [
            'apiKey' => config('firebase.api_key'),
            'authDomain' => config('firebase.auth_domain'),
            'projectId' => config('firebase.project_id'),
            'storageBucket' => config('firebase.storage_bucket'),
            'messagingSenderId' => config('firebase.messaging_sender_id'),
            'appId' => config('firebase.app_id'),
            'measurementId' => config('firebase.measurement_id'),
        ];
    }

    /**
     * Send push notification to a single FCM device token.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function sendToToken(string $fcmToken, string $title, string $body, array $data = []): array
    {
        if (empty($fcmToken)) {
            return [
                'success' => false,
                'message' => 'FCM Token cannot be empty.',
            ];
        }

        // Try HTTP v1 API first (Service Account credentials)
        $credentials = $this->getServiceAccountCredentials();
        if ($credentials) {
            return $this->sendHttpV1($credentials, $fcmToken, $title, $body, $data);
        }

        // Fallback to legacy server key if provided
        $serverKey = config('firebase.server_key');
        if (! empty($serverKey)) {
            return $this->sendLegacyHttp($serverKey, $fcmToken, $title, $body, $data);
        }

        return [
            'success' => false,
            'message' => 'Firebase credentials not configured. Please place your service-account.json at storage/app/firebase/service-account.json or set FIREBASE_CREDENTIALS / FIREBASE_SERVER_KEY.',
        ];
    }

    /**
     * Send push notification to multiple FCM device tokens.
     *
     * @param  array<int, string>  $tokens
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function sendToTokens(array $tokens, string $title, string $body, array $data = []): array
    {
        $uniqueTokens = array_values(array_filter(array_unique($tokens)));
        if (empty($uniqueTokens)) {
            return [
                'success' => false,
                'message' => 'No valid FCM tokens provided.',
                'results' => [],
            ];
        }

        $results = [
            'total' => count($uniqueTokens),
            'success_count' => 0,
            'failure_count' => 0,
            'details' => [],
        ];

        foreach ($uniqueTokens as $token) {
            $response = $this->sendToToken($token, $title, $body, $data);
            $results['details'][$token] = $response;

            if (! empty($response['success'])) {
                $results['success_count']++;
            } else {
                $results['failure_count']++;
            }
        }

        $results['success'] = $results['success_count'] > 0;

        return $results;
    }

    /**
     * Send push notification to a specific user.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function sendToUser(User $user, string $title, string $body, array $data = []): array
    {
        if (empty($user->fcm_token)) {
            return [
                'success' => false,
                'message' => "User {$user->username} has not registered an FCM token.",
            ];
        }

        return $this->sendToToken($user->fcm_token, $title, $body, $data);
    }

    /**
     * Send push notification to all users who have an FCM token.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public function sendToAllUsers(string $title, string $body, array $data = []): array
    {
        $tokens = User::whereNotNull('fcm_token')
            ->where('fcm_token', '!=', '')
            ->pluck('fcm_token')
            ->toArray();

        return $this->sendToTokens($tokens, $title, $body, $data);
    }

    /**
     * Send notification using Firebase HTTP v1 API.
     *
     * @param  array<string, mixed>  $credentials
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function sendHttpV1(array $credentials, string $fcmToken, string $title, string $body, array $data = []): array
    {
        $projectId = $credentials['project_id'] ?? config('firebase.project_id');
        $accessToken = $this->getOAuth2AccessToken($credentials);

        if (! $accessToken) {
            return [
                'success' => false,
                'message' => 'Failed to obtain Google OAuth2 access token for Firebase HTTP v1 API.',
            ];
        }

        $url = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";

        // Convert data values to strings as required by Firebase HTTP v1
        $stringData = [];
        foreach ($data as $key => $value) {
            $stringData[(string) $key] = is_string($value) ? $value : json_encode($value);
        }

        $payload = [
            'message' => [
                'token' => $fcmToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                ],
                'android' => [
                    'priority' => 'high',
                    'notification' => [
                        'sound' => 'default',
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    ],
                ],
                'apns' => [
                    'payload' => [
                        'aps' => [
                            'sound' => 'default',
                        ],
                    ],
                ],
            ],
        ];

        if (! empty($stringData)) {
            $payload['message']['data'] = $stringData;
        }

        try {
            $response = Http::withToken($accessToken)
                ->acceptJson()
                ->post($url, $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Notification sent successfully.',
                    'response' => $response->json(),
                ];
            }

            Log::error('Firebase HTTP v1 error: '.$response->body());

            return [
                'success' => false,
                'message' => 'Failed to send notification via Firebase HTTP v1.',
                'error' => $response->json(),
            ];
        } catch (\Throwable $e) {
            Log::error('Firebase HTTP v1 exception: '.$e->getMessage());

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send notification using legacy Firebase HTTP API.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function sendLegacyHttp(string $serverKey, string $fcmToken, string $title, string $body, array $data = []): array
    {
        $url = 'https://fcm.googleapis.com/fcm/send';

        $payload = [
            'to' => $fcmToken,
            'notification' => [
                'title' => $title,
                'body' => $body,
                'sound' => 'default',
            ],
            'data' => $data,
            'priority' => 'high',
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key='.$serverKey,
                'Content-Type' => 'application/json',
            ])->post($url, $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Notification sent successfully via legacy FCM.',
                    'response' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to send notification via legacy FCM.',
                'error' => $response->json(),
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Retrieve service account credentials from file path or JSON config.
     *
     * @return array<string, mixed>|null
     */
    protected function getServiceAccountCredentials(): ?array
    {
        $jsonString = config('firebase.credentials_json');
        if (! empty($jsonString)) {
            $data = json_decode($jsonString, true);
            if (is_array($data) && ! empty($data['private_key']) && ! empty($data['client_email'])) {
                return $data;
            }
        }

        $path = config('firebase.credentials_path');
        if (! empty($path)) {
            $resolvedPath = str_starts_with($path, '/') ? $path : base_path($path);
            if (file_exists($resolvedPath)) {
                $content = file_get_contents($resolvedPath);
                $data = json_decode((string) $content, true);
                if (is_array($data) && ! empty($data['private_key']) && ! empty($data['client_email'])) {
                    return $data;
                }
            }
        }

        return null;
    }

    /**
     * Get or create Google OAuth2 access token using the Service Account credentials.
     *
     * @param  array<string, mixed>  $credentials
     */
    protected function getOAuth2AccessToken(array $credentials): ?string
    {
        $cacheKey = 'firebase_oauth2_access_token_'.md5((string) ($credentials['client_email'] ?? ''));

        return Cache::remember($cacheKey, 3300, function () use ($credentials) {
            return $this->fetchNewOAuth2AccessToken($credentials);
        });
    }

    /**
     * Sign JWT and exchange with Google OAuth2 token endpoint.
     *
     * @param  array<string, mixed>  $credentials
     */
    protected function fetchNewOAuth2AccessToken(array $credentials): ?string
    {
        $clientEmail = $credentials['client_email'] ?? null;
        $privateKey = $credentials['private_key'] ?? null;

        if (! $clientEmail || ! $privateKey) {
            return null;
        }

        $now = time();
        $header = ['alg' => 'RS256', 'typ' => 'JWT'];
        $claims = [
            'iss' => $clientEmail,
            'sub' => $clientEmail,
            'aud' => 'https://oauth2.googleapis.com/token',
            'iat' => $now,
            'exp' => $now + 3600,
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        ];

        $base64UrlHeader = $this->base64UrlEncode(json_encode($header));
        $base64UrlClaims = $this->base64UrlEncode(json_encode($claims));
        $signatureInput = "{$base64UrlHeader}.{$base64UrlClaims}";

        $signature = '';
        $success = openssl_sign($signatureInput, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        if (! $success) {
            Log::error('Failed to sign JWT with Firebase private key.');

            return null;
        }

        $jwt = "{$signatureInput}.".$this->base64UrlEncode($signature);

        try {
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

            if ($response->successful()) {
                return $response->json('access_token');
            }

            Log::error('Google OAuth2 token exchange error: '.$response->body());

            return null;
        } catch (\Throwable $e) {
            Log::error('Google OAuth2 token exchange exception: '.$e->getMessage());

            return null;
        }
    }

    /**
     * Encode string to base64url format.
     */
    protected function base64UrlEncode(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
