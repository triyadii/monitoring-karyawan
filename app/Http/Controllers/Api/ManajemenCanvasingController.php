<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ManajemenCanvasing;
use OpenApi\Attributes as OA;

class ManajemenCanvasingController extends Controller
{
    #[OA\Get(
        path: '/api/manajemen-canvasing',
        summary: 'Get list of manajemen canvasing',
        security: [['bearerAuth' => []]],
        tags: ['Manajemen Canvasing'],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ]
    )]
    public function index()
    {
        $canvasings = ManajemenCanvasing::with(['user', 'status'])->get();
        return response()->json($canvasings);
    }

    #[OA\Post(
        path: '/api/manajemen-canvasing',
        summary: 'Create new canvasing',
        security: [['bearerAuth' => []]],
        tags: ['Manajemen Canvasing'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    required: ['namaClient', 'nomorTelepon', 'alamat', 'status_id'],
                    properties: [
                        new OA\Property(property: 'namaClient', type: 'string'),
                        new OA\Property(property: 'nomorTelepon', type: 'string'),
                        new OA\Property(property: 'alamat', type: 'string'),
                        new OA\Property(property: 'status_id', type: 'string', description: 'UUID of Master Status Client'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Canvasing created'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'namaClient' => 'required|string|max:255',
            'nomorTelepon' => 'required|string|max:50',
            'alamat' => 'required|string',
            'status_id' => 'required|uuid|exists:master_status_clients,id',
        ]);

        if ($request->user()) {
            $validated['user_id'] = $request->user()->id;
        }

        $canvasing = ManajemenCanvasing::create($validated);

        return response()->json($canvasing->load(['user', 'status']), 201);
    }

    #[OA\Get(
        path: '/api/manajemen-canvasing/{uuid}',
        summary: 'Get canvasing details',
        security: [['bearerAuth' => []]],
        tags: ['Manajemen Canvasing'],
        parameters: [
            new OA\Parameter(name: 'uuid', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
            new OA\Response(response: 404, description: 'Canvasing not found'),
        ]
    )]
    public function show($uuid)
    {
        $canvasing = ManajemenCanvasing::with(['user', 'status'])->findOrFail($uuid);
        return response()->json($canvasing);
    }

    #[OA\Put(
        path: '/api/manajemen-canvasing/{uuid}',
        summary: 'Update canvasing',
        security: [['bearerAuth' => []]],
        tags: ['Manajemen Canvasing'],
        parameters: [
            new OA\Parameter(name: 'uuid', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'application/json',
                schema: new OA\Schema(
                    required: ['namaClient', 'nomorTelepon', 'alamat', 'status_id'],
                    properties: [
                        new OA\Property(property: 'namaClient', type: 'string'),
                        new OA\Property(property: 'nomorTelepon', type: 'string'),
                        new OA\Property(property: 'alamat', type: 'string'),
                        new OA\Property(property: 'status_id', type: 'string'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Canvasing updated'),
            new OA\Response(response: 404, description: 'Canvasing not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function update(Request $request, $uuid)
    {
        $canvasing = ManajemenCanvasing::findOrFail($uuid);

        $validated = $request->validate([
            'namaClient' => 'required|string|max:255',
            'nomorTelepon' => 'required|string|max:50',
            'alamat' => 'required|string',
            'status_id' => 'required|uuid|exists:master_status_clients,id',
        ]);

        $canvasing->update($validated);

        return response()->json($canvasing->load(['user', 'status']));
    }

    #[OA\Delete(
        path: '/api/manajemen-canvasing/{uuid}',
        summary: 'Delete canvasing',
        security: [['bearerAuth' => []]],
        tags: ['Manajemen Canvasing'],
        parameters: [
            new OA\Parameter(name: 'uuid', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Successful operation'),
            new OA\Response(response: 404, description: 'Canvasing not found'),
        ]
    )]
    public function destroy($uuid)
    {
        $canvasing = ManajemenCanvasing::findOrFail($uuid);
        $canvasing->delete();

        return response()->json(null, 204);
    }
}
