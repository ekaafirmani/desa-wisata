<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterStok extends Model
{
    protected $table = 'master_stok';

    protected $fillable = [
        'jenis',
        'total_stok',
        'tersedia',
        'harga_satuan',
    ];

    // Batas minimum sebelum stok otomatis ditambah
    const BATAS_MINIMUM = 10;

    // Jumlah stok tersedia setelah otomatis ditambah
    const JUMLAH_TOP_UP = 50;

    /**
     * Kurangi stok tersedia, lalu otomatis top-up kalau sudah menyentuh batas minimum.
     * Khusus dipakai untuk stok Pakan Ikan.
     */
    public function kurangiStok(int $jumlah): void
    {
        $this->decrement('tersedia', $jumlah);
        $this->refresh();

        if ($this->tersedia <= self::BATAS_MINIMUM) {
            $tambahan = self::JUMLAH_TOP_UP - $this->tersedia;

            $this->increment('tersedia', $tambahan);
            $this->increment('total_stok', $tambahan);
        }
    }
}