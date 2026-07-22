<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PakanIkan extends Model
{
    protected $table = "pakan_ikan";

    protected $fillable = [
        'user_id',
        'titik_jual',
        'jumlah_porsi',
        'harga_satuan',
        'total_bayar',
    ];

    public function petugas()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
