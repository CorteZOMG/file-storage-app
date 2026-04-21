<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="File Storage API",
 *     description="API documentation for the File Storage Service"
 * )
 * @OA\Server(
 *     url="/api",
 *     description="API Server"
 * )
 * @OA\SecurityScheme(
 *     type="http",
 *     scheme="bearer",
 *     securityScheme="bearerAuth"
 * )
 */
abstract class Controller
{
}
