<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ManajemenHo;
use OpenApi\Attributes as OA;
use Carbon\Carbon;

class ManajemenHoController extends Controller
{
    #[OA\Get(
        path: '/api/manajemen-ho',
        summary: 'Get list of manajemen ho created today',
        security: [['bearerAuth' => []]],
        tags: ['Manajemen HO'],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ]
    )]
    public function index(Request $request)
    {
        $query = ManajemenHo::with(['user', 'status']);
        
        $user = $request->user();
        if ($user && $user->role) {
            $roleName = strtolower($user->role->nama_role);
            // Jika bukan superadmin dan bukan leader, terapkan filter tanggal
            if (!in_array($roleName, ['superadmin', 'leader'])) {
                $query->whereDate('created_at', Carbon::today());
            }
        } else {
            // Fallback jika tidak ada user/role terdeteksi, filter aktif
            $query->whereDate('created_at', Carbon::today());
        }
        
        $hos = $query->get();
        return response()->json($hos);
    }

    #[OA\Post(
        path: '/api/manajemen-ho',
        summary: 'Create new HO',
        security: [['bearerAuth' => []]],
        tags: ['Manajemen HO'],
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
            new OA\Response(response: 201, description: 'HO created'),
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
        ]);

        if ($request->user()) {
            $validated['user_id'] = $request->user()->id;
        }

        $ho = ManajemenHo::create($validated);

        return response()->json($ho->load(['user', 'status']), 201);
    }

    #[OA\Get(
        path: '/api/manajemen-ho/{uuid}',
        summary: 'Get HO details',
        security: [['bearerAuth' => []]],
        tags: ['Manajemen HO'],
        parameters: [
            new OA\Parameter(name: 'uuid', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
            new OA\Response(response: 404, description: 'HO not found'),
        ]
    )]
    public function show($uuid)
    {
        $ho = ManajemenHo::with(['user', 'status'])->findOrFail($uuid);
        return response()->json($ho);
    }

    #[OA\Put(
        path: '/api/manajemen-ho/{uuid}',
        summary: 'Update HO',
        security: [['bearerAuth' => []]],
        tags: ['Manajemen HO'],
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
            new OA\Response(response: 200, description: 'HO updated'),
            new OA\Response(response: 404, description: 'HO not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function update(Request $request, $uuid)
    {
        $ho = ManajemenHo::findOrFail($uuid);

        $validated = $request->validate([
            'namaClient' => 'required|string|max:255',
            'nomorTelepon' => 'required|string|max:255',
            'alamat' => 'required|string',
            'status_id' => 'required|uuid|exists:master_status_clients,id',
        ]);

        $ho->update($validated);

        return response()->json($ho->load(['user', 'status']));
    }

    #[OA\Delete(
        path: '/api/manajemen-ho/{uuid}',
        summary: 'Delete HO',
        security: [['bearerAuth' => []]],
        tags: ['Manajemen HO'],
        parameters: [
            new OA\Parameter(name: 'uuid', in: 'path', required: true, schema: new OA\Schema(type: 'string')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Successful operation'),
            new OA\Response(response: 404, description: 'HO not found'),
        ]
    )]
    public function destroy($uuid)
    {
        $ho = ManajemenHo::findOrFail($uuid);
        $ho->delete();

        return response()->json(null, 204);
    }
}
