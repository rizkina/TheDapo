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
}
