<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '3.0.0',
    title: 'My API',
    description: 'This is a sample API for demonstration purposes.'
)]
#[OA\Server(
    url: 'http://localhost:8000/api',
    description: 'Local Server'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth', // Name for the security scheme
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT',
    description: 'JWT Bearer Token'
)]
#[OA\Tag(
    name: 'Users',
    description: 'User related operations'
),]

#[OA\Tag(
    name: 'Auth',
    description: 'Auth related operations'
),]
abstract class Controller
{
    //
}
