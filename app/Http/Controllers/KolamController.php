<?php

namespace App\Http\Controllers;
use App\Models\IkanHiasPenjualan;
use App\Services\IkanHiasService;
use App\Models\TiketKolam;
use App\Models\SewaPelampung;
use App\Models\MasterStok;
use App\Models\PakanIkan;
use App\Services\PakanIkanService;
use Illuminate\Http\Request;


class KolamController extends Controller
{
    // Harga tetap
    const HARGA_TIKET_KOLAM = 10000;
    const HARGA_SEWA_PELAMPUNG = 5000;

    protected PakanIkanService $pakanIkanService;

    public function __construct(PakanIkanService $pakanIkanService)
    {
        $this->pakanIkanService = $pakanIkanService;
    }

    // Halaman Tiket Kolam
    public function tiket()
    {
        $riwayatTiket = TiketKolam::where('user_id', auth()->id())
            ->whereDate('created_at', today())
            ->latest()
            ->get();

        $totalHariIni = $riwayatTiket->sum('total_bayar');

        return view('kolam.tiket', [
            'riwayatTiket'    => $riwayatTiket,
            'totalHariIni'    => $totalHariIni,
            'hargaTiketKolam' => self::HARGA_TIKET_KOLAM,
        ]);
    }

    // Simpan transaksi tiket kolam
    public function simpanTiket(Request $request)
    {
        $request->validate([
            'jumlah_orang' => 'required|integer|min:1',
        ]);

        TiketKolam::create([
            'user_id'      => auth()->id(),
            'jumlah_orang' => $request->jumlah_orang,
            'harga_satuan' => self::HARGA_TIKET_KOLAM,
            'total_bayar'  => self::HARGA_TIKET_KOLAM * $request->jumlah_orang,
        ]);

        return redirect()->route('kolam.tiket')
                         ->with('success', 'Transaksi tiket kolam berhasil disimpan!');
    }

    // Halaman Sewa Pelampung
    public function pelampung()
    {
        $stokPelampung = MasterStok::where('jenis', 'pelampung')->first();

        $riwayatPelampung = SewaPelampung::where('user_id', auth()->id())
            ->whereDate('created_at', today())
            ->latest()
            ->get();

        $pelampungDipinjam = SewaPelampung::where('status', 'dipinjam')
            ->latest()
            ->get();

        $totalHariIni = $riwayatPelampung->sum('total_bayar');

        return view('kolam.pelampung', [
            'stokPelampung'      => $stokPelampung,
            'riwayatPelampung'   => $riwayatPelampung,
            'pelampungDipinjam'  => $pelampungDipinjam,
            'totalHariIni'       => $totalHariIni,
            'hargaSewaPelampung' => self::HARGA_SEWA_PELAMPUNG,
        ]);
    }

    // Simpan transaksi sewa pelampung
    public function simpanSewaPelampung(Request $request)
    {
        $request->validate([
            'jumlah'  => 'required|integer|min:1',
            'catatan' => 'nullable|string|max:100',
        ]);

        $stok = MasterStok::where('jenis', 'pelampung')->first();

        if (!$stok || $stok->tersedia < $request->jumlah) {
            return redirect()->route('kolam.pelampung')
                             ->with('error', 'Stok pelampung tidak cukup. Tersedia: ' . ($stok->tersedia ?? 0) . ' buah.');
        }

        $stok->decrement('tersedia', $request->jumlah);

        SewaPelampung::create([
            'user_id'      => auth()->id(),
            'jumlah'       => $request->jumlah,
            'catatan'      => $request->catatan,
            'harga_satuan' => self::HARGA_SEWA_PELAMPUNG,
            'total_bayar'  => self::HARGA_SEWA_PELAMPUNG * $request->jumlah,
            'status'       => 'dipinjam',
        ]);

        return redirect()->route('kolam.pelampung')
                         ->with('success', 'Sewa pelampung berhasil disimpan!');
    }

    // Tandai pelampung sudah dikembalikan
    public function tandaiKembali($id)
    {
        $sewa = SewaPelampung::findOrFail($id);

        if ($sewa->status === 'kembali') {
            return redirect()->route('kolam.pelampung')
                             ->with('error', 'Pelampung ini sudah ditandai kembali sebelumnya.');
        }

        $sewa->update([
            'status'        => 'kembali',
            'waktu_kembali' => now(),
        ]);

        $stok = MasterStok::where('jenis', 'pelampung')->first();
        if ($stok) {
            $stok->increment('tersedia', $sewa->jumlah);
        }

        return redirect()->route('kolam.pelampung')
                         ->with('success', 'Pelampung berhasil ditandai kembali.');
    }

    // Halaman Pakan Ikan
    public function pakanIkan()
    {
        $stokPakanIkan = MasterStok::where('jenis', 'pakan_ikan')->first();

        $riwayatPakanIkan = PakanIkan::where('user_id', auth()->id())
            ->where('titik_jual', 'kolam')
            ->whereDate('created_at', today())
            ->latest()
            ->get();

        $totalHariIni = $riwayatPakanIkan->sum('total_bayar');

        return view('kolam.pakan_ikan', [
            'stokPakanIkan'    => $stokPakanIkan,
            'riwayatPakanIkan' => $riwayatPakanIkan,
            'totalHariIni'     => $totalHariIni,
        ]);
    }

    // Simpan transaksi pakan ikan (titik jual: kolam)
    public function simpanPakanIkan(Request $request)
    {
        $request->validate([
            'jumlah_porsi' => 'required|integer|min:1',
        ]);

        $hasil = $this->pakanIkanService->jual(
            auth()->id(),
            'kolam',
            $request->jumlah_porsi
        );

        return redirect()->route('kolam.pakan_ikan')
                         ->with($hasil['success'] ? 'success' : 'error', $hasil['message']);
    }

    public function ikanHias()
{
    $totalHariIni = IkanHiasPenjualan::whereDate('created_at', today())->sum('total_bayar');

    $riwayatIkanHias = IkanHiasPenjualan::where('user_id', auth()->id())
        ->whereDate('created_at', today())
        ->orderByDesc('created_at')
        ->get();

    return view('kolam.ikan_hias', compact('totalHariIni', 'riwayatIkanHias'));
}

public function simpanIkanHias(Request $request)
{
    $request->validate([
        'jumlah_ikan' => 'required|integer|min:1',
    ]);

    $hargaSatuan = IkanHiasService::HARGA_PER_EKOR;
    $totalBayar = $request->jumlah_ikan * $hargaSatuan;

    IkanHiasPenjualan::create([
        'user_id'      => auth()->id(),
        'jumlah_ikan'  => $request->jumlah_ikan,
        'harga_satuan' => $hargaSatuan,
        'total_bayar'  => $totalBayar,
    ]);

    return redirect()->route('kolam.ikan_hias')->with('success', 'Penjualan ikan hias berhasil disimpan!');
}
}