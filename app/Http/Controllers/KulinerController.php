<?php

namespace App\Http\Controllers;

use App\Models\MenuKuliner;
use App\Models\TransaksiKuliner;
use App\Models\DetailKuliner;
use App\Models\MasterStok;
use App\Models\Gasebo;
use App\Models\SewaGasebo;
use App\Services\PakanIkanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KulinerController extends Controller
{
    protected PakanIkanService $pakanIkanService;

    public function __construct(PakanIkanService $pakanIkanService)
    {
        $this->pakanIkanService = $pakanIkanService;
    }

    // Halaman utama: form transaksi (keranjang) + riwayat hari ini
    public function dashboard()
    {
        $menuList = MenuKuliner::where('tersedia', true)->get();

        $riwayatHariIni = TransaksiKuliner::with('detail.menu')
            ->where('user_id', auth()->id())
            ->whereDate('created_at', today())
            ->latest()
            ->get();

        $totalHariIni = $riwayatHariIni->sum('total_bayar');

        return view('kuliner.dashboard', [
            'menuList'       => $menuList,
            'riwayatHariIni' => $riwayatHariIni,
            'totalHariIni'   => $totalHariIni,
        ]);
    }

    // Simpan transaksi kuliner (bisa banyak menu sekaligus)
    public function simpanTransaksi(Request $request)
    {
        $request->validate([
            'menu_id'   => 'required|array|min:1',
            'menu_id.*' => 'required|exists:menu_kuliner,id',
            'jumlah'    => 'required|array',
            'jumlah.*'  => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $totalBayar = 0;
            $detailItems = [];

            foreach ($request->menu_id as $i => $menuId) {
                $jumlah = (int) $request->jumlah[$i];
                $menu = MenuKuliner::find($menuId);

                if (!$menu || $jumlah < 1) {
                    continue;
                }

                $subtotal = $menu->harga * $jumlah;
                $totalBayar += $subtotal;

                $detailItems[] = [
                    'menu_id'  => $menu->id,
                    'jumlah'   => $jumlah,
                    'subtotal' => $subtotal,
                ];
            }

            if (empty($detailItems)) {
                abort(422, 'Tidak ada item valid dalam transaksi.');
            }

            $transaksi = TransaksiKuliner::create([
                'user_id'     => auth()->id(),
                'total_bayar' => $totalBayar,
            ]);

            foreach ($detailItems as $item) {
                DetailKuliner::create([
                    'transaksi_id' => $transaksi->id,
                    'menu_id'      => $item['menu_id'],
                    'jumlah'       => $item['jumlah'],
                    'subtotal'     => $item['subtotal'],
                ]);
            }
        });

        return redirect()->route('kuliner.dashboard')
                         ->with('success', 'Transaksi kuliner berhasil disimpan!');
    }

    // Halaman Pakan Ikan
    public function pakanIkan()
    {
        $stokPakanIkan = MasterStok::where('jenis', 'pakan_ikan')->first();

        $riwayatPakanIkan = \App\Models\PakanIkan::where('user_id', auth()->id())
            ->where('titik_jual', 'kuliner')
            ->whereDate('created_at', today())
            ->latest()
            ->get();

        $totalHariIni = $riwayatPakanIkan->sum('total_bayar');

        return view('kuliner.pakan_ikan', [
            'stokPakanIkan'    => $stokPakanIkan,
            'riwayatPakanIkan' => $riwayatPakanIkan,
            'totalHariIni'     => $totalHariIni,
        ]);
    }

    // Simpan transaksi pakan ikan (titik jual: kuliner)
    public function simpanPakanIkan(Request $request)
    {
        $request->validate([
            'jumlah_porsi' => 'required|integer|min:1',
        ]);

        $hasil = $this->pakanIkanService->jual(
            auth()->id(),
            'kuliner',
            $request->jumlah_porsi
        );

        return redirect()->route('kuliner.pakan_ikan')
                         ->with($hasil['success'] ? 'success' : 'error', $hasil['message']);
    }

    // Halaman daftar menu (dikelola oleh petugas kuliner)
    public function menu()
    {
        $menu = MenuKuliner::latest()->get();
        return view('kuliner.menu.index', compact('menu'));
    }

    // Form tambah menu baru
    public function tambahMenu()
    {
        return view('kuliner.menu.tambah');
    }

    // Simpan menu baru (oleh petugas kuliner)
    public function simpanMenu(Request $request)
    {
        $request->validate([
            'nama_menu' => 'required|string|max:100',
            'harga'     => 'required|integer|min:0',
            'kategori'  => 'required|in:makanan,minuman,snack',
        ]);

        MenuKuliner::create($request->all());

        return redirect()->route('kuliner.menu')
                         ->with('success', 'Menu berhasil ditambahkan!');
    }

    // Tandai menu tidak tersedia
    public function nonaktifkanMenu($id)
    {
        $menu = MenuKuliner::findOrFail($id);
        $menu->update(['tersedia' => false]);

        return redirect()->route('kuliner.menu')
                         ->with('success', 'Menu ditandai tidak tersedia.');
    }

    // Tandai menu tersedia kembali
    public function aktifkanMenu($id)
    {
        $menu = MenuKuliner::findOrFail($id);
        $menu->update(['tersedia' => true]);

        return redirect()->route('kuliner.menu')
                         ->with('success', 'Menu ditandai tersedia kembali.');
    }

    // Halaman sewa gasebo besar
    public function gasebo()
    {
        $gaseboList = Gasebo::where('jenis', 'besar')
                            ->where('aktif', true)
                            ->get();

        $sewaAktif = SewaGasebo::with('gasebo')
                        ->whereHas('gasebo', fn($q) => $q->where('jenis', 'besar'))
                        ->where('status', 'aktif')
                        ->latest()
                        ->get();

        $riwayatHariIni = SewaGasebo::with('gasebo')
                            ->whereHas('gasebo', fn($q) => $q->where('jenis', 'besar'))
                            ->where('user_id', auth()->id())
                            ->whereDate('created_at', today())
                            ->where('status', 'selesai')
                            ->latest()
                            ->get();

        $totalHariIni = $riwayatHariIni->sum('total_bayar');

        return view('kuliner.gasebo', compact(
            'gaseboList', 'sewaAktif', 'riwayatHariIni', 'totalHariIni'
        ));
    }

    // Simpan transaksi sewa gasebo besar
    public function simpanGasebo(Request $request)
    {
        $request->validate([
            'gasebo_id'    => 'required|exists:gasebo,id',
            'nama_penyewa' => 'nullable|string|max:100',
            'durasi_jam'   => 'required|integer|min:3', // minimal 3 jam untuk gasebo besar
        ]);

        $gasebo = Gasebo::findOrFail($request->gasebo_id);

        if ($gasebo->sedangDisewa()) {
            return redirect()->route('kuliner.gasebo')
                            ->with('error', 'Gasebo ini sedang disewa.');
        }

        $totalBayar = $gasebo->harga_per_jam * $request->durasi_jam;

        SewaGasebo::create([
            'user_id'       => auth()->id(),
            'gasebo_id'     => $gasebo->id,
            'nama_penyewa'  => $request->nama_penyewa,
            'durasi_jam'    => $request->durasi_jam,
            'harga_per_jam' => $gasebo->harga_per_jam,
            'total_bayar'   => $totalBayar,
            'waktu_mulai'   => now(),
            'waktu_selesai' => now()->addHours($request->durasi_jam),
            'status'        => 'aktif',
            'catatan'       => $request->catatan,
        ]);

        $gasebo->update(['status' => 'disewa']);

        return redirect()->route('kuliner.gasebo')
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

        $sewa->gasebo->update(['status' => 'tersedia']);

        return redirect()->route('kuliner.gasebo')
                        ->with('success', 'Sewa gasebo berhasil diselesaikan!');
    }
}