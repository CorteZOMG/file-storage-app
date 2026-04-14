<?php

use App\Http\Controllers\FileController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ShareLinkController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('files.index');
});

Route::get('/dashboard', [\App\Http\Controllers\ReportController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Share Route
Route::get('/shared/{token}', [ShareLinkController::class, 'show'])->name('shared.show');
Route::get('/shared/{token}/image', [ShareLinkController::class, 'image'])->name('shared.image')->middleware('signed');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // File management routes
    Route::get('/files/{file}/download', [FileController::class, 'download'])->name('files.download');
    Route::get('/files/{file}/preview', [FileController::class, 'preview'])->name('files.preview');
    Route::get('/files/{file}', [FileController::class, 'show'])->name('files.show');
    Route::get('/files', [FileController::class, 'index'])->name('files.index');
    Route::post('/files', [FileController::class, 'store'])->name('files.store');
    Route::delete('/files/{file}', [FileController::class, 'destroy'])->name('files.destroy');
    Route::post('/files/{file}/share', [ShareLinkController::class, 'store'])->name('files.share');

});

require __DIR__ . '/auth.php';
