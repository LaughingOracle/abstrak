<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ZipUploadController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AbstractPaperController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ForgotPasswordController;

//defunct, no dynamic routing in blade's href (yet)
//Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('/viewAbstract/{id}', [ZipUploadController::class, 'viewAbstract'])->name('viewAbstract');
Route::get('/view/{id}', [ZipUploadController::class, 'viewFile'])->name('view');

// route for dr john doctor
Route::get('/client/{event}/{name}', [ClientController::class, 'listing'])->name('listing');
Route::get('/scoringList/{event}/{name}', [ClientController::class, 'scoringList'])->name('scoringList');
Route::get('/scoreMenu/{id}', [ClientController::class, 'scoreMenu'])->name('scoreMenu');
Route::get('/scoreMenu2/{id}', [ClientController::class, 'scoreMenu2'])->name('scoreMenu2');
Route::post('/score', [ClientController::class, 'score'])->name('score');
Route::post('/score2', [ClientController::class, 'score2'])->name('score2');
Route::post('/review/{id}', [ClientController::class, 'review'])->name('review');
Route::post('/revise/{id}', [ClientController::class, 'revise'])->name('revise');

//use default register controller for POST method, *shrugs* it worked
Route::get('/register/{event}', [RegisterController::class, 'show'])
    ->name('register.with.event');
// shit aint workin, now used custom post register
Route::post('/register', [RegisterController::class, 'store']);

Route::get('/login/{event}', [LoginController::class, 'showLoginForm'])->name('custom.login');
Route::post('/login', [LoginController::class, 'login'])->name('custom.login.submit');

//not using middleware for custom guest access login rerouting (rerouting logic in controller)
Route::get('/usermenu/{event}', [UserController::class, 'listing'])->name('usermenu');


Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified'])
    ->group(function () {
        Route::get('/upload', [ZipUploadController::class, 'showForm'])->name('zip.form');
        Route::post('/uploadPresentation', [ZipUploadController::class, 'uploadPresentation'])->name('uploadPresentation');
        Route::post('/upload', [ZipUploadController::class, 'handleUpload'])->name('zip.upload');
        Route::post('/update', [ZipUploadController::class, 'handleUpdate'])->name('zip.update');
        Route::resource('abstracts', AbstractPaperController::class);
});

Route::get('/viewPresentation/{id}', [ZipUploadController::class, 'viewPresentation'])->name('viewPresentation');



// there's a custom admin email n password check inside all the dashboard route (VERY unoptimized)
Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
Route::post('/dashboard', [AdminController::class, 'assignReviewer'])->name('insertReviewer');
Route::post('/dashboard/event', [AdminController::class, 'assignEvent'])->name('assignEvent');
Route::post('/dashboard/topic', [AdminController::class, 'assignTopic'])->name('assignTopic');

//this one is an extention for dashboard, its in a different page to avoid ui cluster
//also, please avoid using this when an event is ongoing
Route::get('/formMenu', [AdminController::class, 'formMenu'])->name('formMenu');
Route::post('/formInsert', [AdminController::class, 'formInsert'])->name('formInsert');
Route::delete('/formDelete/{id}', [AdminController::class, 'deleteForm'])->name('formDelete');
Route::get('/report', [AdminController::class, 'generateReport'])->name('report');

Route::get('/forgot-password/{event}', [ForgotPasswordController::class, 'showRequestForm'])->name('custom.password.request');
Route::post('/forgot-password/{event}', [ForgotPasswordController::class, 'sendResetLink']);

Route::get('/reset-password/{token}/{event}', [ForgotPasswordController::class, 'showResetForm'])->name('custom.password.reset');
Route::post('/reset-password/{event}', [ForgotPasswordController::class, 'resetPassword']);

//dev note: i know this code is fucking terrible(i exclusively wrote terrible code),
//but the app scale mid dev (thank got its not post production)(it got upscaled again).

//there's really should've a uniform software proposal document template 
//(like idunno, a flow diagram, theres one btw, i made that, thats why its sucked)

//shoutout to aork123 for letting me stay in Goonswarm after not log-in for 6 months