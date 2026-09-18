<?php

$controllers = [
    'ManajemenAksiController' => 'namaAksi',
    'ManajemenVisitController' => 'namaClient',
    'ManajemenCanvasingController' => 'namaClient',
    'ManajemenHoController' => 'namaClient',
    'ManajemenPpdController' => 'namaClient',
];

foreach ($controllers as $controller => $filterField) {
    $path = "/var/www/html/monitoring-karyawan/app/Http/Controllers/Api/{$controller}.php";
    $content = file_get_contents($path);

    // 1. Revert index()
    $badIndex = <<<'CODE'
        $user = $request->user();
        if ($user) {
            $roleName = $user->role ? strtolower($user->role->nama_role) : '';
            if (!in_array($roleName, ['superadmin', 'leader'])) {
                $query->where('user_id', $user->id);
            } else {
                if ($request->has('user_id') && !empty($request->query('user_id'))) {
                    $query->where('user_id', $request->query('user_id'));
                }
            }
        }
CODE;
    $goodIndex = <<<'CODE'
        if ($request->has('user_id') && !empty($request->query('user_id'))) {
            $query->where('user_id', $request->query('user_id'));
        }
CODE;
    $content = str_replace($badIndex, $goodIndex, $content);

    // 2. Revert export()
    $badExport = <<<CODE
        \$filters = \$request->all();
        \$user = \$request->user();
        if (\$user) {
            \$roleName = \$user->role ? strtolower(\$user->role->nama_role) : '';
            if (!in_array(\$roleName, ['superadmin', 'leader'])) {
                \$filters['user_id'] = \$user->id;
            }
        }
        return Excel::download(new {$controller}Export(\$filters),
CODE;
    $badExportName = str_replace('Controller', '', $controller);
    $badExportStr = <<<CODE
        \$filters = \$request->all();
        \$user = \$request->user();
        if (\$user) {
            \$roleName = \$user->role ? strtolower(\$user->role->nama_role) : '';
            if (!in_array(\$roleName, ['superadmin', 'leader'])) {
                \$filters['user_id'] = \$user->id;
            }
        }
        return Excel::download(new {$badExportName}Export(\$filters), 
CODE;

    // We'll use regex for export to be safer
    $content = preg_replace(
        '/(\$filters = \$request->all\(\);\s*\$user = \$request->user\(\);\s*if \(\$user\) \{\s*\$roleName = \$user->role \? strtolower\(\$user->role->nama_role\) : \'\';\s*if \(!in_array\(\$roleName, \[\'superadmin\', \'leader\'\]\)\) \{\s*\$filters\[\'user_id\'\] = \$user->id;\s*\}\s*\}\s*return Excel::download\(new [a-zA-Z]+Export\()\$filters(\), \'[a-zA-Z_]+\.xlsx\'\);)/s',
        'return Excel::download(new '.str_replace('Controller', '', $controller).'Export($request->all()), \''.strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', str_replace('Controller', '', $controller))).'.xlsx\');',
        $content
    );

    // 3. Add getByUser method
    $modelName = str_replace('Controller', '', $controller);
    $endpointName = strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $modelName));
    $relation = ($controller === 'ManajemenAksi' || $controller === 'ManajemenPpd') ? "'user'" : "['user', 'status']";

    // For specific models, they might have ['user', 'status'] or just 'user'
    if ($controller == 'ManajemenAksiController') {
        $relation = "'user'";
    }
    if ($controller == 'ManajemenVisitController') {
        $relation = "['user', 'status']";
    }
    if ($controller == 'ManajemenCanvasingController') {
        $relation = "['user', 'status']";
    }
    if ($controller == 'ManajemenHoController') {
        $relation = "['user', 'status']";
    }
    if ($controller == 'ManajemenPpdController') {
        $relation = "'user'";
    }

    $tagName = str_replace('-', ' ', ucwords(str_replace('manajemen-', 'manajemen ', $endpointName)));

    $getByUserMethod = <<<METHOD

    #[OA\Get(
        path: '/api/{$endpointName}/user/{userId}',
        summary: 'Get list of {$modelName} by user id',
        security: [['bearerAuth' => []]],
        tags: ['{$tagName}'],
        parameters: [
            new OA\Parameter(name: 'userId', in: 'path', required: true, schema: new OA\Schema(type: 'string', format: 'uuid')),
            new OA\Parameter(name: 'filter_nama', in: 'query', required: false, schema: new OA\Schema(type: 'string')),
            new OA\Parameter(name: 'filter_tanggal', in: 'query', required: false, schema: new OA\Schema(type: 'string', format: 'date'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
            new OA\Response(response: 401, description: 'Unauthorized'),
        ]
    )]
    public function getByUser(Request \$request, \$userId)
    {
        \$query = {$modelName}::with({$relation})->where('user_id', \$userId);
        
        if (\$request->has('filter_nama') && !empty(\$request->query('filter_nama'))) {
            \$query->where('{$filterField}', 'like', '%' . \$request->query('filter_nama') . '%');
        }

        if (\$request->has('filter_tanggal') && !empty(\$request->query('filter_tanggal'))) {
            \$query->whereDate('created_at', \$request->query('filter_tanggal'));
        }

        \$data = \$query->get();
        return response()->json(\$data);
    }
}
METHOD;

    // Replace the last closing brace with the new method and closing brace
    $content = preg_replace('/\}\s*$/', $getByUserMethod, $content);

    file_put_contents($path, $content);
    echo "Patched {$controller}\n";
}
