<?php

namespace App\Services;

use App\Models\PakanIkan;
use App\Models\MasterStok;
use Illuminate\Support\Facades\DB;

class PakanIkanService
{
    // Harga pakan ikan per porsi (tetap/hardcode)
    const HARGA_PER_PORSI = 2000;

    /**
     * Proses penjualan pakan ikan: cek stok, kurangi stok, simpan transaksi.
     *
     * @param int $userId ID petugas yang menjual
     * @param string $titikJual 'loket' | 'kolam' | 'kuliner'
     * @param int $jumlahPorsi
     * @return array ['success' => bool, 'message' => string, 'data' => PakanIkan|null]
     */
    public function jual(int $userId, string $titikJual, int $jumlahPorsi): array
    {
        $stok = MasterStok::where('jenis', 'pakan_ikan')->first();

        if (!$stok) {
            return [
                'success' => false,
                'message' => 'Data stok pakan ikan belum tersedia. Hubungi admin.',
                'data'    => null,
            ];
        }

        if ($stok->tersedia < $jumlahPorsi) {
            return [
                'success' => false,
                'message' => "Stok pakan ikan tidak cukup. Tersedia: {$stok->tersedia} porsi.",
                'data'    => null,
            ];
        }

        $totalBayar = self::HARGA_PER_PORSI * $jumlahPorsi;

        $transaksi = DB::transaction(function () use ($userId, $titikJual, $jumlahPorsi, $totalBayar, $stok) {
            $stok->decrement('tersedia', $jumlahPorsi);

            return PakanIkan::create([
                'user_id'      => $userId,
                'titik_jual'   => $titikJual,
                'jumlah_porsi' => $jumlahPorsi,
                'harga_satuan' => self::HARGA_PER_PORSI,
                'total_bayar'  => $totalBayar,
            ]);
        });

        return [
            'success' => true,
            'message' => 'Penjualan pakan ikan berhasil disimpan!',
            'data'    => $transaksi,
        ];
    }
}