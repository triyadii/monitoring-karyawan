<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FirebaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class NotificationController extends Controller
{
    public function __construct(
        protected FirebaseService $firebaseService
    ) {}

    #[OA\Get(
        path: '/api/firebase-config',
        summary: 'Get Firebase Web & Messaging configuration',
        tags: ['Notifications'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Firebase configuration',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'apiKey', type: 'string'),
                        new OA\Property(property: 'authDomain', type: 'string'),
                        new OA\Property(property: 'projectId', type: 'string'),
                        new OA\Property(property: 'storageBucket', type: 'string'),
                        new OA\Property(property: 'messagingSenderId', type: 'string'),
                        new OA\Property(property: 'appId', type: 'string'),
                        new OA\Property(property: 'measurementId', type: 'string'),
                    ]
                )
            ),
        ]
    )]
    public function config(): JsonResponse
    {
        return response()->json($this->firebaseService->getWebConfig());
    }

    #[OA\Post(
        path: '/api/notifications/send-to-token',
        summary: 'Send push notification to a specific FCM token',
        security: [['bearerAuth' => []]],
        tags: ['Notifications'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['fcm_token', 'title', 'body'],
                properties: [
                    new OA\Property(property: 'fcm_token', type: 'string'),
                    new OA\Property(property: 'title', type: 'string', example: 'Pengumuman Penting'),
                    new OA\Property(property: 'body', type: 'string', example: 'Silakan cek jadwal kunjungan terbaru.'),
                    new OA\Property(property: 'data', type: 'object'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Notification dispatch result'),
            new OA\Response(response: 422, description: 'Validation error'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ]
    )]
    public function sendToToken(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'fcm_token' => 'required|string',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'data' => 'nullable|array',
        ]);

        $result = $this->firebaseService->sendToToken(
            $validated['fcm_token'],
            $validated['title'],
            $validated['body'],
            $validated['data'] ?? []
        );

        return response()->json($result);
    }

    #[OA\Post(
        path: '/api/notifications/send-to-user',
        summary: 'Send push notification to a specific user by ID',
        security: [['bearerAuth' => []]],
        tags: ['Notifications'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['title', 'body'],
                properties: [
                    new OA\Property(property: 'user_id', type: 'string', format: 'uuid', description: 'Defaults to current authenticated user if not provided'),
                    new OA\Property(property: 'title', type: 'string', example: 'Tugas Baru'),
                    new OA\Property(property: 'body', type: 'string', example: 'Anda memiliki penugasan baru.'),
                    new OA\Property(property: 'data', type: 'object'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Notification dispatch result'),
            new OA\Response(response: 404, description: 'User not found'),
            new OA\Response(response: 422, description: 'Validation error'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ]
    )]
    public function sendToUser(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'nullable|uuid|exists:users,id',
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'data' => 'nullable|array',
        ]);

        $userId = $validated['user_id'] ?? auth('api')->id();
        $user = User::findOrFail($userId);

        if (empty($user->fcm_token)) {
            return response()->json([
                'success' => false,
                'message' => "User {$user->nama} ({$user->username}) belum memiliki FCM token.",
            ], 422);
        }

        $result = $this->firebaseService->sendToUser(
            $user,
            $validated['title'],
            $validated['body'],
            $validated['data'] ?? []
        );

        return response()->json($result);
    }

    #[OA\Post(
        path: '/api/notifications/send-to-all',
        summary: 'Send push notification broadcast to all users with FCM token',
        security: [['bearerAuth' => []]],
        tags: ['Notifications'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['title', 'body'],
                properties: [
                    new OA\Property(property: 'title', type: 'string', example: 'Broadcast Sistem'),
                    new OA\Property(property: 'body', type: 'string', example: 'Pemberitahuan kepada seluruh karyawan.'),
                    new OA\Property(property: 'data', type: 'object'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Broadcast results'),
            new OA\Response(response: 422, description: 'Validation error'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ]
    )]
    public function sendToAll(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'data' => 'nullable|array',
        ]);

        $result = $this->firebaseService->sendToAllUsers(
            $validated['title'],
            $validated['body'],
            $validated['data'] ?? []
        );

        return response()->json($result);
    }
}
