<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TiketKolam extends Model
{
    protected $table = 'tiket_kolam';

    protected $fillable = [
        'user_id',
        'jumlah_orang',
        'harga_satuan',
        'total_bayar',
    ];

    public function petugas()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
