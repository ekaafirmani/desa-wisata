<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranUmkm extends Model
{
    protected $table = 'pembayaran_umkm';

    protected $fillable = [
        'lapak_id',
        'user_id',
        'bulan',
        'tahun',
        'jumlah_bayar',
        'tanggal_bayar',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
    ];

    public function lapak()
    {
        return $this->belongsTo(LapakUmkm::class, 'lapak_id');
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getNamaBulanAttribute()
    {
        $bulan = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
            4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September',
            10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        return $bulan[$this->bulan] ?? '-';
    }
}