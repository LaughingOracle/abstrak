<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ZipUploadController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/view/{title}', [ZipUploadController::class, 'viewFile']);

Route::get('/upload', [ZipUploadController::class, 'showForm'])->name('zip.form');
Route::post('/upload', [ZipUploadController::class, 'handleUpload'])->name('zip.upload');