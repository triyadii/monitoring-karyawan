<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class MemberStatsController extends Controller
{
    #[OA\Get(
        path: '/api/member-stats/{type}',
        summary: 'Get members and their input count based on type',
        security: [['bearerAuth' => []]],
        tags: ['Member Stats'],
        parameters: [
            new OA\Parameter(
                name: 'type',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string', enum: ['aksi', 'visit', 'canvasing', 'ho', 'ppd'])
            )
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
            new OA\Response(response: 401, description: 'Unauthorized'),
            new OA\Response(response: 400, description: 'Invalid type')
        ]
    )]
    public function getStats($type)
    {
        $query = User::with(['role', 'jenisPegawai']);

        $relationMap = [
            'aksi' => 'manajemenAksi',
            'visit' => 'manajemenVisit',
            'canvasing' => 'manajemenCanvasing',
            'ho' => 'manajemenHo',
            'ppd' => 'manajemenPpd',
        ];

        if (!array_key_exists($type, $relationMap)) {
            return response()->json(['error' => 'Invalid type'], 400);
        }

        $relationCount = $relationMap[$type];

        if (in_array($type, ['aksi', 'visit', 'canvasing'])) {
            $query->whereHas('role', function ($q) {
                $q->where('nama_role', 'ilike', 'channeling');
            });
        } elseif ($type === 'ho') {
            $query->whereHas('role', function ($q) {
                $q->where('nama_role', 'ilike', 'retention');
            });
        } elseif ($type === 'ppd') {
            $query->whereHas('jenisPegawai', function ($q) {
                $q->where('jenisPegawai', 'ilike', 'cco')
                  ->orWhere('jenisPegawai', 'ilike', 'cro');
            });
        }

        $members = $query->withCount($relationCount)->get();

        $result = $members->map(function ($user) use ($relationCount) {
            $countKey = $relationCount . '_count';
            return [
                'id' => $user->id,
                'uuid' => $user->uuid,
                'nama' => $user->nama,
                'username' => $user->username,
                'role' => $user->role ? $user->role->nama_role : '-',
                'jenis_pegawai' => $user->jenisPegawai ? $user->jenisPegawai->jenisPegawai : '-',
                'total_input' => $user->$countKey
            ];
        });

        return response()->json($result);
    }
}
