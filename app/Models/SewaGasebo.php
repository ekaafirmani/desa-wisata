<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SewaGasebo extends Model
{
    protected $table = 'sewa_gasebo';

    protected $fillable = [
        'user_id',
        'gasebo_id',
        'nama_penyewa',
        'durasi_jam',
        'harga_per_jam',
        'total_bayar',
        'waktu_mulai',
        'waktu_selesai',
        'status',
        'catatan',
    ];

    protected $casts = [
        'waktu_mulai'   => 'datetime',
        'waktu_selesai' => 'datetime',
    ];

    public function petugas()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function gasebo()
    {
        return $this->belongsTo(Gasebo::class, 'gasebo_id');
    }
}