<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    #[OA\Post(
        path: '/api/login',
        summary: 'Login user and get JWT token',
        tags: ['Authentication'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['username', 'password'],
                properties: [
                    new OA\Property(property: 'username', type: 'string', example: 'admin'),
                    new OA\Property(property: 'password', type: 'string', example: 'password'),
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
                            new OA\Property(property: 'role_id', type: 'string', format: 'uuid'),
                            new OA\Property(property: 'username', type: 'string'),
                            new OA\Property(property: 'nama', type: 'string'),
                            new OA\Property(property: 'status', type: 'integer'),
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
        ]
    )]
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
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
            ],
            'authorization' => [
                'token' => $token,
                'type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60,
            ],
        ]);
    }
}
