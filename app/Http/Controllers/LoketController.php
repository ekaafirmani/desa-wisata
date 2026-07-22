<?php

namespace App\Http\Controllers;

use App\Models\TiketMasuk;
use App\Models\MasterStok;
use App\Models\PakanIkan;
use App\Services\PakanIkanService;
use Illuminate\Http\Request;

class LoketController extends Controller
{
    // Harga tiket per jenis kendaraan (tetap/hardcode)
    private const HARGA_TIKET = [
        'motor' => 5000,
        'mobil' => 10000,
        'bus'   => 25000,
    ];

    protected PakanIkanService $pakanIkanService;

    public function __construct(PakanIkanService $pakanIkanService)
    {
        $this->pakanIkanService = $pakanIkanService;
    }

    // Halaman Tiket Masuk
    public function tiket()
    {
        $riwayatHariIni = TiketMasuk::where('user_id', auth()->id())
            ->whereDate('created_at', today())
            ->latest()
            ->get();

        $totalHariIni = $riwayatHariIni->sum('total_bayar');

        return view('loket.tiket', [
            'hargaTiket'     => self::HARGA_TIKET,
            'riwayatHariIni' => $riwayatHariIni,
            'totalHariIni'   => $totalHariIni,
        ]);
    }

    // Simpan transaksi tiket masuk baru
    public function simpan(Request $request)
    {
        $request->validate([
            'jenis_kendaraan' => 'required|in:motor,mobil,bus',
            'jumlah'          => 'required|integer|min:1',
        ]);

        $hargaSatuan = self::HARGA_TIKET[$request->jenis_kendaraan];
        $totalBayar  = $hargaSatuan * $request->jumlah;

        TiketMasuk::create([
            'user_id'         => auth()->id(),
            'jenis_kendaraan' => $request->jenis_kendaraan,
            'jumlah'          => $request->jumlah,
            'harga_satuan'    => $hargaSatuan,
            'total_bayar'     => $totalBayar,
        ]);

        return redirect()->route('loket.tiket')
                         ->with('success', 'Transaksi tiket berhasil disimpan!');
    }

    // Halaman Pakan Ikan
    public function pakanIkan()
    {
        $stokPakanIkan = MasterStok::where('jenis', 'pakan_ikan')->first();

        $riwayatPakanIkan = PakanIkan::where('user_id', auth()->id())
            ->where('titik_jual', 'loket')
            ->whereDate('created_at', today())
            ->latest()
            ->get();

        $totalHariIni = $riwayatPakanIkan->sum('total_bayar');

        return view('loket.pakan_ikan', [
            'stokPakanIkan'    => $stokPakanIkan,
            'riwayatPakanIkan' => $riwayatPakanIkan,
            'totalHariIni'     => $totalHariIni,
        ]);
    }

    // Simpan transaksi pakan ikan (titik jual: loket)
    public function simpanPakanIkan(Request $request)
    {
        $request->validate([
            'jumlah_porsi' => 'required|integer|min:1',
        ]);

        $hasil = $this->pakanIkanService->jual(
            auth()->id(),
            'loket',
            $request->jumlah_porsi
        );

        return redirect()->route('loket.pakan_ikan')
                         ->with($hasil['success'] ? 'success' : 'error', $hasil['message']);
    }
}