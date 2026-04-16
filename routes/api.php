<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FileApiController;
use App\Http\Controllers\Api\ApiShareLinkController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public Endpoints
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public link view with no auth
Route::get(
    '/share/{token}',
    [ApiShareLinkController::class, 'show']
);

// Protected Endpoints
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::apiResource('files', FileApiController::class)->except(['update']);

    Route::post(
        'files/{file}/links',
        [ApiShareLinkController::class, 'store']
    );
});
