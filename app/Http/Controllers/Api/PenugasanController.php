<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Penugasan;
use App\Models\ManajemenHo;
use App\Models\ManajemenCanvasing;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;
use Illuminate\Support\Facades\Auth;

#[OA\Tag(
    name: 'Penugasan',
    description: 'API Endpoints for Tugas Kunjungan (Assignments)'
)]
class PenugasanController extends Controller
{
    #[OA\Get(
        path: '/api/penugasans',
        summary: 'Get all penugasans',
        security: [['bearerAuth' => []]],
        tags: ['Penugasan'],
        responses: [
            new OA\Response(response: 200, description: 'List of all penugasans')
        ]
    )]
    public function index(Request $request)
    {
        $query = Penugasan::with(['assignable', 'user', 'assigner', 'status']);

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }
        
        if ($request->has('status_id')) {
            if ($request->status_id === 'pending') {
                $query->whereNull('status_id');
            } else {
                $query->where('status_id', $request->status_id);
            }
        }

        $penugasans = $query->orderBy('created_at', 'desc')->get();

        // Transform assignable data for easier frontend use
        $penugasans->transform(function ($item) {
            $item->sumber_tipe = class_basename($item->assignable_type);
            return $item;
        });

        return response()->json($penugasans);
    }

    #[OA\Post(
        path: '/api/penugasans',
        summary: 'Create a new penugasan (assign task)',
        security: [['bearerAuth' => []]],
        tags: ['Penugasan'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['assignable_type', 'assignable_id', 'user_id'],
                properties: [
                    new OA\Property(property: 'assignable_type', type: 'string', enum: ['HO', 'Canvasing']),
                    new OA\Property(property: 'assignable_id', type: 'string', format: 'uuid'),
                    new OA\Property(property: 'user_id', type: 'string', format: 'uuid'),
                    new OA\Property(property: 'catatan', type: 'string', nullable: true),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Penugasan created successfully'),
            new OA\Response(response: 422, description: 'Validation error'),
            new OA\Response(response: 404, description: 'Assignable record not found')
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'assignable_type' => 'required|string|in:HO,Canvasing',
            'assignable_id' => 'required|uuid',
            'user_id' => 'required|uuid|exists:users,id',
            'catatan' => 'nullable|string'
        ]);

        $modelClass = $validated['assignable_type'] === 'HO' ? ManajemenHo::class : ManajemenCanvasing::class;
        
        // Verify the record exists
        $record = $modelClass::find($validated['assignable_id']);
        if (!$record) {
            return response()->json(['message' => 'Record not found'], 404);
        }

        // Prevent duplicate assignment if it's already pending for the same user
        $exists = Penugasan::where('assignable_type', $modelClass)
            ->where('assignable_id', $validated['assignable_id'])
            ->where('user_id', $validated['user_id'])
            ->whereNull('status_id')
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Tugas sudah di-assign ke pengguna ini dan masih pending'], 422);
        }

        $penugasan = Penugasan::create([
            'assignable_type' => $modelClass,
            'assignable_id' => $validated['assignable_id'],
            'user_id' => $validated['user_id'],
            'assigned_by' => Auth::id(),
            'status_id' => null,
            'catatan' => $validated['catatan'] ?? null,
        ]);

        return response()->json(
            $penugasan->load(['assignable', 'user', 'assigner']), 
            201
        );
    }

    #[OA\Post(
        path: '/api/penugasans/{uuid}/status',
        summary: 'Update status of a penugasan',
        security: [['bearerAuth' => []]],
        tags: ['Penugasan'],
        parameters: [
            new OA\Parameter(name: 'uuid', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: 'multipart/form-data',
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: 'status_id', type: 'string', format: 'uuid', nullable: true),
                        new OA\Property(
                            property: 'foto[]',
                            type: 'array',
                            items: new OA\Items(type: 'string', format: 'binary'),
                            description: 'Max 3 images (jpeg, png, jpg)'
                        )
                    ]
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Status updated successfully'),
            new OA\Response(response: 404, description: 'Penugasan not found')
        ]
    )]
    public function updateStatus(Request $request, $uuid)
    {
        $penugasan = Penugasan::findOrFail($uuid);

        $validated = $request->validate([
            'status_id' => 'nullable|uuid|exists:master_status_clients,id',
            'foto' => 'nullable|array|max:3',
            'foto.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        if ($request->hasFile('foto')) {
            // Delete old photos
            if ($penugasan->foto && is_array($penugasan->foto)) {
                foreach ($penugasan->foto as $oldFoto) {
                    if (\Illuminate\Support\Facades\Storage::disk('public')->exists($oldFoto)) {
                        \Illuminate\Support\Facades\Storage::disk('public')->delete($oldFoto);
                    }
                }
            }

            $fotoPaths = [];
            foreach ($request->file('foto') as $file) {
                $fotoPaths[] = $file->store('penugasan', 'public');
            }
            $validated['foto'] = $fotoPaths;
        }

        $penugasan->update($validated);
        
        // Also update parent client status if not null
        if ($validated['status_id']) {
            if ($penugasan->assignable) {
                $penugasan->assignable->update(['status_id' => $validated['status_id']]);
            }
        }

        return response()->json($penugasan->load(['assignable', 'user', 'status']));
    }

    #[OA\Delete(
        path: '/api/penugasans/{uuid}',
        summary: 'Delete (cancel) a penugasan',
        security: [['bearerAuth' => []]],
        tags: ['Penugasan'],
        parameters: [
            new OA\Parameter(name: 'uuid', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid'))
        ],
        responses: [
            new OA\Response(response: 204, description: 'Penugasan deleted'),
            new OA\Response(response: 404, description: 'Penugasan not found')
        ]
    )]
    public function destroy($uuid)
    {
        $penugasan = Penugasan::findOrFail($uuid);
        $penugasan->delete();

        return response()->json(null, 204);
    }
}
