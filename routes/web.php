<?php

use App\Http\Controllers\FileController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('files.index');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // File management routes
    Route::get('/files/{file}/download', [FileController::class, 'download'])->name('files.download');
    Route::get('/files/{file}', [FileController::class, 'show'])->name('files.show');
    Route::get('/files', [FileController::class, 'index'])->name('files.index');
    Route::post('/files', [FileController::class, 'store'])->name('files.store');
    Route::delete('/files/{file}', [FileController::class, 'destroy'])->name('files.destroy');

    Route::get('/reports', function () {
        return "Reports list here";
    })->name('reports.index');
});

require __DIR__ . '/auth.php';
