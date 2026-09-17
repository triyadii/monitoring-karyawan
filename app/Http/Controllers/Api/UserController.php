<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use OpenApi\Attributes as OA;

class UserController extends Controller
{
    #[OA\Get(
        path: '/api/users',
        summary: 'Get list of users',
        security: [['bearerAuth' => []]],
        tags: ['Users'],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ]
    )]
    public function index()
    {
        $users = User::with(['role', 'jenisPegawai'])->get();

        return response()->json($users);
    }

    #[OA\Post(
        path: '/api/users',
        summary: 'Create new user',
        security: [['bearerAuth' => []]],
        tags: ['Users'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['username', 'password', 'nama'],
                properties: [
                    new OA\Property(property: 'role_id', type: 'string', format: 'uuid'),
                    new OA\Property(property: 'jenis_pegawai_id', type: 'string', format: 'uuid'),
                    new OA\Property(property: 'username', type: 'string'),
                    new OA\Property(property: 'password', type: 'string'),
                    new OA\Property(property: 'nama', type: 'string'),
                    new OA\Property(property: 'status', type: 'integer', default: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'User created'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'role_id' => 'nullable|uuid|exists:roles,id',
            'jenis_pegawai_id' => 'nullable|uuid|exists:jenis_pegawais,uuid',
            'username' => 'required|unique:users',
            'password' => 'required|min:6',
            'nama' => 'required',
            'status' => 'integer|in:0,1',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['status'] = $validated['status'] ?? 1;

        $user = User::create($validated);

        return response()->json($user, 201);
    }

    #[OA\Get(
        path: '/api/users/{id}',
        summary: 'Get user details',
        security: [['bearerAuth' => []]],
        tags: ['Users'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
            new OA\Response(response: 404, description: 'User not found'),
        ]
    )]
    public function show($id)
    {
        $user = User::findOrFail($id);

        return response()->json($user);
    }

    #[OA\Put(
        path: '/api/users/{id}',
        summary: 'Update user',
        security: [['bearerAuth' => []]],
        tags: ['Users'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'role_id', type: 'string', format: 'uuid'),
                    new OA\Property(property: 'jenis_pegawai_id', type: 'string', format: 'uuid'),
                    new OA\Property(property: 'username', type: 'string'),
                    new OA\Property(property: 'password', type: 'string'),
                    new OA\Property(property: 'nama', type: 'string'),
                    new OA\Property(property: 'status', type: 'integer', enum: [0, 1]),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'User updated'),
            new OA\Response(response: 404, description: 'User not found'),
        ]
    )]
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'role_id' => 'nullable|uuid|exists:roles,id',
            'jenis_pegawai_id' => 'nullable|uuid|exists:jenis_pegawais,uuid',
            'username' => 'unique:users,username,'.$user->id,
            'password' => 'nullable|min:6',
            'nama' => 'string',
            'status' => 'integer|in:0,1',
        ]);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json($user);
    }

    #[OA\Delete(
        path: '/api/users/{id}',
        summary: 'Delete user',
        security: [['bearerAuth' => []]],
        tags: ['Users'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'User deleted'),
            new OA\Response(response: 404, description: 'User not found'),
        ]
    )]
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }

    #[OA\Patch(
        path: '/api/users/{id}/password',
        summary: 'Update user password',
        security: [['bearerAuth' => []]],
        tags: ['Users'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['password', 'password_confirmation'],
                properties: [
                    new OA\Property(property: 'password', type: 'string'),
                    new OA\Property(property: 'password_confirmation', type: 'string'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Password updated'),
            new OA\Response(response: 422, description: 'Validation error'),
            new OA\Response(response: 404, description: 'User not found'),
        ]
    )]
    public function updatePassword(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'password' => 'required|min:6|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return response()->json(['message' => 'Password updated successfully']);
    }

    #[OA\Post(
        path: '/api/users/{id}/reset-password',
        summary: 'Reset user password to a random string',
        security: [['bearerAuth' => []]],
        tags: ['Users'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Password reset successful'),
            new OA\Response(response: 404, description: 'User not found'),
        ]
    )]
    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        
        $newPassword = \Illuminate\Support\Str::random(8);

        $user->update([
            'password' => Hash::make($newPassword),
        ]);

        return response()->json([
            'message' => 'Password reset successfully',
            'new_password' => $newPassword
        ]);
    }
}
