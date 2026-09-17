<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KegiatanAnggota;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use OpenApi\Attributes as OA;

class KegiatanAnggotaController extends Controller
{
    private function applyDateFilter($query, Request $request)
    {
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }
        return $query->orderBy('created_at', 'desc');
    }
    #[OA\Get(
        path: '/api/kegiatan-anggota',
        summary: 'Get list of kegiatan',
        security: [['bearerAuth' => []]],
        tags: ['Kegiatan Anggota'],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ]
    )]
    public function index(Request $request)
    {
        $query = KegiatanAnggota::query();

        $user = $request->user();
        if ($user && $user->role && $user->role->nama_role === 'anggota') {
            $query->where('user_id', $user->id);
        }

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        $this->applyDateFilter($query, $request);
        $kegiatans = $query->with(['user', 'jenisKegiatan'])->get();

        return response()->json($kegiatans);
    }

    #[OA\Get(
        path: '/api/kegiatan-anggota/my-kegiatan',
        summary: 'Get list of kegiatan for authenticated user',
        security: [['bearerAuth' => []]],
        tags: ['Kegiatan Anggota'],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
        ]
    )]
    public function myKegiatan(Request $request)
    {
        $query = KegiatanAnggota::with(['user', 'jenisKegiatan'])
            ->where('user_id', $request->user()->id);
            
        $this->applyDateFilter($query, $request);
        $kegiatans = $query->get();

        return response()->json($kegiatans);
    }

    #[OA\Get(
        path: '/api/kegiatan-anggota/user/{userId}',
        summary: 'Get list of kegiatan by user id',
        security: [['bearerAuth' => []]],
        tags: ['Kegiatan Anggota'],
        parameters: [
            new OA\Parameter(name: 'userId', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ]
    )]
    public function getByUser(Request $request, $userId)
    {
        $query = KegiatanAnggota::with(['user', 'jenisKegiatan'])->where('user_id', $userId);
        
        $this->applyDateFilter($query, $request);
        $kegiatans = $query->get();

        return response()->json($kegiatans);
    }

    #[OA\Post(
        path: '/api/kegiatan-anggota',
        summary: 'Create new kegiatan',
        security: [['bearerAuth' => []]],
        tags: ['Kegiatan Anggota'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    required: ['nama_kegiatan_user', 'tanggal_kegiatan'],
                    properties: [
                        new OA\Property(property: 'user_id', type: 'string', format: 'uuid', description: 'Optional. Assign to specific user, defaults to current user.'),
                        new OA\Property(property: 'nama_kegiatan_user', type: 'string'),
                        new OA\Property(property: 'tanggal_kegiatan', type: 'string', format: 'date-time'),
                        new OA\Property(property: 'foto', type: 'string', format: 'binary', description: 'Image file'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Kegiatan created'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'nullable|uuid|exists:users,id',
            'jenis_kegiatan_id' => 'required|uuid|exists:jenis_kegiatans,id',
            'nama_kegiatan_user' => 'required|string|max:255',
            'tanggal_kegiatan' => 'required|date',
            'keterangan_kegiatan' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if (empty($validated['user_id'])) {
            $validated['user_id'] = Auth::id();
        }

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('kegiatan', 'public');
            $validated['foto'] = 'storage/'.$path;
        }

        $kegiatan = KegiatanAnggota::create($validated);

        return response()->json($kegiatan, 201);
    }

    #[OA\Get(
        path: '/api/kegiatan-anggota/{id}',
        summary: 'Get kegiatan details',
        security: [['bearerAuth' => []]],
        tags: ['Kegiatan Anggota'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function show($id)
    {
        $kegiatan = KegiatanAnggota::with(['user', 'jenisKegiatan'])->findOrFail($id);

        return response()->json($kegiatan);
    }

    #[OA\Post(
        path: '/api/kegiatan-anggota/{id}',
        summary: 'Update kegiatan (Using POST due to multipart/form-data support)',
        security: [['bearerAuth' => []]],
        tags: ['Kegiatan Anggota'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: '_method', type: 'string', example: 'PUT', description: 'Required for Laravel to treat POST as PUT'),
                        new OA\Property(property: 'user_id', type: 'string', format: 'uuid', description: 'Optional. Assign to specific user.'),
                        new OA\Property(property: 'nama_kegiatan_user', type: 'string'),
                        new OA\Property(property: 'tanggal_kegiatan', type: 'string', format: 'date-time'),
                        new OA\Property(property: 'foto', type: 'string', format: 'binary'),
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Kegiatan updated'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function update(Request $request, $id)
    {
        $kegiatan = KegiatanAnggota::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'nullable|uuid|exists:users,id',
            'jenis_kegiatan_id' => 'required|uuid|exists:jenis_kegiatans,id',
            'nama_kegiatan_user' => 'string|max:255',
            'tanggal_kegiatan' => 'date',
            'keterangan_kegiatan' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('foto')) {
            // Delete old photo
            if ($kegiatan->foto) {
                $oldPath = str_replace('storage/', '', $kegiatan->foto);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('foto')->store('kegiatan', 'public');
            $validated['foto'] = 'storage/'.$path;
        }

        $kegiatan->update($validated);

        return response()->json($kegiatan);
    }

    #[OA\Delete(
        path: '/api/kegiatan-anggota/{id}',
        summary: 'Delete kegiatan',
        security: [['bearerAuth' => []]],
        tags: ['Kegiatan Anggota'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
        ],
        responses: [
            new OA\Response(response: 200, description: 'Kegiatan deleted'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function destroy($id)
    {
        $kegiatan = KegiatanAnggota::findOrFail($id);

        if ($kegiatan->foto) {
            $oldPath = str_replace('storage/', '', $kegiatan->foto);
            Storage::disk('public')->delete($oldPath);
        }

        $kegiatan->delete();

        return response()->json(['message' => 'Kegiatan deleted successfully']);
    }
}
