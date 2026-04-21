<?php

namespace App\Docs;

use OpenApi\Attributes as OAT;

#[OAT\Info(
    version: "1.0.0",
    description: "API documentation for the File Storage Service",
    title: "File Storage API"
)]
#[OAT\Server(
    url: "/api",
    description: "API Server"
)]
#[OAT\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer"
)]
class Swagger
{
}
