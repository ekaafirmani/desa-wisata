<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterStok extends Model
{
    protected $table = 'master_stok';

    protected $fillable = [
        'jenis',
        'total_stok',
        'tersedia',
        'harga_satuan',
    ];
}
