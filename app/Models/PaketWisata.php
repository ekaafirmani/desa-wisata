<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaketWisata extends Model
{
    protected $table = 'paket_wisata';

    protected $fillable = [
        'nama_paket',
        'deskripsi',
        'fasilitas',
        'harga_per_orang',
        'minimal_orang',
        'aktif',
    ];

    public function transaksi()
    {
        return $this->hasMany(TransaksiPaketWisata::class, 'paket_id');
    }
}