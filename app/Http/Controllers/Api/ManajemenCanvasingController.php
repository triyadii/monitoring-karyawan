<?php

namespace App\Http\Controllers\Api;

use App\Exports\ManajemenCanvasingExport;
use App\Http\Controllers\Controller;
use App\Models\ManajemenCanvasing;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use OpenApi\Attributes as OA;

class ManajemenCanvasingController extends Controller
{
    #[OA\Get(
        path: '/api/manajemen-canvasing',
        summary: 'Get list of manajemen canvasing',
        security: [['bearerAuth' => []]],
        tags: ['Manajemen Canvasing'],
        parameters: [
            new OA\Parameter(name: 'user_id', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'filter_nama', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'filter_tanggal', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'date')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ]
    )]
    public function index(Request $request)
    {
        $query = ManajemenCanvasing::with(['user', 'status']);

        if ($request->has('user_id') && ! empty($request->query('user_id'))) {
            $query->where('user_id', $request->query('user_id'));
        }

        if ($request->has('filter_nama') && ! empty($request->query('filter_nama'))) {
            $query->where('namaClient', 'like', '%'.$request->query('filter_nama').'%');
        }

        if ($request->has('filter_tanggal') && ! empty($request->query('filter_tanggal'))) {
            $query->whereDate('created_at', $request->query('filter_tanggal'));
        }

        $canvasings = $query->get();

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
                        new OA\Property(
                            property: 'foto[]',
                            type: 'array',
                            items: new OA\Items(type: 'string', format: 'binary'),
                            description: 'Max 3 images (jpeg, png, jpg)'
                        ),
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
            'foto' => 'nullable|array|max:3',
            'foto.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($request->hasFile('foto')) {
            $fotoPaths = [];
            foreach ($request->file('foto') as $file) {
                $fotoPaths[] = $file->store('canvasing', 'public');
            }
            $validated['foto'] = $fotoPaths;
        }

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

    #[OA\Post(
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
                        new OA\Property(
                            property: 'foto[]',
                            type: 'array',
                            items: new OA\Items(type: 'string', format: 'binary'),
                            description: 'Max 3 images (jpeg, png, jpg)'
                        ),
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
            'foto' => 'nullable|array|max:3',
            'foto.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($request->hasFile('foto')) {
            // Delete old photos
            if ($canvasing->foto && is_array($canvasing->foto)) {
                foreach ($canvasing->foto as $oldFoto) {
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($oldFoto)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($oldFoto);
                    }
                }
            } elseif ($canvasing->foto && is_string($canvasing->foto)) {
                if (\Illuminate\Support\Facades\Storage::disk('public')->exists($canvasing->foto)) {
                    \Illuminate\Support\Facades\Storage::disk('public')->delete($canvasing->foto);
                }
            }

            $fotoPaths = [];
            foreach ($request->file('foto') as $file) {
                $fotoPaths[] = $file->store('canvasing', 'public');
            }
            $validated['foto'] = $fotoPaths;
        }

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

    #[OA\Get(
        path: '/api/export/manajemen-canvasing',
        summary: 'Export Manajemen Canvasing to Excel',
        security: [['bearerAuth' => []]],
        tags: ['Manajemen Canvasing'],
        parameters: [
            new OA\Parameter(name: 'user_id', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'filter_nama', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'filter_tanggal', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'date')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'File downloaded successfully'),
        ]
    )]
    public function export(Request $request)
    {
        return Excel::download(new ManajemenCanvasingExport($request->all()), 'manajemen_canvasing.xlsx');
    }

    #[OA\Get(
        path: '/api/manajemen-canvasing/user/{userId}',
        summary: 'Get list of ManajemenCanvasing by user id',
        security: [['bearerAuth' => []]],
        tags: ['Manajemen Canvasing'],
        parameters: [
            new OA\Parameter(name: 'userId', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
            new OA\Parameter(name: 'filter_nama', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'filter_tanggal', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'date')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ]
    )]
    public function getByUser(Request $request, $userId)
    {
        $query = ManajemenCanvasing::with(['user', 'status'])->where('user_id', $userId);

        if ($request->has('filter_nama') && ! empty($request->query('filter_nama'))) {
            $query->where('namaClient', 'like', '%'.$request->query('filter_nama').'%');
        }

        if ($request->has('filter_tanggal') && ! empty($request->query('filter_tanggal'))) {
            $query->whereDate('created_at', $request->query('filter_tanggal'));
        }

        $data = $query->get();

        return response()->json($data);
    }
}
