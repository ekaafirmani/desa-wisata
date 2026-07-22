<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SewaPelampung extends Model
{
    protected $table = 'sewa_pelampung';

    protected $fillable = [
        'user_id',
        'jumlah',
        'catatan',
        'harga_satuan',
        'total_bayar',
        'waktu_kembali',
        'status',
    ];

    public function petugas ()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
