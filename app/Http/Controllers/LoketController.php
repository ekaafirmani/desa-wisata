<?php

namespace App\Http\Controllers;

use App\Models\TiketMasuk;
use App\Models\MasterStok;
use App\Models\PakanIkan;
use App\Services\PakanIkanService;
use Illuminate\Http\Request;
use App\Models\SewaGazebo;

class LoketController extends Controller
{
    // Harga tiket per jenis kendaraan (tetap/hardcode)
    private const HARGA_TIKET = [
        'motor' => 3000,
        'mobil' => 7000,
        'bus' => 10000,
    ];

    protected PakanIkanService $pakanIkanService;

    public function __construct(PakanIkanService $pakanIkanService)
    {
        $this->pakanIkanService = $pakanIkanService;
    }
    // Halaman Gazebo
    public function gazebo()
    {
        $stokGazeboBesar = MasterStok::where('jenis', 'gazebo_besar')->first();
        $stokGazeboKecil = MasterStok::where('jenis', 'gazebo_kecil')->first();

        $nomorBesarTersedia = $this->nomorGazeboTersedia('besar', $stokGazeboBesar->total_stok ?? 0);
        $nomorKecilTersedia = $this->nomorGazeboTersedia('kecil', $stokGazeboKecil->total_stok ?? 0);

        $riwayatGazebo = SewaGazebo::where('user_id', auth()->id())
            ->where('titik_jual', 'loket')
            ->whereDate('created_at', today())
            ->latest()
            ->get();

        $gazeboDisewa = SewaGazebo::with('petugas')
            ->where('status', 'disewa')
            ->latest()
            ->get();

        $totalHariIni = $riwayatGazebo->sum('total_bayar');

        return view('loket.gazebo', [
            'stokGazeboBesar' => $stokGazeboBesar,
            'stokGazeboKecil' => $stokGazeboKecil,
            'nomorBesarTersedia' => $nomorBesarTersedia,
            'nomorKecilTersedia' => $nomorKecilTersedia,
            'riwayatGazebo' => $riwayatGazebo,
            'gazeboDisewa' => $gazeboDisewa,
            'totalHariIni' => $totalHariIni,
        ]);
    }

    // Simpan transaksi sewa gazebo (titik jual: loket)
    public function simpanGazebo(Request $request)
    {
        $request->validate([
            'jenis_gazebo' => 'required|in:besar,kecil',
            'jumlah' => 'required|integer|min:1',
            'nomor_gazebo' => 'required|array|min:1',
            'nomor_gazebo.*' => 'integer',
            'catatan' => 'nullable|string|max:100',
        ]);

        if (count($request->nomor_gazebo) != $request->jumlah) {
            return redirect()->route('loket.gazebo')
                ->with('error', 'Jumlah nomor gazebo yang dipilih harus sama dengan jumlah gazebo (' . $request->jumlah . ').');
        }

        $jenisStok = 'gazebo_' . $request->jenis_gazebo;
        $stok = MasterStok::where('jenis', $jenisStok)->first();

        if (!$stok || $stok->tersedia < $request->jumlah) {
            return redirect()->route('loket.gazebo')
                ->with('error', 'Stok gazebo ' . $request->jenis_gazebo . ' tidak cukup. Tersedia: ' . ($stok->tersedia ?? 0) . '.');
        }

        // Cegah nomor yang sudah dipinjam dipilih ulang
        $nomorTersedia = $this->nomorGazeboTersedia($request->jenis_gazebo, $stok->total_stok);
        $nomorTidakValid = array_diff($request->nomor_gazebo, $nomorTersedia);

        if (count($nomorTidakValid) > 0) {
            return redirect()->route('loket.gazebo')
                ->with('error', 'Nomor gazebo ' . implode(', ', $nomorTidakValid) . ' sudah dipinjam. Silakan pilih nomor lain.');
        }

        $stok->decrement('tersedia', $request->jumlah);

        $catatanNomor = 'No. ' . implode(', ', $request->nomor_gazebo);
        $catatanFinal = $catatanNomor . ($request->catatan ? ' | ' . $request->catatan : '');

        SewaGazebo::create([
            'user_id' => auth()->id(),
            'titik_jual' => 'loket',
            'jenis_gazebo' => $request->jenis_gazebo,
            'jumlah' => $request->jumlah,
            'catatan' => $catatanFinal,
            'harga_satuan' => $stok->harga_satuan,
            'total_bayar' => $stok->harga_satuan * $request->jumlah,
            'status' => 'disewa',
        ]);

        return redirect()->route('loket.gazebo')
            ->with('success', 'Sewa gazebo berhasil disimpan!');
    }

    public function tandaiKembaliGazebo($id)
    {
        $sewa = SewaGazebo::findOrFail($id);

        if ($sewa->status === 'kembali') {
            return redirect()->back()
                ->with('error', 'Gazebo ini sudah ditandai kembali sebelumnya.');
        }

        $sewa->update([
            'status' => 'kembali',
            'waktu_kembali' => now(),
        ]);

        $jenisStok = 'gazebo_' . $sewa->jenis_gazebo;
        $stok = MasterStok::where('jenis', $jenisStok)->first();
        if ($stok) {
            $stok->increment('tersedia', $sewa->jumlah);
        }

        return redirect()->back()
            ->with('success', 'Gazebo berhasil ditandai kembali.');
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
            'hargaTiket' => self::HARGA_TIKET,
            'riwayatHariIni' => $riwayatHariIni,
            'totalHariIni' => $totalHariIni,
        ]);
    }

    // Simpan transaksi tiket masuk baru
    public function simpan(Request $request)
    {
        $request->validate([
            'jenis_kendaraan' => 'required|in:motor,mobil,bus',
            'jumlah' => 'required|integer|min:1',
        ]);

        $hargaSatuan = self::HARGA_TIKET[$request->jenis_kendaraan];
        $totalBayar = $hargaSatuan * $request->jumlah;

        TiketMasuk::create([
            'user_id' => auth()->id(),
            'jenis_kendaraan' => $request->jenis_kendaraan,
            'jumlah' => $request->jumlah,
            'harga_satuan' => $hargaSatuan,
            'total_bayar' => $totalBayar,
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
            'stokPakanIkan' => $stokPakanIkan,
            'riwayatPakanIkan' => $riwayatPakanIkan,
            'totalHariIni' => $totalHariIni,
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

    private function nomorGazeboTersedia(string $jenisGazebo, int $totalStok): array
    {
        $semuaNomor = range(1, max($totalStok, 0));

        $nomorDipinjam = SewaGazebo::where('jenis_gazebo', $jenisGazebo)
            ->where('status', 'disewa')
            ->pluck('catatan')
            ->flatMap(function ($catatan) {
                if ($catatan && preg_match('/No\.\s*([\d,\s]+)/', $catatan, $match)) {
                    return array_map('trim', explode(',', $match[1]));
                }
                return [];
            })
            ->map(fn($n) => (int) $n)
            ->toArray();

        return array_values(array_diff($semuaNomor, $nomorDipinjam));
    }
}