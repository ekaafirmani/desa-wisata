<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SewaGazebo extends Model
{
    protected $table = 'sewa_gazebo';

    protected $fillable = [
        'user_id',
        'titik_jual',
        'jenis_gazebo',
        'jumlah',
        'catatan',
        'harga_satuan',
        'total_bayar',
        'status',
        'waktu_kembali',
    ];

    public function petugas()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}