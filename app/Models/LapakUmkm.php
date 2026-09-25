<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LapakUmkm extends Model
{
    protected $table = 'lapak_umkm';

    protected $fillable = [
        'nama_pedagang',
        'nama_usaha',
        'jenis_usaha',
        'no_telepon',
        'tarif_bulanan',
        'status',
        'tanggal_mulai',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
    ];

    public function pembayaran()
    {
        return $this->hasMany(PembayaranUmkm::class, 'lapak_id');
    }

    public function sudahBayar($bulan, $tahun)
    {
        return $this->pembayaran()
                    ->where('bulan', $bulan)
                    ->where('tahun', $tahun)
                    ->where('status', 'lunas')
                    ->exists();
    }
}