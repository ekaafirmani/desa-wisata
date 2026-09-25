<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gasebo extends Model
{
    protected $table = 'gasebo';

    protected $fillable = [
        'nama_gasebo',
        'jenis',
        'harga_per_jam',
        'durasi_minimal',
        'status',
        'aktif',
    ];

    public function sewa()
    {
        return $this->hasMany(SewaGasebo::class, 'gasebo_id');
    }

    // Cek apakah gasebo sedang disewa
    public function sedangDisewa()
    {
        return $this->status === 'disewa';
    }
}