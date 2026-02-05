<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id',
        'file_path',
        'file_name',
        'original_name',
        'mime_type',
        'size',
        'disk',
    ];

    public function user()
    {
        return $this->belongsTo(Dapodik_User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(FileCategory::class, 'file_category_id');
    }

    protected static function booted()
    {
        static::creating(function ($file) {
            // Cari apakah user sudah pernah upload kategori ini
            $existing = self::where('user_id', $file->user_id)
                            ->where('file_category_id', $file->file_category_id)
                            ->first();
            
            if ($existing) {
                // Hapus record lama di database agar diganti yang baru (Overwrite)
                $existing->forceDelete(); 
            }
        });
    }
}
