<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GoogleDriveConf extends Model
{
     use SoftDeletes;

    protected $fillable = ['name', 'service_account_json', 'folder_id', 'is_active'];

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
