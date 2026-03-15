<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pekerjaan extends Model
{
    protected $table = 'pekerjaans';

    protected $fillable = [
        'kode',
        'nama',
    ];
}
