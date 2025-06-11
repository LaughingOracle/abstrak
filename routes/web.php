<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ZipUploadController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AbstractPaperController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/view/{title}', [ZipUploadController::class, 'viewFile']);

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->group(function () {
        Route::get('/usermenu', [UserController::class, 'listing'])->name('usermenu');
        Route::get('/upload', [ZipUploadController::class, 'showForm'])->name('zip.form');
        Route::post('/upload', [ZipUploadController::class, 'handleUpload'])->name('zip.upload');
        Route::post('/update', [ZipUploadController::class, 'handleUpdate'])->name('zip.update');
        Route::resource('abstracts', AbstractPaperController::class);
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
