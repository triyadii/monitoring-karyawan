<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    public function __construct(
        protected FirebaseService $firebaseService
    ) {}

    #[OA\Post(
        path: '/api/login',
        summary: 'Login user and get JWT token',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['username', 'password', 'fcm_token'],
                properties: [
                    new OA\Property(property: 'username', type: 'string', example: 'admin'),
                    new OA\Property(property: 'password', type: 'string', example: 'password'),
                    new OA\Property(property: 'fcm_token', type: 'string', example: 'eX123fcm_device_token_abc...'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful login',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'user', type: 'object', properties: [
                            new OA\Property(property: 'id', type: 'string', format: 'uuid'),
                            new OA\Property(property: 'role_id', type: 'string', format: 'uuid'),
                            new OA\Property(property: 'role_name', type: 'string'),
                            new OA\Property(property: 'jenis_pegawai_name', type: 'string'),
                            new OA\Property(property: 'username', type: 'string'),
                            new OA\Property(property: 'nama', type: 'string'),
                            new OA\Property(property: 'status', type: 'integer'),
                            new OA\Property(property: 'fcm_token', type: 'string'),
                            new OA\Property(property: 'is_password_expired', type: 'boolean'),
                        ]),
                        new OA\Property(property: 'authorization', type: 'object', properties: [
                            new OA\Property(property: 'token', type: 'string'),
                            new OA\Property(property: 'type', type: 'string', example: 'bearer'),
                            new OA\Property(property: 'expires_in', type: 'integer'),
                        ]),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 403, description: 'Device mismatch (FCM token does not match)'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'fcm_token' => 'required|string',
        ]);

        $credentials = $request->only('username', 'password');

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = auth('api')->user();

        // Check FCM Token Device Lock
        if (! empty($user->fcm_token) && $user->fcm_token !== $request->fcm_token) {
            auth('api')->logout();

            return response()->json([
                'error' => 'Device mismatch',
                'message' => 'Akun sedang aktif di perangkat lain. Silakan lakukan reset FCM token untuk login di perangkat ini.',
            ], 403);
        }

        // If user does not have an FCM token registered yet, register this device
        if (empty($user->fcm_token)) {
            $user->fcm_token = $request->fcm_token;
            $user->save();
        }

        // Send push notification to leader and superadmin roles
        try {
            $this->firebaseService->notifyUserLogin($user);
        } catch (\Throwable $e) {
            Log::error('Failed to notify leaders/superadmins on user login: '.$e->getMessage());
        }

        return $this->respondWithToken($token);
    }

    #[OA\Post(
        path: '/api/reset-fcm-token',
        summary: 'Reset FCM token with credentials to switch device',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['username', 'password'],
                properties: [
                    new OA\Property(property: 'username', type: 'string', example: 'admin'),
                    new OA\Property(property: 'password', type: 'string', example: 'password'),
                    new OA\Property(property: 'fcm_token', type: 'string', nullable: true, description: 'Optional new device FCM token. If omitted, fcm_token is cleared.'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'FCM token reset successful',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'message', type: 'string'),
                        new OA\Property(property: 'user', type: 'object'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Invalid username or password'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function resetFcmToken(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'fcm_token' => 'nullable|string',
        ]);

        $user = User::where('username', $validated['username'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'error' => 'Unauthorized',
                'message' => 'Username atau password salah.',
            ], 401);
        }

        $user->fcm_token = $validated['fcm_token'] ?? null;
        $user->save();

        return response()->json([
            'message' => 'FCM token berhasil direset. Silakan login di perangkat baru.',
            'user' => [
                'id' => $user->id,
                'username' => $user->username,
                'nama' => $user->nama,
                'fcm_token' => $user->fcm_token,
            ],
        ]);
    }

    #[OA\Post(
        path: '/api/logout',
        summary: 'Logout user (Invalidate the token)',
        security: [['bearerAuth' => []]],
        tags: ['Authentication'],
        responses: [
            new OA\Response(response: 200, description: 'Successfully logged out'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ]
    )]
    public function logout()
    {
        auth('api')->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    protected function respondWithToken($token)
    {
        $user = auth('api')->user();
        $user->load(['role', 'jenisPegawai']);

        return response()->json([
            'user' => [
                'id' => $user->id,
                'role_id' => $user->role_id,
                'role_name' => $user->role ? $user->role->nama_role : '',
                'jenis_pegawai_name' => $user->jenisPegawai ? $user->jenisPegawai->jenisPegawai : '',
                'username' => $user->username,
                'nama' => $user->nama,
                'status' => $user->status,
                'fcm_token' => $user->fcm_token,
                'is_password_expired' => ! $user->password_updated_at || $user->password_updated_at->copy()->addDays(30)->isPast(),
            ],
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
            ],
        ]);
    }
}
