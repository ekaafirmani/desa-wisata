<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaketTubing extends Model
{
    protected $table = 'paket_tubing';

    protected $fillable = [
        'nama_paket',
        'jenis',
        'fasilitas',
        'harga',
        'aktif',
    ];

    public function transaksi()
    {
        return $this->hasMany(TransaksiTubing::class, 'paket_id');
    }
}
