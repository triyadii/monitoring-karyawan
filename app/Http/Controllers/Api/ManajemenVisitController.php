<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ManajemenVisit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ManajemenVisitExport;
use OpenApi\Attributes as OA;

class ManajemenVisitController extends Controller
{
    #[OA\Get(
        path: '/api/manajemen-visit',
        summary: 'Get list of manajemen visit',
        security: [['bearerAuth' => []]],
        tags: ['Manajemen Visit'],
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
        $query = ManajemenVisit::with(['user', 'status']);
        
        if ($request->has('user_id') && !empty($request->query('user_id'))) {
            $query->where('user_id', $request->query('user_id'));
        }

        if ($request->has('filter_nama') && !empty($request->query('filter_nama'))) {
            $query->where('namaClient', 'like', '%' . $request->query('filter_nama') . '%');
        }

        if ($request->has('filter_tanggal') && !empty($request->query('filter_tanggal'))) {
            $query->whereDate('created_at', $request->query('filter_tanggal'));
        }

        $visits = $query->get();
        return response()->json($visits);
    }

    #[OA\Post(
        path: '/api/manajemen-visit',
        summary: 'Create new visit',
        security: [['bearerAuth' => []]],
        tags: ['Manajemen Visit'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['namaClient', 'nomorTelepon', 'alamat', 'status_id'],
                    properties: [
                        new OA\Property(property: 'namaClient', type: 'string'),
                        new OA\Property(property: 'nomorTelepon', type: 'string'),
                        new OA\Property(property: 'alamat', type: 'string'),
                        new OA\Property(property: 'status_id', type: 'string'),
                        new OA\Property(property: 'kegiatan', type: 'string'),
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
            new OA\Response(response: 201, description: 'Visit created'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'namaClient' => 'required|string|max:255',
            'nomorTelepon' => 'required|string|max:255',
            'alamat' => 'required|string',
            'status_id' => 'required|uuid|exists:master_status_clients,id',
            'kegiatan' => 'nullable|string',
            'foto' => 'nullable|array|max:3',
            'foto.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->user()) {
            $validated['user_id'] = $request->user()->id;
        }

        if ($request->hasFile('foto')) {
            $paths = [];
            foreach ($request->file('foto') as $file) {
                $path = $file->store('visit', 'public');
                $paths[] = 'storage/' . $path;
            }
            $validated['foto'] = $paths;
        }

        $visit = ManajemenVisit::create($validated);

        return response()->json($visit->load(['user', 'status']), 201);
    }

    #[OA\Get(
        path: '/api/manajemen-visit/{uuid}',
        summary: 'Get visit details',
        security: [['bearerAuth' => []]],
        tags: ['Manajemen Visit'],
        parameters: [
            new OA\Parameter(name: 'uuid', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
            new OA\Response(response: 404, description: 'Visit not found'),
        ]
    )]
    public function show($uuid)
    {
        $visit = ManajemenVisit::with(['user', 'status'])->findOrFail($uuid);
        return response()->json($visit);
    }

    #[OA\Post(
        path: '/api/manajemen-visit/{uuid}',
        summary: 'Update visit with file upload',
        security: [['bearerAuth' => []]],
        tags: ['Manajemen Visit'],
        parameters: [
            new OA\Parameter(name: 'uuid', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['namaClient', 'nomorTelepon', 'alamat', 'status_id'],
                    properties: [
                        new OA\Property(property: 'namaClient', type: 'string'),
                        new OA\Property(property: 'nomorTelepon', type: 'string'),
                        new OA\Property(property: 'alamat', type: 'string'),
                        new OA\Property(property: 'status_id', type: 'string'),
                        new OA\Property(property: 'kegiatan', type: 'string'),
                        new OA\Property(
                            property: 'foto[]', 
                            type: 'array',
                            items: new OA\Items(type: 'string', format: 'binary'),
                            description: 'Max 3 images (jpeg, png, jpg). Will replace old images.'
                        ),
                        new OA\Property(
                            property: '_method',
                            type: 'string',
                            default: 'PUT',
                            description: 'Spoof method to PUT (optional if handled by controller explicitly)'
                        )
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Visit updated'),
            new OA\Response(response: 404, description: 'Visit not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function update(Request $request, $uuid)
    {
        $visit = ManajemenVisit::findOrFail($uuid);

        $validated = $request->validate([
            'namaClient' => 'required|string|max:255',
            'nomorTelepon' => 'required|string|max:255',
            'alamat' => 'required|string',
            'status_id' => 'required|uuid|exists:master_status_clients,id',
            'kegiatan' => 'nullable|string',
            'foto' => 'nullable|array|max:3',
            'foto.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            // Hapus file lama jika ada
            if ($visit->foto) {
                foreach ($visit->foto as $oldFile) {
                    $relativePath = str_replace('storage/', '', $oldFile);
                    if (Storage::disk('public')->exists($relativePath)) {
                        Storage::disk('public')->delete($relativePath);
                    }
                }
            }

            $paths = [];
            foreach ($request->file('foto') as $file) {
                $path = $file->store('visit', 'public');
                $paths[] = 'storage/' . $path;
            }
            $validated['foto'] = $paths;
        }

        $visit->update($validated);

        return response()->json($visit->load(['user', 'status']));
    }

    #[OA\Delete(
        path: '/api/manajemen-visit/{uuid}',
        summary: 'Delete visit',
        security: [['bearerAuth' => []]],
        tags: ['Manajemen Visit'],
        parameters: [
            new OA\Parameter(name: 'uuid', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Successful operation'),
            new OA\Response(response: 404, description: 'Visit not found'),
        ]
    )]
    public function destroy($uuid)
    {
        $visit = ManajemenVisit::findOrFail($uuid);

        if ($visit->foto) {
            foreach ($visit->foto as $oldFile) {
                $relativePath = str_replace('storage/', '', $oldFile);
                if (Storage::disk('public')->exists($relativePath)) {
                    Storage::disk('public')->delete($relativePath);
                }
            }
        }

        $visit->delete();

        return response()->json(null, 204);
    }

    #[OA\Get(
        path: '/api/export/manajemen-visit',
        summary: 'Export Manajemen Visit to Excel',
        security: [['bearerAuth' => []]],
        tags: ['Manajemen Visit'],
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
        return Excel::download(new ManajemenVisitExport($request->all()), 'manajemen_visit.xlsx');
    }
}
