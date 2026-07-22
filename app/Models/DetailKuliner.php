<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailKuliner extends Model
{
    protected $table = 'detail_kuliner';

    protected $fillable = [
        'transaksi_id',
        'menu_id',
        'jumlah',
        'subtotal',
    ];

    public function transaksi()
    {
        return $this->belongsTo(TransaksiKuliner::class, 'transaksi_id');
    }

    public function menu()
    {
        return $this->belongsTo(MenuKuliner::class, 'menu_id');
    }
}
