<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;


class Announcement extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'judul',
        'konten',
        'tipe',
        'is_active',
        'expires_at'
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'expires_at' => 'datetime', // Tambahkan ini juga agar tidak error saat expired
            'is_active' => 'boolean',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(\Spatie\Permission\Models\Role::class, 'announcement_role');
    }
}
