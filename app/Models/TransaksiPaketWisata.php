<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiPaketWisata extends Model
{
    protected $table = 'transaksi_paket_wisata';

    protected $fillable = [
        'user_id',
        'paket_id',
        'jumlah_orang',
        'harga_per_orang',
        'total_bayar',
        'nama_pemesan',
        'no_telepon',
        'catatan',
    ];

    public function petugas()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function paket()
    {
        return $this->belongsTo(PaketWisata::class, 'paket_id');
    }
}