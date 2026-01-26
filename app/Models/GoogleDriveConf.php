<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoogleDriveConf extends Model
{
     use SoftDeletes;

    protected $fillable = [
        'name',
        'client_id', 
        'client_secret', 
        'access_token', 
        'refresh_token',
        'folder_id', 
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'client_secret' => 'encrypted', // Otomatis enkripsi di DB
        'access_token' => 'encrypted',
        'refresh_token' => 'encrypted',
    ];

    // Logika otomatis: Hanya boleh satu koneksi Drive yang aktif
    protected static function booted()
    {
        static::saving(function ($config) {
            if ($config->is_active) {
                static::where('id', '!=', $config->id)->update(['is_active' => false]);
            }
        });
    }
}
