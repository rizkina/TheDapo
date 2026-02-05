<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleDriveController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\File;
use App\Services\GoogleDriveService;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/google-drive/connect', [GoogleDriveController::class, 'connect'])
    ->name('google.drive.connect')
    ->middleware(['auth']); // Hanya user login yang bisa akses

Route::get('/google-drive/callback', [GoogleDriveController::class, 'callback'])
    ->name('google.drive.callback');

Route::get('/files/{file}/preview', function (File $file) {
    // Keamanan: Pastikan hanya pemilik atau admin yang bisa lihat
    if (Auth::user()->cannot('view', $file)) { abort(403, 'Anda tidak memiliki izin untuk melihat file ini.'); }

    GoogleDriveService::applyConfig();
    
    /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
    $disk = Storage::disk('google');

    if (!$disk->exists($file->file_path)) {
        abort(404, 'File tidak ditemukan di Google Drive.');
    }

    $content = $disk->get($file->file_path);
    $mimeType = $disk->mimeType($file->file_path);
    return response()->stream(function() use ($disk, $file){
        echo $disk->get($file->file_path);
    }, 200, [
        'Content-Type' => $file->mime_type,
        'Content-Disposition' => 'inline; filename="' . $file->original_name . '"',
    ]);
})->name('file.preview')->middleware(['auth']);