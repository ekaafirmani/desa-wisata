<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IkanHiasPenjualan extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'jumlah_ikan',
        'harga_satuan',
        'total_bayar',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}