<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ManajemenAksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ManajemenAksiExport;
use OpenApi\Attributes as OA;

class ManajemenAksiController extends Controller
{
    #[OA\Get(
        path: '/api/manajemen-aksi',
        summary: 'Get list of manajemen aksi',
        security: [['bearerAuth' => []]],
        tags: ['Manajemen Aksi'],
        parameters: [
            new OA\Parameter(name: 'user_id', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'filter_nama', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'filter_tanggal', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'date'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ]
    )]
    public function index(Request $request)
    {
        $query = ManajemenAksi::with('user');
        
        if ($request->has('user_id') && !empty($request->query('user_id'))) {
            $query->where('user_id', $request->query('user_id'));
        }

        if ($request->has('filter_nama') && !empty($request->query('filter_nama'))) {
            $query->where('namaAksi', 'like', '%' . $request->query('filter_nama') . '%');
        }

        if ($request->has('filter_tanggal') && !empty($request->query('filter_tanggal'))) {
            $query->whereDate('created_at', $request->query('filter_tanggal'));
        }

        $aksis = $query->get();
        return response()->json($aksis);
    }

    #[OA\Post(
        path: '/api/manajemen-aksi',
        summary: 'Create new aksi',
        security: [['bearerAuth' => []]],
        tags: ['Manajemen Aksi'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['namaAksi'],
                    properties: [
                        new OA\Property(property: 'idAksi', type: 'string', description: 'ID Aksi (Manual Input)'),
                        new OA\Property(property: 'namaAksi', type: 'string'),
                        new OA\Property(property: 'kegiatan', type: 'string'),
                        new OA\Property(property: 'status', type: 'integer', default: 1),
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
            new OA\Response(response: 201, description: 'Aksi created'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'idAksi' => 'nullable|string|max:255',
            'namaAksi' => 'required|string|max:255',
            'kegiatan' => 'nullable|string',
            'status' => 'integer|in:0,1',
            'foto' => 'nullable|array|max:3',
            'foto.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $validated['status'] = $validated['status'] ?? 1;
        
        if ($request->user()) {
            $validated['user_id'] = $request->user()->id;
        }

        if ($request->hasFile('foto')) {
            $paths = [];
            foreach ($request->file('foto') as $file) {
                $path = $file->store('aksi', 'public');
                $paths[] = 'storage/' . $path;
            }
            $validated['foto'] = $paths;
        }

        $aksi = ManajemenAksi::create($validated);

        return response()->json($aksi, 201);
    }

    #[OA\Get(
        path: '/api/manajemen-aksi/{uuid}',
        summary: 'Get aksi details',
        security: [['bearerAuth' => []]],
        tags: ['Manajemen Aksi'],
        parameters: [
            new OA\Parameter(name: 'uuid', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
            new OA\Response(response: 404, description: 'Aksi not found'),
        ]
    )]
    public function show($uuid)
    {
        $aksi = ManajemenAksi::with('user')->findOrFail($uuid);
        return response()->json($aksi);
    }

    #[OA\Post(
        path: '/api/manajemen-aksi/{uuid}',
        summary: 'Update aksi (using POST with _method=PUT for multipart/form-data)',
        security: [['bearerAuth' => []]],
        tags: ['Manajemen Aksi'],
        parameters: [
            new OA\Parameter(name: 'uuid', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: '_method', type: 'string', example: 'PUT', description: 'Method spoofing for PUT request'),
                        new OA\Property(property: 'idAksi', type: 'string'),
                        new OA\Property(property: 'namaAksi', type: 'string'),
                        new OA\Property(property: 'kegiatan', type: 'string'),
                        new OA\Property(property: 'status', type: 'integer', enum: [0, 1]),
                        new OA\Property(
                            property: 'foto[]', 
                            type: 'array',
                            items: new OA\Items(type: 'string', format: 'binary'),
                            description: 'Upload new images to replace old ones. Max 3 images (jpeg, png, jpg)'
                        ),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Aksi updated'),
            new OA\Response(response: 404, description: 'Aksi not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function update(Request $request, $uuid)
    {
        $aksi = ManajemenAksi::findOrFail($uuid);

        $validated = $request->validate([
            'idAksi' => 'nullable|string|max:255',
            'namaAksi' => 'string|max:255',
            'kegiatan' => 'nullable|string',
            'status' => 'integer|in:0,1',
            'foto' => 'nullable|array|max:3',
            'foto.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if (!empty($aksi->foto) && is_array($aksi->foto)) {
                foreach ($aksi->foto as $oldFoto) {
                    $relativePath = str_replace('storage/', '', $oldFoto);
                    Storage::disk('public')->delete($relativePath);
                }
            }

            $paths = [];
            foreach ($request->file('foto') as $file) {
                $path = $file->store('aksi', 'public');
                $paths[] = 'storage/' . $path;
            }
            $validated['foto'] = $paths;
        } else {
            unset($validated['foto']);
        }

        $aksi->update($validated);

        return response()->json($aksi);
    }

    #[OA\Delete(
        path: '/api/manajemen-aksi/{uuid}',
        summary: 'Delete aksi',
        security: [['bearerAuth' => []]],
        tags: ['Manajemen Aksi'],
        parameters: [
            new OA\Parameter(name: 'uuid', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Aksi deleted'),
            new OA\Response(response: 404, description: 'Aksi not found'),
        ]
    )]
    public function destroy($uuid)
    {
        $aksi = ManajemenAksi::findOrFail($uuid);
        
        // Hapus file foto terkait
        if (!empty($aksi->foto) && is_array($aksi->foto)) {
            foreach ($aksi->foto as $oldFoto) {
                $relativePath = str_replace('storage/', '', $oldFoto);
                Storage::disk('public')->delete($relativePath);
            }
        }

        $aksi->delete();

        return response()->json(['message' => 'Aksi deleted successfully']);
    }

    #[OA\Get(
        path: '/api/export/manajemen-aksi',
        summary: 'Export Manajemen Aksi to Excel',
        security: [['bearerAuth' => []]],
        tags: ['Manajemen Aksi'],
        parameters: [
            new OA\Parameter(name: 'user_id', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'filter_nama', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'filter_tanggal', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'date'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'File downloaded successfully')
        ]
    )]
    public function export(Request $request)
    {
        return Excel::download(new ManajemenAksiExport($request->all()), 'manajemen_aksi.xlsx');
    }
}
