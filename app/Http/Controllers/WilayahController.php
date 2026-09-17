<?php

namespace App\Http\Controllers;

use App\Models\District;
use App\Models\Province;
use App\Models\Regency;
use App\Models\Village;
use OpenApi\Attributes as OA;

class WilayahController extends Controller
{
    #[OA\Get(
        path: '/api/wilayah/provinces',
        summary: 'Get all provinces',
        tags: ['Wilayah'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'code', type: 'string'),
                                    new OA\Property(property: 'name', type: 'string'),
                                ]
                            )
                        ),
                    ]
                )
            ),
        ]
    )]
    public function provinces()
    {
        $data = Province::orderBy('name', 'asc')->get();

        return response()->json(['data' => $data]);
    }

    #[OA\Get(
        path: '/api/wilayah/regencies/{province_code}',
        summary: 'Get regencies by province code',
        tags: ['Wilayah'],
        parameters: [
            new OA\Parameter(
                name: 'province_code',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'code', type: 'string'),
                                    new OA\Property(property: 'province_code', type: 'string'),
                                    new OA\Property(property: 'name', type: 'string'),
                                ]
                            )
                        ),
                    ]
                )
            ),
        ]
    )]
    public function regencies($provinceCode)
    {
        $data = Regency::where('province_code', $provinceCode)->orderBy('name', 'asc')->get();

        return response()->json(['data' => $data]);
    }

    #[OA\Get(
        path: '/api/wilayah/districts/{regency_code}',
        summary: 'Get districts by regency code',
        tags: ['Wilayah'],
        parameters: [
            new OA\Parameter(
                name: 'regency_code',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'code', type: 'string'),
                                    new OA\Property(property: 'regency_code', type: 'string'),
                                    new OA\Property(property: 'name', type: 'string'),
                                ]
                            )
                        ),
                    ]
                )
            ),
        ]
    )]
    public function districts($regencyCode)
    {
        $data = District::where('regency_code', $regencyCode)->orderBy('name', 'asc')->get();

        return response()->json(['data' => $data]);
    }

    #[OA\Get(
        path: '/api/wilayah/villages/{district_code}',
        summary: 'Get villages by district code',
        tags: ['Wilayah'],
        parameters: [
            new OA\Parameter(
                name: 'district_code',
                in: 'path',
                required: true,
                schema: new OA\Schema(type: 'string')
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Success',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(
                                properties: [
                                    new OA\Property(property: 'code', type: 'string'),
                                    new OA\Property(property: 'district_code', type: 'string'),
                                    new OA\Property(property: 'name', type: 'string'),
                                ]
                            )
                        ),
                    ]
                )
            ),
        ]
    )]
    public function villages($districtCode)
    {
        $data = Village::where('district_code', $districtCode)->orderBy('name', 'asc')->get();

        return response()->json(['data' => $data]);
    }
}
