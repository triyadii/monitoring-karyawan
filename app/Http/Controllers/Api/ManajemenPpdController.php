<?php

namespace App\Http\Controllers\Api;

use App\Exports\ManajemenPpdExport;
use App\Http\Controllers\Controller;
use App\Models\ManajemenPpd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use OpenApi\Attributes as OA;

class ManajemenPpdController extends Controller
{
    #[OA\Get(
        path: '/api/manajemen-ppd',
        summary: 'Get list of manajemen PPD',
        security: [['bearerAuth' => []]],
        tags: ['Manajemen PPD'],
        parameters: [
            new OA\Parameter(name: 'user_id', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'search', in: 'query', required: false, description: 'Pencarian berdasarkan nama client', schema: new OA\Schema(type: 'string')),
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
        $query = ManajemenPpd::with('user');

        if ($request->has('user_id') && ! empty($request->query('user_id'))) {
            $query->where('user_id', $request->query('user_id'));
        }

        if ($request->has('search') && ! empty($request->query('search'))) {
            $query->where('namaClient', 'like', '%'.$request->query('search').'%');
        }

        if ($request->has('filter_nama') && ! empty($request->query('filter_nama'))) {
            $query->where('namaClient', 'like', '%'.$request->query('filter_nama').'%');
        }

        if ($request->has('filter_tanggal') && ! empty($request->query('filter_tanggal'))) {
            $query->whereDate('created_at', $request->query('filter_tanggal'));
        }

        $ppds = $query->get();

        return response()->json($ppds);
    }

    #[OA\Post(
        path: '/api/manajemen-ppd',
        summary: 'Create new PPD',
        security: [['bearerAuth' => []]],
        tags: ['Manajemen PPD'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['namaClient', 'nomorTelepon', 'alamat'],
                    properties: [
                        new OA\Property(property: 'namaClient', type: 'string'),
                        new OA\Property(property: 'nomorTelepon', type: 'string'),
                        new OA\Property(property: 'alamat', type: 'string'),
                        new OA\Property(property: 'nomorKontrak', type: 'string'),
                        new OA\Property(property: 'tenor', type: 'string'),
                        new OA\Property(property: 'angsuran', type: 'string'),
                        new OA\Property(property: 'merk', type: 'string'),
                        new OA\Property(property: 'type', type: 'string'),
                        new OA\Property(property: 'jenisKendaraan', type: 'string'),
                        new OA\Property(property: 'pinjaman', type: 'string'),
                        new OA\Property(property: 'jatuhTempo', type: 'string', format: 'date'),
                        new OA\Property(
                            property: 'berkas[]',
                            type: 'array',
                            items: new OA\Items(type: 'string', format: 'binary'),
                            description: 'Upload berkas/dokumen (jpeg, png, jpg, pdf). Maksimal 3 file.'
                        ),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'PPD created'),
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
            'nomorKontrak' => 'nullable|string',
            'tenor' => 'nullable|string',
            'angsuran' => 'nullable|string',
            'merk' => 'nullable|string',
            'type' => 'nullable|string',
            'jenisKendaraan' => 'nullable|string',
            'pinjaman' => 'nullable|string',
            'jatuhTempo' => 'nullable|date',
            'berkas' => 'nullable|array|max:3',
            'berkas.*' => 'file|mimes:jpeg,png,jpg,gif,pdf|max:5120',
        ]);

        if ($request->user()) {
            $validated['user_id'] = $request->user()->id;
        }

        if ($request->hasFile('berkas')) {
            $paths = [];
            foreach ($request->file('berkas') as $file) {
                $path = $file->store('ppd', 'public');
                $paths[] = 'storage/'.$path;
            }
            $validated['berkas'] = $paths;
        }

        $ppd = ManajemenPpd::create($validated);

        return response()->json($ppd->load(['user']), 201);
    }

    #[OA\Get(
        path: '/api/manajemen-ppd/{uuid}',
        summary: 'Get PPD details',
        security: [['bearerAuth' => []]],
        tags: ['Manajemen PPD'],
        parameters: [
            new OA\Parameter(name: 'uuid', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
            new OA\Response(response: 404, description: 'PPD not found'),
        ]
    )]
    public function show($uuid)
    {
        $ppd = ManajemenPpd::with(['user'])->findOrFail($uuid);

        return response()->json($ppd);
    }

    #[OA\Post(
        path: '/api/manajemen-ppd/{uuid}',
        summary: 'Update PPD with file upload',
        security: [['bearerAuth' => []]],
        tags: ['Manajemen PPD'],
        parameters: [
            new OA\Parameter(name: 'uuid', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['namaClient', 'nomorTelepon', 'alamat'],
                    properties: [
                        new OA\Property(property: '_method', type: 'string', example: 'PUT', description: 'Method spoofing for PUT request'),
                        new OA\Property(property: 'namaClient', type: 'string'),
                        new OA\Property(property: 'nomorTelepon', type: 'string'),
                        new OA\Property(property: 'alamat', type: 'string'),
                        new OA\Property(property: 'nomorKontrak', type: 'string'),
                        new OA\Property(property: 'tenor', type: 'string'),
                        new OA\Property(property: 'angsuran', type: 'string'),
                        new OA\Property(property: 'merk', type: 'string'),
                        new OA\Property(property: 'type', type: 'string'),
                        new OA\Property(property: 'jenisKendaraan', type: 'string'),
                        new OA\Property(property: 'pinjaman', type: 'string'),
                        new OA\Property(property: 'jatuhTempo', type: 'string', format: 'date'),
                        new OA\Property(
                            property: 'berkas[]',
                            type: 'array',
                            items: new OA\Items(type: 'string', format: 'binary'),
                            description: 'Upload berkas/dokumen baru untuk menggantikan yang lama (jpeg, png, jpg, pdf). Maksimal 3 file.'
                        ),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'PPD updated'),
            new OA\Response(response: 404, description: 'PPD not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function update(Request $request, $uuid)
    {
        $ppd = ManajemenPpd::findOrFail($uuid);

        $validated = $request->validate([
            'namaClient' => 'required|string|max:255',
            'nomorTelepon' => 'required|string|max:255',
            'alamat' => 'required|string',
            'nomorKontrak' => 'nullable|string',
            'tenor' => 'nullable|string',
            'angsuran' => 'nullable|string',
            'merk' => 'nullable|string',
            'type' => 'nullable|string',
            'jenisKendaraan' => 'nullable|string',
            'pinjaman' => 'nullable|string',
            'jatuhTempo' => 'nullable|date',
            'berkas' => 'nullable|array|max:3',
            'berkas.*' => 'file|mimes:jpeg,png,jpg,gif,pdf|max:5120',
        ]);

        if ($request->hasFile('berkas')) {
            // Hapus file lama jika ada
            if (! empty($ppd->berkas) && is_array($ppd->berkas)) {
                foreach ($ppd->berkas as $oldFile) {
                    $relativePath = str_replace('storage/', '', $oldFile);
                    if (Storage::disk('public')->exists($relativePath)) {
                        Storage::disk('public')->delete($relativePath);
                    }
                }
            }

            $paths = [];
            foreach ($request->file('berkas') as $file) {
                $path = $file->store('ppd', 'public');
                $paths[] = 'storage/'.$path;
            }
            $validated['berkas'] = $paths;
        } else {
            unset($validated['berkas']);
        }

        $ppd->update($validated);

        return response()->json($ppd->load(['user']));
    }

    #[OA\Delete(
        path: '/api/manajemen-ppd/{uuid}',
        summary: 'Delete PPD',
        security: [['bearerAuth' => []]],
        tags: ['Manajemen PPD'],
        parameters: [
            new OA\Parameter(name: 'uuid', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Successful operation'),
            new OA\Response(response: 404, description: 'PPD not found'),
        ]
    )]
    public function destroy($uuid)
    {
        $ppd = ManajemenPpd::findOrFail($uuid);

        if (! empty($ppd->berkas) && is_array($ppd->berkas)) {
            foreach ($ppd->berkas as $oldFile) {
                $relativePath = str_replace('storage/', '', $oldFile);
                if (Storage::disk('public')->exists($relativePath)) {
                    Storage::disk('public')->delete($relativePath);
                }
            }
        }

        $ppd->delete();

        return response()->json(null, 204);
    }

    #[OA\Get(
        path: '/api/export/manajemen-ppd',
        summary: 'Export Manajemen PPD to Excel',
        security: [['bearerAuth' => []]],
        tags: ['Manajemen PPD'],
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
        return Excel::download(new ManajemenPpdExport($request->all()), 'manajemen_ppd.xlsx');
    }

    #[OA\Get(
        path: '/api/manajemen-ppd/user/{userId}',
        summary: 'Get list of ManajemenPpd by user id',
        security: [['bearerAuth' => []]],
        tags: ['Manajemen PPD'],
        parameters: [
            new OA\Parameter(name: 'userId', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
            new OA\Parameter(name: 'search', in: 'query', required: false, description: 'Pencarian berdasarkan nama client', schema: new OA\Schema(type: 'string')),
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
        $query = ManajemenPpd::with('user')->where('user_id', $userId);

        if ($request->has('search') && ! empty($request->query('search'))) {
            $query->where('namaClient', 'like', '%'.$request->query('search').'%');
        }

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
