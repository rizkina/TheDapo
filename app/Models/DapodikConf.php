<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DapodikConf extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'base_url',
        'npsn',
        'token',
        'is_active',
        'last_sync_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_sync_at' => 'datetime',
    ];

    protected static function booted()
    {
        static::saving(function ($config) {
            if ($config->is_active) {
                // Nonaktifkan konfigurasi lain jika yang ini diaktifkan
                static::where('id', '!=', $config->id)->update(['is_active' => false]);
            }
        });
    }
}
