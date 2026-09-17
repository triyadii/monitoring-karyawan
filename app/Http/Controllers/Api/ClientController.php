<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Imports\ClientsImport;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use OpenApi\Attributes as OA;

class ClientController extends Controller
{
    #[OA\Get(
        path: '/api/clients',
        summary: 'Get list of clients',
        security: [['bearerAuth' => []]],
        tags: ['Clients'],
        parameters: [
            new OA\Parameter(name: 'user_id', in: 'query', required: false, description: 'Filter by user ID', schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ]
    )]
    public function index(Request $request)
    {
        $query = Client::query();

        $user = $request->user();
        if ($user && $user->role && $user->role->nama_role === 'anggota') {
            $query->where('user_id', $user->id);
        }

        if ($request->has('user_id') && $request->user_id != '') {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('status_client') && $request->status_client != '') {
            $query->where('status_client', $request->status_client);
        }

        $clients = $query->with(['user', 'penginput', 'status'])->orderBy('created_at', 'desc')->get();

        return response()->json($clients);
    }

    #[OA\Post(
        path: '/api/clients',
        summary: 'Create new client',
        security: [['bearerAuth' => []]],
        tags: ['Clients'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['nama', 'sumber_data'],
                properties: [
                    new OA\Property(property: 'nama', type: 'string'),
                    new OA\Property(property: 'alamat', type: 'string'),
                    new OA\Property(property: 'kelurahan', type: 'string'),
                    new OA\Property(property: 'kecamatan', type: 'string'),
                    new OA\Property(property: 'kabupaten', type: 'string'),
                    new OA\Property(property: 'nomor_telepon', type: 'string'),
                    new OA\Property(property: 'status_client', type: 'string', format: 'uuid'),
                    new OA\Property(property: 'sumber_data', type: 'integer', enum: [1, 2], description: '1: HO, 2: Anggota'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Client created'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'alamat' => 'nullable|string',
            'kelurahan' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kabupaten' => 'nullable|string|max:255',
            'nomor_telepon' => 'nullable|string|max:50',
            'status_client' => 'required|uuid|exists:master_status_clients,id',
            'sumber_data' => 'required|integer|in:1,2',
        ]);
        $validated['idUser'] = auth('api')->id();
        $client = Client::create($validated);

        return response()->json($client, 201);
    }

    #[OA\Get(
        path: '/api/clients/{id}',
        summary: 'Get client details',
        security: [['bearerAuth' => []]],
        tags: ['Clients'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
            new OA\Response(response: 404, description: 'Client not found'),
        ]
    )]
    public function show($id)
    {
        $client = Client::findOrFail($id);

        return response()->json($client);
    }

    #[OA\Put(
        path: '/api/clients/{id}',
        summary: 'Update client',
        security: [['bearerAuth' => []]],
        tags: ['Clients'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'nama', type: 'string'),
                    new OA\Property(property: 'alamat', type: 'string'),
                    new OA\Property(property: 'kelurahan', type: 'string'),
                    new OA\Property(property: 'kecamatan', type: 'string'),
                    new OA\Property(property: 'kabupaten', type: 'string'),
                    new OA\Property(property: 'nomor_telepon', type: 'string'),
                    new OA\Property(property: 'status_client', type: 'string', format: 'uuid'),
                    new OA\Property(property: 'sumber_data', type: 'integer', enum: [1, 2]),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Client updated'),
            new OA\Response(response: 404, description: 'Client not found'),
        ]
    )]
    public function update(Request $request, $id)
    {
        $user = $request->user();
        if ($user && $user->role && $user->role->nama_role === 'anggota') {
            return response()->json(['message' => 'Anda tidak memiliki akses untuk mengubah data client'], 403);
        }

        $client = Client::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'string|max:255',
            'alamat' => 'nullable|string',
            'kelurahan' => 'nullable|string|max:255',
            'kecamatan' => 'nullable|string|max:255',
            'kabupaten' => 'nullable|string|max:255',
            'nomor_telepon' => 'nullable|string|max:50',
            'status_client' => 'required|uuid|exists:master_status_clients,id',
            'sumber_data' => 'integer|in:1,2',
        ]);

        $client->update($validated);

        return response()->json($client);
    }

    public function updateStatus(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        $validated = $request->validate([
            'status_client' => 'required|uuid|exists:master_status_clients,id',
        ]);

        $client->update($validated);
        
        // Return with eager loaded status so frontend can display correctly
        $client->load('status');

        return response()->json($client);
    }

    #[OA\Delete(
        path: '/api/clients/{id}',
        summary: 'Delete client',
        security: [['bearerAuth' => []]],
        tags: ['Clients'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Client deleted'),
            new OA\Response(response: 404, description: 'Client not found'),
        ]
    )]
    public function destroy($id)
    {
        $client = Client::findOrFail($id);
        $client->delete();

        return response()->json(['message' => 'Client deleted successfully']);
    }

    #[OA\Post(
        path: '/api/clients/{id}/disposisi',
        summary: 'Disposisi client ke user tertentu',
        security: [['bearerAuth' => []]],
        tags: ['Clients'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['user_id'],
                properties: [
                    new OA\Property(property: 'user_id', type: 'string', format: 'uuid'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Disposisi berhasil'),
            new OA\Response(response: 404, description: 'Client not found'),
        ]
    )]
    public function disposisi(Request $request, $id)
    {
        $client = Client::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'required|uuid|exists:users,id',
        ]);

        $client->update(['user_id' => $validated['user_id']]);

        return response()->json(['message' => 'Disposisi berhasil', 'client' => $client]);
    }

    #[OA\Post(
        path: '/api/clients/bulk-disposisi',
        summary: 'Disposisi beberapa client ke user tertentu sekaligus',
        security: [['bearerAuth' => []]],
        tags: ['Clients'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['client_ids', 'user_id'],
                properties: [
                    new OA\Property(
                        property: 'client_ids',
                        type: 'array',
                        items: new OA\Items(type: 'string', format: 'uuid')
                    ),
                    new OA\Property(property: 'user_id', type: 'string', format: 'uuid'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Bulk Disposisi berhasil'),
        ]
    )]
    public function bulkDisposisi(Request $request)
    {
        $validated = $request->validate([
            'client_ids' => 'required|array',
            'client_ids.*' => 'uuid|exists:clients,id',
            'user_id' => 'required|uuid|exists:users,id',
        ]);

        Client::whereIn('id', $validated['client_ids'])->update(['user_id' => $validated['user_id']]);

        return response()->json([
            'message' => 'Bulk disposisi berhasil',
            'count' => count($validated['client_ids']),
        ]);
    }

    #[OA\Post(
        path: '/api/clients/import',
        summary: 'Import clients dari file Excel/CSV',
        security: [['bearerAuth' => []]],
        tags: ['Clients'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['file'],
                    properties: [
                        new OA\Property(property: 'file', type: 'string', format: 'binary', description: 'File Excel (.xlsx) atau .csv'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Import berhasil'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,txt|max:10240',
        ]);

        try {
            Excel::import(new ClientsImport, $request->file('file'));

            return response()->json(['message' => 'Data clients berhasil diimport']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Gagal mengimpor data: '.$e->getMessage()], 500);
        }
    }

    #[OA\Get(
        path: '/api/clients/my-disposisi',
        summary: 'Get list of clients disposed to the authenticated user',
        security: [['bearerAuth' => []]],
        tags: ['Clients'],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
        ]
    )]
    public function myDisposisi(Request $request)
    {
        $clients = Client::with(['user', 'penginput'])
            ->where('user_id', $request->user()->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($clients);
    }

    #[OA\Get(
        path: '/api/clients/stats/sumber-data',
        summary: 'Get count of clients by sumber data (HO vs Anggota)',
        security: [['bearerAuth' => []]],
        tags: ['Clients'],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
        ]
    )]
    public function getSumberDataStats()
    {
        $hoCount = Client::where('sumber_data', 1)->count();
        $anggotaCount = Client::where('sumber_data', 2)->count();

        return response()->json([
            'ho' => $hoCount,
            'anggota' => $anggotaCount,
        ]);
    }

    #[OA\Get(
        path: '/api/clients/stats/user',
        summary: 'Get count of clients by input and disposisi per user',
        security: [['bearerAuth' => []]],
        tags: ['Clients'],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
        ]
    )]
    public function getUserStats()
    {
        $inputStats = Client::select('idUser', DB::raw('count(*) as total'))
            ->whereNotNull('idUser')
            ->groupBy('idUser')
            ->pluck('total', 'idUser');

        $disposisiStats = Client::select('user_id', DB::raw('count(*) as total'))
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        $users = User::all()->map(function ($user) use ($inputStats, $disposisiStats) {
            return [
                'user_id' => $user->id,
                'nama' => $user->nama,
                'input_count' => $inputStats[$user->id] ?? 0,
                'disposisi_count' => $disposisiStats[$user->id] ?? 0,
            ];
        });

        return response()->json($users);
    }

    #[OA\Get(
        path: '/api/clients/stats/status',
        summary: 'Get count of clients by status',
        security: [['bearerAuth' => []]],
        tags: ['Clients'],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
        ]
    )]
    public function getStatusStats()
    {
        // Join with master_status_clients to get the names, and group by status_client
        $stats = Client::select('status_client', \Illuminate\Support\Facades\DB::raw('count(*) as total'))
            ->groupBy('status_client')
            ->pluck('total', 'status_client');

        $masterStatuses = \App\Models\MasterStatusClient::all();
        $result = $masterStatuses->map(function ($status) use ($stats) {
            return [
                'status_id' => $status->id,
                'nama_status' => $status->nama_status,
                'total' => $stats[$status->id] ?? 0,
            ];
        });

        // Add 'Belum ada' for null status if necessary
        $nullStats = $stats[''] ?? ($stats[null] ?? 0);
        if ($nullStats > 0) {
            $result->push([
                'status_id' => null,
                'nama_status' => 'Belum ada',
                'total' => $nullStats,
            ]);
        }

        return response()->json($result);
    }
}
