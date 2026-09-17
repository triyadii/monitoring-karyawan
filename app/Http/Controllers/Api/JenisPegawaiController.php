<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JenisPegawai;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class JenisPegawaiController extends Controller
{
    #[OA\Get(
        path: '/api/jenis-pegawai',
        summary: 'Get list of Jenis Pegawai',
        security: [['bearerAuth' => []]],
        tags: ['Jenis Pegawai'],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ]
    )]
    public function index()
    {
        return response()->json(JenisPegawai::orderBy('jenisPegawai', 'asc')->get());
    }

    #[OA\Post(
        path: '/api/jenis-pegawai',
        summary: 'Create new Jenis Pegawai',
        security: [['bearerAuth' => []]],
        tags: ['Jenis Pegawai'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['jenisPegawai'],
                properties: [
                    new OA\Property(property: 'jenisPegawai', type: 'string', example: 'Tetap'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Jenis Pegawai created'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenisPegawai' => 'required|string|max:255',
        ]);

        $jenisPegawai = JenisPegawai::create($validated);

        return response()->json($jenisPegawai, 201);
    }

    #[OA\Get(
        path: '/api/jenis-pegawai/{jenis_pegawai}',
        summary: 'Get Jenis Pegawai by UUID',
        security: [['bearerAuth' => []]],
        tags: ['Jenis Pegawai'],
        parameters: [
            new OA\Parameter(
                name: 'jenis_pegawai',
                description: 'UUID of Jenis Pegawai',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function show(JenisPegawai $jenisPegawai)
    {
        return response()->json($jenisPegawai);
    }

    #[OA\Put(
        path: '/api/jenis-pegawai/{jenis_pegawai}',
        summary: 'Update Jenis Pegawai',
        security: [['bearerAuth' => []]],
        tags: ['Jenis Pegawai'],
        parameters: [
            new OA\Parameter(
                name: 'jenis_pegawai',
                description: 'UUID of Jenis Pegawai',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string')
            )
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['jenisPegawai'],
                properties: [
                    new OA\Property(property: 'jenisPegawai', type: 'string', example: 'Kontrak'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Jenis Pegawai updated'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Not found'),
            new OA\Response(response: 422, description: 'Validation error'),
        ]
    )]
    public function update(Request $request, JenisPegawai $jenisPegawai)
    {
        $validated = $request->validate([
            'jenisPegawai' => 'required|string|max:255',
        ]);

        $jenisPegawai->update($validated);

        return response()->json($jenisPegawai);
    }

    #[OA\Delete(
        path: '/api/jenis-pegawai/{jenis_pegawai}',
        summary: 'Delete Jenis Pegawai',
        security: [['bearerAuth' => []]],
        tags: ['Jenis Pegawai'],
        parameters: [
            new OA\Parameter(
                name: 'jenis_pegawai',
                description: 'UUID of Jenis Pegawai',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string')
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Jenis Pegawai deleted'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 404, description: 'Not found'),
        ]
    )]
    public function destroy(JenisPegawai $jenisPegawai)
    {
        $jenisPegawai->delete();

        return response()->json(['message' => 'Jenis Pegawai deleted successfully']);
    }
}
