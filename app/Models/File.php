<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'id',
        'file_category_id',
        'user_id',
        'file_path',
        'file_name',
        'original_name',
        'mime_type',
        'size',
        'disk',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
        ];
    }

    /**
     * Accessor untuk menampilkan ukuran file yang manusiawi
     */
    public function getFormattedSizeAttribute(): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($this->size, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        return round($bytes / (1024 ** $pow), 2) . ' ' . $units[$pow];
    }

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
            if ($file->file_category_id && $file->user_id) {
                // Cari apakah user sudah pernah upload kategori ini
                $existing = self::where('user_id', $file->user_id)
                                ->where('file_category_id', $file->file_category_id)
                                ->first();
                
                if ($existing) {
                    // Hapus record lama di database agar diganti yang baru (Overwrite)
                    $existing->forceDelete(); 
                }
            }
        });
    }
}
