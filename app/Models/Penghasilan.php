<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penghasilan extends Model
{
    protected $table = 'penghasilans';

    protected $fillable = [
        'kode',
        'nama',
    ];
}
