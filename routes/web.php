<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ZipUploadController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegisterController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/view/{title}', [ZipUploadController::class, 'viewFile']);

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->group(function () {
        Route::get('/upload', [ZipUploadController::class, 'showForm'])->name('zip.form');
        Route::post('/upload', [ZipUploadController::class, 'handleUpload'])->name('zip.upload');
    });
Route::get('/', [HomeController::class, 'index'])->name('home');


Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
