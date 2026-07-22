<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransaksiKuliner extends Model
{
    protected $table = 'transaksi_kuliner';

    protected $fillable = [
        'user_id',
        'total_bayar',
    ];

    public function petugas()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function detail()
    {
        return $this->hasMany(DetailKuliner::class, 'transaksi_id');
    }
}
