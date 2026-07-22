<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuKuliner extends Model
{
    protected $table = 'menu_kuliner';

    protected $fillable = [
        'nama_menu',
        'harga',
        'kategori',
        'tersedia',
    ];

    public function detail()
    {
        return $this->hasMany(DetailKuliner::class, 'menu_id');
    }

}
