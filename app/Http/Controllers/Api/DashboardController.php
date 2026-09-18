<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ManajemenAksi;
use App\Models\ManajemenCanvasing;
use App\Models\ManajemenHo;
use App\Models\ManajemenPpd;
use App\Models\ManajemenVisit;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class DashboardController extends Controller
{
    #[OA\Get(
        path: '/api/dashboard/stats',
        summary: 'Get dashboard statistics for Manajemen modules',
        security: [['bearerAuth' => []]],
        tags: ['Dashboard'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Successful operation',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'aksi', type: 'integer'),
                        new OA\Property(property: 'visit', type: 'integer'),
                        new OA\Property(property: 'canvasing', type: 'integer'),
                        new OA\Property(property: 'ho', type: 'integer'),
                        new OA\Property(property: 'ppd', type: 'integer'),
                    ]
                )
            ),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ]
    )]
    public function stats(Request $request)
    {
        return response()->json([
            'aksi' => ManajemenAksi::count(),
            'visit' => ManajemenVisit::count(),
            'canvasing' => ManajemenCanvasing::count(),
            'ho' => ManajemenHo::count(),
            'ppd' => ManajemenPpd::count(),
        ]);
    }
}
