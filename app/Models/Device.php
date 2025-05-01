<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    protected $fillable = [
        'name',
        'number',
        'device',
        'status',
        'package',
        'quota',
        'autoread',
        'expired',
        'token',
        'last_active'
    ];

    // Tidak perlu timestamps karena kita tidak menyimpan ke database
    public $timestamps = false;

    // Tidak perlu table karena kita tidak menyimpan ke database
    protected $table = null;

    // Tidak perlu koneksi database
    protected $connection = null;
}
