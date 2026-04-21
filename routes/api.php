<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FileApiController;
use App\Http\Controllers\Api\ApiShareLinkController;
use App\Http\Controllers\Api\ReportApiController;
use App\Http\Middleware\TokenFromQuery;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public Endpoints
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Public link endpoints (no auth)
Route::get(
    '/share/{token}',
    [ApiShareLinkController::class, 'show']
);
Route::get(
    '/share/{token}/image',
    [ApiShareLinkController::class, 'image']
)->name('api.share.image')->middleware('signed');

// Protected Endpoints
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', [AuthController::class, 'user']);

    Route::name('api.')->group(function () {
        Route::apiResource('files', FileApiController::class)->except(['update']);
    });

    Route::get('files/{file}/download', [FileApiController::class, 'download']);
    Route::get('files/{file}/preview', [FileApiController::class, 'preview']);

    Route::post(
        'files/{file}/links',
        [ApiShareLinkController::class, 'store']
    );

    Route::get('/reports', [ReportApiController::class, 'index']);
});
