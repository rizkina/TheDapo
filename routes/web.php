<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleDriveController;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\File;
use App\Services\GoogleDriveService;
use App\Http\Controllers\LandingController;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', [LandingController::class, 'index'])->name('landing');

Route::get('/google-drive/connect', [GoogleDriveController::class, 'connect'])
    ->name('google.drive.connect')
    ->middleware(['auth']); // Hanya user login yang bisa akses

Route::get('/google-drive/callback', [GoogleDriveController::class, 'callback'])
    ->name('google.drive.callback');

Route::get('/files/{file}/preview', function (File $file) {
    // 1. Keamanan: Cek Policy (Pastikan class FilePolicy sudah benar)
    if (Auth::user()->cannot('view', $file)) { 
        abort(403, 'Anda tidak memiliki izin untuk melihat file ini.'); 
    }

    // 2. Suntikkan Config Google Drive
    GoogleDriveService::applyConfig();
    
    /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
    $disk = Storage::disk('google');

    // 3. Cek apakah file benar-benar ada di Drive
    if (!$disk->exists($file->file_path)) {
        abort(404, 'File tidak ditemukan di Google Drive.');
    }

    // 4. STREAMING RESPONSE (Sangat Ringan Memori)
    // Kita gunakan readStream agar data mengalir bit demi bit, bukan sekaligus
    return response()->stream(
        function () use ($disk, $file) {
            $stream = $disk->readStream($file->file_path);
            if ($stream) {
                fpassthru($stream); // Mengalirkan data langsung ke output browser
                if (is_resource($stream)) {
                    fclose($stream);
                }
            }
        }, 
        200, 
        [
            'Content-Type' => $file->mime_type ?? 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $file->original_name . '"',
            'Cache-Control' => 'no-cache, must-revalidate',
        ]
    );
})->name('file.preview')->middleware(['auth']);