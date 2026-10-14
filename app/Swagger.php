<?php

namespace App;

use OpenApi\Attributes as OA;

if (! defined('L5_SWAGGER_CONST_HOST')) {
    define('L5_SWAGGER_CONST_HOST', env('L5_SWAGGER_CONST_HOST', env('APP_URL', 'http://localhost')));
}

#[OA\Info(
    version: '1.0.0',
    title: 'Monitoring Karyawan API',
    description: 'API Documentation for Monitoring Karyawan',
    contact: new OA\Contact(email: 'admin@monitoringkaryawan.local'),
    license: new OA\License(name: 'Apache 2.0', url: 'http://www.apache.org/licenses/LICENSE-2.0.html')
)]
#[OA\Server(url: L5_SWAGGER_CONST_HOST, description: 'Current API Server')]
#[OA\Server(url: 'http://monitoringkaryawan.local/', description: 'Local API Server')]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    name: 'bearerAuth',
    in: 'header',
    scheme: 'bearer',
    bearerFormat: 'JWT'
)]
class Swagger {}
