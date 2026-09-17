<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class RoleController extends Controller
{
    #[OA\Get(
        path: '/api/roles',
        summary: 'Get list of master roles',
        security: [['bearerAuth' => []]],
        tags: ['Roles'],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ]
    )]
    public function index()
    {
        $roles = Role::all();

        return response()->json($roles);
    }

    #[OA\Post(
        path: '/api/roles',
        summary: 'Create new role',
        security: [['bearerAuth' => []]],
        tags: ['Roles'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nama_role'],
                properties: [
                    new OA\Property(property: 'nama_role', type: 'string', example: 'admin'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Role created'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_role' => 'required|string|unique:roles,nama_role',
        ]);

        $role = Role::create($validated);

        return response()->json($role, 201);
    }

    #[OA\Get(
        path: '/api/roles/{id}',
        summary: 'Get role details',
        security: [['bearerAuth' => []]],
        tags: ['Roles'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
            new OA\Response(response: 404, description: 'Role not found'),
        ]
    )]
    public function show($id)
    {
        $role = Role::findOrFail($id);

        return response()->json($role);
    }

    #[OA\Put(
        path: '/api/roles/{id}',
        summary: 'Update role',
        security: [['bearerAuth' => []]],
        tags: ['Roles'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nama_role'],
                properties: [
                    new OA\Property(property: 'nama_role', type: 'string'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Role updated'),
            new OA\Response(response: 404, description: 'Role not found'),
        ]
    )]
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'nama_role' => 'required|string|unique:roles,nama_role,'.$role->id,
        ]);

        $role->update($validated);

        return response()->json($role);
    }

    #[OA\Delete(
        path: '/api/roles/{id}',
        summary: 'Delete role',
        security: [['bearerAuth' => []]],
        tags: ['Roles'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Role deleted'),
            new OA\Response(response: 404, description: 'Role not found'),
        ]
    )]
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return response()->json(['message' => 'Role deleted successfully']);
    }
}
