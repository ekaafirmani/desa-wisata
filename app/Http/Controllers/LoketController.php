<?php

namespace App\Http\Controllers;

use App\Models\TiketMasuk;
use App\Models\MasterStok;
use App\Models\PakanIkan;
use App\Services\PakanIkanService;
use App\Models\Gasebo;
use App\Models\SewaGasebo;
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

    // Halaman Tiket Masuk
    public function tiket()
    {
        $riwayatQuery = TiketMasuk::where('user_id', auth()->id())
            ->whereDate('created_at', today());

        $totalHariIni = (clone $riwayatQuery)->sum('total_bayar');

        $riwayatHariIni = (clone $riwayatQuery)
            ->latest()
            ->paginate(5)
            ->withQueryString();

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

        $riwayatQuery = PakanIkan::where('user_id', auth()->id())
            ->where('titik_jual', 'loket')
            ->whereDate('created_at', today());

        $totalHariIni = (clone $riwayatQuery)->sum('total_bayar');

        $riwayatPakanIkan = (clone $riwayatQuery)
            ->latest()
            ->paginate(5)
            ->withQueryString();

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

    // Halaman sewa gasebo kecil
    public function gasebo()
    {
        $gaseboList = Gasebo::where('jenis', 'kecil')
                            ->where('aktif', true)
                            ->get();

        $sewaAktif = SewaGasebo::with('gasebo')
                        ->whereHas('gasebo', fn($q) => $q->where('jenis', 'kecil'))
                        ->where('status', 'aktif')
                        ->latest()
                        ->get();

        $riwayatQuery = SewaGasebo::with('gasebo')
            ->whereHas('gasebo', fn($q) => $q->where('jenis', 'kecil'))
            ->where('user_id', auth()->id())
            ->whereDate('created_at', today())
            ->where('status', 'selesai');

        $totalHariIni = (clone $riwayatQuery)->sum('total_bayar');

        $riwayatHariIni = (clone $riwayatQuery)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('loket.gasebo', compact(
            'gaseboList', 'sewaAktif', 'riwayatHariIni', 'totalHariIni'
        ));
    }

    // Simpan transaksi sewa gasebo kecil
    public function simpanGasebo(Request $request)
    {
        $request->validate([
            'gasebo_id'    => 'required|exists:gasebo,id',
            'nama_penyewa' => 'nullable|string|max:100',
            'durasi_jam'   => 'required|integer|min:1',
        ]);

        $gasebo = Gasebo::findOrFail($request->gasebo_id);

        if ($gasebo->sedangDisewa()) {
            return redirect()->route('loket.gasebo')
                            ->with('error', 'Gasebo ini sedang disewa.');
        }

        $totalBayar = $gasebo->harga_per_jam * $request->durasi_jam;
        $waktuMulai = now();
        $waktuSelesai = now()->addHours($request->durasi_jam);

        SewaGasebo::create([
            'user_id'       => auth()->id(),
            'gasebo_id'     => $gasebo->id,
            'nama_penyewa'  => $request->nama_penyewa,
            'durasi_jam'    => $request->durasi_jam,
            'harga_per_jam' => $gasebo->harga_per_jam,
            'total_bayar'   => $totalBayar,
            'waktu_mulai'   => $waktuMulai,
            'waktu_selesai' => $waktuSelesai,
            'status'        => 'aktif',
            'catatan'       => $request->catatan,
        ]);

        // Update status gasebo jadi disewa
        $gasebo->update(['status' => 'disewa']);

        return redirect()->route('loket.gasebo')
                        ->with('success', 'Sewa gasebo berhasil dicatat!');
    }

    // Tandai sewa gasebo selesai
    public function selesaiGasebo($id)
    {
        $sewa = SewaGasebo::findOrFail($id);
        $sewa->update([
            'status'        => 'selesai',
            'waktu_selesai' => now(),
        ]);

        // Update status gasebo kembali tersedia
        $sewa->gasebo->update(['status' => 'tersedia']);

        return redirect()->route('loket.gasebo')
                        ->with('success', 'Sewa gasebo berhasil diselesaikan!');
    }
}