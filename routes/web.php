<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleDriveController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/google-drive/connect', [GoogleDriveController::class, 'connect'])
    ->name('google.drive.connect')
    ->middleware(['auth']); // Hanya user login yang bisa akses

Route::get('/google-drive/callback', [GoogleDriveController::class, 'callback'])
    ->name('google.drive.callback');