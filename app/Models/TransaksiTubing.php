<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiTubing extends Model
{
    protected $table = 'transaksi_tubing';

    protected $fillable = [
        'user_id',
        'paket_id',
        'jumlah_peserta',
        'total_bayar',
    ];

    public function petugas()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function paket()
    {
        return $this->belongsTo(PaketTubing::class, 'paket_id');
    }
}
