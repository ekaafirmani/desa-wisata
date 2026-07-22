<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiketMasuk extends Model
{
    protected $table = 'tiket_masuk';

    protected $fillable = [
        'user_id',
        'jenis_kendaraan',
        'jumlah',
        'harga_satuan',
        'total_bayar',
    ];

    public function petugas ()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
