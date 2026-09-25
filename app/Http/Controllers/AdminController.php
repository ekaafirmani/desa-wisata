<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\TiketMasuk;
use App\Models\TransaksiTubing;
use App\Models\TiketKolam;
use App\Models\SewaPelampung;
use App\Models\PakanIkan;
use App\Models\TransaksiKuliner;
use App\Models\PaketTubing;
use App\Models\MenuKuliner;
use App\Models\MasterStok;
use App\Models\LapakUmkm;
use App\Models\PembayaranUmkm;
use App\Models\PaketWisata;
use App\Models\TransaksiPaketWisata;
use App\Models\Gasebo;
use App\Models\SewaGasebo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\IkanHiasPenjualan;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\LaporanExport;

class AdminController extends Controller
{
    // Halaman dashboard utama admin
    public function dashboard()
    {
        $today = today();
        $awalBulan = now()->startOfMonth();
        $akhirBulan = now()->endOfMonth();

        // Label & ikon kategori (dipakai bareng untuk Hari Ini & Bulan Ini)
        $kategoriLabel = [
            'parkir'            => ['icon' => '🅿️', 'label' => 'Parkir'],
            'kolam_renang'      => ['icon' => '🏊', 'label' => 'Kolam Renang'],
            'tubing_mini'       => ['icon' => '🚣', 'label' => 'Batur Tubing Mini'],
            'tubing_dewasa'     => ['icon' => '🌊', 'label' => 'Sukan River Tubing'],
            'warung_pokdarwis'  => ['icon' => '🍽️', 'label' => 'Warung Pokdarwis'],
            'umkm'              => ['icon' => '🏪', 'label' => 'UMKM'],
            'pelet'             => ['icon' => '🐟', 'label' => 'Pelet'],
            'ikan_hias'         => ['icon' => '🐠', 'label' => 'Ikan Hias'],
            'gasebo'            => ['icon' => '🏠', 'label' => 'Gasebo'],
            'paket_wisata'      => ['icon' => '🎫', 'label' => 'Paket Wisata'],
        ];

        // === Pendapatan HARI INI per kategori ===
        $pendapatanHariIni = [
            'parkir' => TiketMasuk::whereDate('created_at', $today)->sum('total_bayar'),

            'kolam_renang' => TiketKolam::whereDate('created_at', $today)->sum('total_bayar') +
                SewaPelampung::whereDate('created_at', $today)->sum('total_bayar'),

            'tubing_mini' => TransaksiTubing::whereDate('created_at', $today)
                ->whereHas('paket', fn($q) => $q->where('jenis', 'mini'))
                ->sum('total_bayar'),

            'tubing_dewasa' => TransaksiTubing::whereDate('created_at', $today)
                ->whereHas('paket', fn($q) => $q->where('jenis', 'dewasa'))
                ->sum('total_bayar'),

            'warung_pokdarwis' => TransaksiKuliner::whereDate('created_at', $today)->sum('total_bayar'),

            'umkm' => PembayaranUmkm::whereDate('created_at', $today)->sum('jumlah_bayar'),

            'pelet' => PakanIkan::whereDate('created_at', $today)->sum('total_bayar'),

            'ikan_hias' => IkanHiasPenjualan::whereDate('created_at', $today)->sum('total_bayar'),

            'gasebo' => SewaGasebo::whereDate('created_at', $today)->sum('total_bayar'),

            'paket_wisata' => TransaksiPaketWisata::whereDate('created_at', $today)->sum('total_bayar'),
        ];
        $totalHariIni = array_sum($pendapatanHariIni);

        // === Pendapatan BULAN INI per kategori ===
        $pendapatanBulanIni = [
            'parkir' => TiketMasuk::whereBetween('created_at', [$awalBulan, $akhirBulan])->sum('total_bayar'),

            'kolam_renang' => TiketKolam::whereBetween('created_at', [$awalBulan, $akhirBulan])->sum('total_bayar') +
                SewaPelampung::whereBetween('created_at', [$awalBulan, $akhirBulan])->sum('total_bayar'),

            'tubing_mini' => TransaksiTubing::whereBetween('created_at', [$awalBulan, $akhirBulan])
                ->whereHas('paket', fn($q) => $q->where('jenis', 'mini'))
                ->sum('total_bayar'),

            'tubing_dewasa' => TransaksiTubing::whereBetween('created_at', [$awalBulan, $akhirBulan])
                ->whereHas('paket', fn($q) => $q->where('jenis', 'dewasa'))
                ->sum('total_bayar'),

            'warung_pokdarwis' => TransaksiKuliner::whereBetween('created_at', [$awalBulan, $akhirBulan])->sum('total_bayar'),

            'umkm' => PembayaranUmkm::whereBetween('created_at', [$awalBulan, $akhirBulan])->sum('jumlah_bayar'),

            'pelet' => PakanIkan::whereBetween('created_at', [$awalBulan, $akhirBulan])->sum('total_bayar'),

            'ikan_hias' => IkanHiasPenjualan::whereBetween('created_at', [$awalBulan, $akhirBulan])->sum('total_bayar'),

            'gasebo' => SewaGasebo::whereBetween('created_at', [$awalBulan, $akhirBulan])->sum('total_bayar'),

            'paket_wisata' => TransaksiPaketWisata::whereBetween('created_at', [$awalBulan, $akhirBulan])->sum('total_bayar'),
        ];
        $totalBulanIni = array_sum($pendapatanBulanIni);

        // === Total petugas aktif ===
        $totalPetugas = User::where('role', '!=', 'admin')
            ->where('aktif', true)
            ->count();

        // === Stok hampir habis (tersedia <= 5) ===
        $stokHampirHabis = MasterStok::where('tersedia', '<=', 5)->get();

        // === Aktivitas terkini (10 transaksi terakhir hari ini) ===
        $aktivitasTerkini = collect();

        $aktivitasTerkini = $aktivitasTerkini->merge(
            TiketMasuk::whereDate('created_at', $today)
                ->latest()->limit(5)
                ->get()->map(fn($t) => [
                    'waktu' => $t->created_at->format('H:i'),
                    'kategori' => 'Parkir',
                    'detail' => ucfirst($t->jenis_kendaraan) . ' x' . $t->jumlah,
                    'total' => $t->total_bayar,
                ])
        );

        $aktivitasTerkini = $aktivitasTerkini->merge(
            TransaksiTubing::with('paket')->whereDate('created_at', $today)
                ->whereHas('paket', fn($q) => $q->where('jenis', 'mini'))
                ->latest()->limit(5)
                ->get()->map(fn($t) => [
                    'waktu' => $t->created_at->format('H:i'),
                    'kategori' => 'Batur Tubing Mini',
                    'detail' => ($t->paket->nama_paket ?? '-') . ' x' . $t->jumlah_peserta . ' org',
                    'total' => $t->total_bayar,
                ])
        );

        $aktivitasTerkini = $aktivitasTerkini->merge(
            TransaksiTubing::with('paket')->whereDate('created_at', $today)
                ->whereHas('paket', fn($q) => $q->where('jenis', 'dewasa'))
                ->latest()->limit(5)
                ->get()->map(fn($t) => [
                    'waktu' => $t->created_at->format('H:i'),
                    'kategori' => 'Sukan River Tubing',
                    'detail' => ($t->paket->nama_paket ?? '-') . ' x' . $t->jumlah_peserta . ' org',
                    'total' => $t->total_bayar,
                ])
        );

        $aktivitasTerkini = $aktivitasTerkini->merge(
            TiketKolam::whereDate('created_at', $today)
                ->latest()->limit(5)
                ->get()->map(fn($t) => [
                    'waktu' => $t->created_at->format('H:i'),
                    'kategori' => 'Kolam Renang',
                    'detail' => $t->jumlah_orang . ' orang',
                    'total' => $t->total_bayar,
                ])
        );

        $aktivitasTerkini = $aktivitasTerkini->merge(
            TransaksiKuliner::whereDate('created_at', $today)
                ->latest()->limit(5)
                ->get()->map(fn($t) => [
                    'waktu' => $t->created_at->format('H:i'),
                    'kategori' => 'Warung Pokdarwis',
                    'detail' => 'Transaksi #' . $t->id,
                    'total' => $t->total_bayar,
                ])
        );

        $aktivitasTerkini = $aktivitasTerkini->merge(
            PembayaranUmkm::with('lapak')->whereDate('created_at', $today)
                ->latest()->limit(5)
                ->get()->map(fn($t) => [
                    'waktu' => $t->created_at->format('H:i'),
                    'kategori' => 'UMKM',
                    'detail' => $t->lapak->nama_lapak ?? ('Lapak #' . $t->lapak_id),
                    'total' => $t->jumlah_bayar,
                ])
        );

        $aktivitasTerkini = $aktivitasTerkini->merge(
            PakanIkan::whereDate('created_at', $today)
                ->latest()->limit(5)
                ->get()->map(fn($t) => [
                    'waktu' => $t->created_at->format('H:i'),
                    'kategori' => 'Pelet',
                    'detail' => $t->jumlah_porsi . ' porsi (' . $t->titik_jual . ')',
                    'total' => $t->total_bayar,
                ])
        );

        $aktivitasTerkini = $aktivitasTerkini->merge(
            IkanHiasPenjualan::whereDate('created_at', $today)
                ->latest()->limit(5)
                ->get()->map(fn($t) => [
                    'waktu' => $t->created_at->format('H:i'),
                    'kategori' => 'Ikan Hias',
                    'detail' => $t->jumlah_ikan . ' ekor',
                    'total' => $t->total_bayar,
                ])
        );

        $aktivitasTerkini = $aktivitasTerkini->merge(
            SewaGasebo::with('gasebo')->whereDate('created_at', $today)
                ->latest()->limit(5)
                ->get()->map(fn($t) => [
                    'waktu' => $t->created_at->format('H:i'),
                    'kategori' => 'Gasebo',
                    'detail' => $t->gasebo->nama_gasebo ?? '-',
                    'total' => $t->total_bayar,
                ])
        );

        $aktivitasTerkini = $aktivitasTerkini->merge(
            TransaksiPaketWisata::with('paket')->whereDate('created_at', $today)
                ->latest()->limit(5)
                ->get()->map(fn($t) => [
                    'waktu' => $t->created_at->format('H:i'),
                    'kategori' => 'Paket Wisata',
                    'detail' => ($t->paket->nama_paket ?? '-') . ' x' . $t->jumlah_orang . ' org',
                    'total' => $t->total_bayar,
                ])
        );

        $aktivitasTerkini = $aktivitasTerkini->sortByDesc('waktu')->take(10)->values();

        // === Data grafik HARIAN (30 hari terakhir) ===
        $grafikHarian = [];
        for ($i = 29; $i >= 0; $i--) {
            $tanggal = now()->subDays($i)->toDateString();
            $label = now()->subDays($i)->format('d M');

            $total = TiketMasuk::whereDate('created_at', $tanggal)->sum('total_bayar')
                + TiketKolam::whereDate('created_at', $tanggal)->sum('total_bayar')
                + SewaPelampung::whereDate('created_at', $tanggal)->sum('total_bayar')
                + TransaksiTubing::whereDate('created_at', $tanggal)->sum('total_bayar')
                + TransaksiKuliner::whereDate('created_at', $tanggal)->sum('total_bayar')
                + PembayaranUmkm::whereDate('created_at', $tanggal)->sum('jumlah_bayar')
                + PakanIkan::whereDate('created_at', $tanggal)->sum('total_bayar')
                + IkanHiasPenjualan::whereDate('created_at', $tanggal)->sum('total_bayar')
                + SewaGasebo::whereDate('created_at', $tanggal)->sum('total_bayar')
                + TransaksiPaketWisata::whereDate('created_at', $tanggal)->sum('total_bayar');

            $grafikHarian[] = ['label' => $label, 'total' => $total];
        }

        // === Data grafik BULANAN (12 bulan terakhir) ===
        $grafikBulanan = [];
        for ($i = 11; $i >= 0; $i--) {
            $bulanAwal = now()->subMonths($i)->startOfMonth();
            $bulanAkhir = now()->subMonths($i)->endOfMonth();
            $label = now()->subMonths($i)->translatedFormat('M Y');

            $total = TiketMasuk::whereBetween('created_at', [$bulanAwal, $bulanAkhir])->sum('total_bayar')
                + TiketKolam::whereBetween('created_at', [$bulanAwal, $bulanAkhir])->sum('total_bayar')
                + SewaPelampung::whereBetween('created_at', [$bulanAwal, $bulanAkhir])->sum('total_bayar')
                + TransaksiTubing::whereBetween('created_at', [$bulanAwal, $bulanAkhir])->sum('total_bayar')
                + TransaksiKuliner::whereBetween('created_at', [$bulanAwal, $bulanAkhir])->sum('total_bayar')
                + PembayaranUmkm::whereBetween('created_at', [$bulanAwal, $bulanAkhir])->sum('jumlah_bayar')
                + PakanIkan::whereBetween('created_at', [$bulanAwal, $bulanAkhir])->sum('total_bayar')
                + IkanHiasPenjualan::whereBetween('created_at', [$bulanAwal, $bulanAkhir])->sum('total_bayar')
                + SewaGasebo::whereBetween('created_at', [$bulanAwal, $bulanAkhir])->sum('total_bayar')
                + TransaksiPaketWisata::whereBetween('created_at', [$bulanAwal, $bulanAkhir])->sum('total_bayar');

            $grafikBulanan[] = ['label' => $label, 'total' => $total];
        }

        return view('admin.dashboard', compact(
            'kategoriLabel',
            'pendapatanHariIni',
            'totalHariIni',
            'pendapatanBulanIni',
            'totalBulanIni',
            'totalPetugas',
            'stokHampirHabis',
            'aktivitasTerkini',
            'grafikHarian',
            'grafikBulanan'
        ));
    }

    // Halaman daftar semua petugas
    public function petugas()
    {
        $petugas = User::where('role', '!=', 'admin')->get();
        return view('admin.petugas.index', compact('petugas'));
    }

    //Form tambah petugas baru
    public function tambahPetugas()
    {
        return view('admin.petugas.tambah');
    }

    //Simpa petugas baru ke database
    public function simpanPetugas(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'role' => 'required|in:loket,tubing_mini,tubing_dewasa,kolam,kuliner,paket_wisata',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'aktif' => true,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.petugas')
            ->with('success', 'Petugas berhasil ditambahkan!');
    }

    // Form edit petugas
    public function editPetugas($id)
    {
        $petugas = User::findOrFail($id);
        return view('admin.petugas.edit', compact('petugas'));
    }

    // Update data petugas
    public function updatePetugas(Request $request, $id)
    {
        $petugas = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $petugas->id,
            'password' => 'nullable|min:6',
            'role' => 'required|in:loket,tubing_mini,tubing_dewasa,kolam,kuliner,paket_wisata',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $petugas->update($data);

        return redirect()->route('admin.petugas')
            ->with('success', 'Data petugas berhasil diperbarui!');
    }

    // Nonaktifkan akun petugas
    public function nonaktifkanPetugas($id)
    {
        $petugas = User::findOrFail($id);
        $petugas->update(['aktif' => false]);

        return redirect()->route('admin.petugas')
            ->with('success', 'Akun petugas berhasil dinonaktifkan!');
    }

    // Aktifkan kembali akun petugas
    public function aktifkanPetugas($id)
    {
        $petugas = User::findOrFail($id);
        $petugas->update(['aktif' => true]);

        return redirect()->route('admin.petugas')
            ->with('success', 'Akun petugas berhasil diaktifkan!');
    }

    // Halaman kelola paket tubing
    public function paketTubing()
    {
        $paket = PaketTubing::all();
        return view('admin.paket_tubing.index', compact('paket'));
    }

    // Form tambah paket tubing baru
    public function tambahPaketTubing()
    {
        return view('admin.paket_tubing.tambah');
    }

    // Simpan paket tubing baru
    public function simpanPaketTubing(Request $request)
    {
        $request->validate([
            'nama_paket' => 'required|string|max:100',
            'jenis' => 'required|in:mini,dewasa',
            'fasilitas' => 'required|string',
            'harga' => 'required|integer|min:0',
        ]);

        PaketTubing::create($request->all());

        return redirect()->route('admin.paket_tubing')
            ->with('success', 'Paket tubing berhasil ditambahkan!');
    }

    // Form edit paket tubing
    public function editPaketTubing($id)
    {
        $paket = PaketTubing::findOrFail($id);
        return view('admin.paket_tubing.edit', compact('paket'));
    }

    // Update paket tubing
    public function updatePaketTubing(Request $request, $id)
    {
        $request->validate([
            'nama_paket' => 'required|string|max:100',
            'jenis' => 'required|in:mini,dewasa',
            'fasilitas' => 'required|string',
            'harga' => 'required|integer|min:0',
        ]);

        $paket = PaketTubing::findOrFail($id);
        $paket->update($request->all());

        return redirect()->route('admin.paket_tubing')
            ->with('success', 'Paket tubing berhasil diperbarui!');
    }

    // Tandai paket tidak tersedia
    public function nonaktifkanPaketTubing($id)
    {
        $paket = PaketTubing::findOrFail($id);
        $paket->update(['aktif' => false]);

        return redirect()->route('admin.paket_tubing')
            ->with('success', 'Paket ditandai tidak tersedia.');
    }

    // Tandai paket tersedia kembali
    public function aktifkanPaketTubing($id)
    {
        $paket = PaketTubing::findOrFail($id);
        $paket->update(['aktif' => true]);

        return redirect()->route('admin.paket_tubing')
            ->with('success', 'Paket ditandai tersedia kembali.');
    }


    // Halaman kelola menu kuliner
    public function menuKuliner()
    {
        $menu = MenuKuliner::all();
        return view('admin.menu_kuliner.index', compact('menu'));
    }

    // Form tambah menu kuliner
    public function tambahMenuKuliner()
    {
        return view('admin.menu_kuliner.tambah');
    }

    // Simpan menu kuliner baru
    public function simpanMenuKuliner(Request $request)
    {
        $request->validate([
            'nama_menu' => 'required|string|max:100',
            'harga' => 'required|integer|min:0',
            'kategori' => 'required|in:makanan,minuman,snack',
        ]);

        MenuKuliner::create($request->all());

        return redirect()->route('admin.menu_kuliner')
            ->with('success', 'Menu berhasil ditambahkan!');
    }

    // Form edit menu kuliner
    public function editMenuKuliner($id)
    {
        $menu = MenuKuliner::findOrFail($id);
        return view('admin.menu_kuliner.edit', compact('menu'));
    }

    // Update menu kuliner
    public function updateMenuKuliner(Request $request, $id)
    {
        $request->validate([
            'nama_menu' => 'required|string|max:100',
            'harga' => 'required|integer|min:0',
            'kategori' => 'required|in:makanan,minuman,snack',
        ]);

        $menu = MenuKuliner::findOrFail($id);
        $menu->update($request->all());

        return redirect()->route('admin.menu_kuliner')
            ->with('success', 'Menu berhasil diperbarui!');
    }

    // Tandai menu tidak tersedia
    public function nonaktifkanMenuKuliner($id)
    {
        $menu = MenuKuliner::findOrFail($id);
        $menu->update(['tersedia' => false]);

        return redirect()->route('admin.menu_kuliner')
            ->with('success', 'Menu ditandai tidak tersedia.');
    }

    // Tandai menu tersedia kembali
    public function aktifkanMenuKuliner($id)
    {
        $menu = MenuKuliner::findOrFail($id);
        $menu->update(['tersedia' => true]);

        return redirect()->route('admin.menu_kuliner')
            ->with('success', 'Menu ditandai tersedia kembali.');
    }

    // Halaman kelola stok
    public function stok()
    {
        $stok = MasterStok::all();
        return view('admin.stok.index', compact('stok'));
    }

    // Form tambah jenis stok baru
    public function tambahStok()
    {
        // Hanya tampilkan jenis yang belum punya record
        $jenisTerpakai = MasterStok::pluck('jenis')->toArray();
        $semuaJenis = ['pelampung', 'pakan_ikan'];
        $jenisTersedia = array_diff($semuaJenis, $jenisTerpakai);

        return view('admin.stok.tambah', compact('jenisTersedia'));
    }

    // Simpan jenis stok baru
    public function simpanStok(Request $request)
    {
        $request->validate([
            'jenis' => 'required|in:pelampung,pakan_ikan|unique:master_stok,jenis',
            'total_stok' => 'required|integer|min:0',
            'harga_satuan' => 'required|integer|min:0',
        ]);

        MasterStok::create([
            'jenis' => $request->jenis,
            'total_stok' => $request->total_stok,
            'tersedia' => $request->total_stok,
            'harga_satuan' => $request->harga_satuan,
        ]);

        return redirect()->route('admin.stok')
            ->with('success', 'Stok baru berhasil ditambahkan!');
    }

    // Update stok
    public function updateStok(Request $request, $id)
    {
        $request->validate([
            'total_stok' => 'required|integer|min:0',
            'harga_satuan' => 'required|integer|min:0',
        ]);

        $stok = MasterStok::findOrFail($id);
        $selisih = $request->total_stok - $stok->total_stok;
        $stok->update([
            'total_stok' => $request->total_stok,
            'tersedia' => $stok->tersedia + $selisih,
            'harga_satuan' => $request->harga_satuan,
        ]);

        return redirect()->route('admin.stok')
            ->with('success', 'Stok berhasil diperbarui!');
    }

    // Kumpulkan data laporan (dipakai bareng oleh web, PDF, Excel)
    private function generateLaporanData(Request $request): array
    {
        $filterKategori = $request->get('kategori', 'semua');
        $filterTipe = $request->get('tipe', 'hari');
        $filterTanggal = $request->get('tanggal', today()->toDateString());
        $filterBulan = $request->get('bulan', now()->format('Y-m'));

        if ($filterTipe === 'bulan') {
            $dari = \Carbon\Carbon::parse($filterBulan . '-01')->startOfMonth();
            $sampai = \Carbon\Carbon::parse($filterBulan . '-01')->endOfMonth();
            $labelPeriode = 'Bulan ' . \Carbon\Carbon::parse($filterBulan)->translatedFormat('F Y');
        } else {
            $dari = \Carbon\Carbon::parse($filterTanggal)->startOfDay();
            $sampai = \Carbon\Carbon::parse($filterTanggal)->endOfDay();
            $labelPeriode = \Carbon\Carbon::parse($filterTanggal)->translatedFormat('l, d F Y');
        }

        $data = [];

        if (in_array($filterKategori, ['semua', 'tiket_masuk'])) {
            $tiket = TiketMasuk::with('petugas')
                ->whereBetween('created_at', [$dari, $sampai])
                ->latest()->get();

            $data['tiket_masuk'] = [
                'label' => '🎫 Tiket Masuk',
                'label_bersih' => 'Tiket Masuk',
                'total' => $tiket->sum('total_bayar'),
                'kolom' => ['Waktu', 'Petugas', 'Jenis Kendaraan', 'Jumlah', 'Harga Satuan', 'Total'],
                'baris' => $tiket->map(fn($t) => [
                    $t->created_at->format('d/m H:i'),
                    $t->petugas->name ?? '-',
                    ucfirst($t->jenis_kendaraan),
                    $t->jumlah,
                    'Rp ' . number_format($t->harga_satuan, 0, ',', '.'),
                    'Rp ' . number_format($t->total_bayar, 0, ',', '.'),
                ]),
            ];
        }

        if (in_array($filterKategori, ['semua', 'tubing'])) {
            $tubing = TransaksiTubing::with('petugas', 'paket')
                ->whereBetween('created_at', [$dari, $sampai])
                ->latest()->get();

            $data['tubing'] = [
                'label' => '🚣 Tubing',
                'label_bersih' => 'Tubing',
                'total' => $tubing->sum('total_bayar'),
                'kolom' => ['Waktu', 'Petugas', 'Paket', 'Peserta', 'Total'],
                'baris' => $tubing->map(fn($t) => [
                    $t->created_at->format('d/m H:i'),
                    $t->petugas->name ?? '-',
                    $t->paket->nama_paket ?? '-',
                    $t->jumlah_peserta . ' orang',
                    'Rp ' . number_format($t->total_bayar, 0, ',', '.'),
                ]),
            ];
        }

        if (in_array($filterKategori, ['semua', 'kolam'])) {
            $tiketKolam = TiketKolam::with('petugas')
                ->whereBetween('created_at', [$dari, $sampai])
                ->latest()->get();

            $sewaPelampung = SewaPelampung::with('petugas')
                ->whereBetween('created_at', [$dari, $sampai])
                ->latest()->get();

            $totalKolam = $tiketKolam->sum('total_bayar') + $sewaPelampung->sum('total_bayar');

            $barisKolam = collect();
            $barisKolam = $barisKolam->merge($tiketKolam->map(fn($t) => [
                $t->created_at->format('d/m H:i'),
                $t->petugas->name ?? '-',
                'Tiket Kolam',
                $t->jumlah_orang . ' orang',
                'Rp ' . number_format($t->harga_satuan, 0, ',', '.'),
                'Rp ' . number_format($t->total_bayar, 0, ',', '.'),
            ]));
            $barisKolam = $barisKolam->merge($sewaPelampung->map(fn($t) => [
                $t->created_at->format('d/m H:i'),
                $t->petugas->name ?? '-',
                'Sewa Pelampung',
                $t->jumlah . ' buah',
                'Rp ' . number_format($t->harga_satuan, 0, ',', '.'),
                'Rp ' . number_format($t->total_bayar, 0, ',', '.'),
            ]));

            $data['kolam'] = [
                'label' => '🏊 Kolam',
                'label_bersih' => 'Kolam',
                'total' => $totalKolam,
                'kolom' => ['Waktu', 'Petugas', 'Jenis', 'Jumlah', 'Harga Satuan', 'Total'],
                'baris' => $barisKolam->sortByDesc(fn($b) => $b[0])->values(),
            ];
        }

        if (in_array($filterKategori, ['semua', 'kuliner'])) {
            $kuliner = TransaksiKuliner::with('petugas', 'detail.menu')
                ->whereBetween('created_at', [$dari, $sampai])
                ->latest()->get();

            $data['kuliner'] = [
                'label' => '🍽️ Kuliner',
                'label_bersih' => 'Kuliner',
                'total' => $kuliner->sum('total_bayar'),
                'kolom' => ['Waktu', 'Petugas', 'Item', 'Total'],
                'baris' => $kuliner->map(fn($t) => [
                    $t->created_at->format('d/m H:i'),
                    $t->petugas->name ?? '-',
                    $t->detail->map(fn($d) => ($d->menu->nama_menu ?? '-') . ' x' . $d->jumlah)->implode(', '),
                    'Rp ' . number_format($t->total_bayar, 0, ',', '.'),
                ]),
            ];
        }

        if (in_array($filterKategori, ['semua', 'pakan_ikan'])) {
            $pakanIkan = PakanIkan::with('petugas')
                ->whereBetween('created_at', [$dari, $sampai])
                ->latest()->get();

            $data['pakan_ikan'] = [
                'label' => '🐟 Pakan Ikan',
                'label_bersih' => 'Pakan Ikan',
                'total' => $pakanIkan->sum('total_bayar'),
                'kolom' => ['Waktu', 'Petugas', 'Titik Jual', 'Porsi', 'Harga Satuan', 'Total'],
                'baris' => $pakanIkan->map(fn($t) => [
                    $t->created_at->format('d/m H:i'),
                    $t->petugas->name ?? '-',
                    ucfirst($t->titik_jual),
                    $t->jumlah_porsi . ' porsi',
                    'Rp ' . number_format($t->harga_satuan, 0, ',', '.'),
                    'Rp ' . number_format($t->total_bayar, 0, ',', '.'),
                ]),
            ];
        }
        
        if (in_array($filterKategori, ['semua', 'gasebo'])) {
            $gasebo = SewaGasebo::with(['petugas', 'gasebo'])
                ->whereBetween('created_at', [$dari, $sampai])
                ->latest()->get();

            $data['gasebo'] = [
                'label' => '🏠 Gasebo',
                'label_bersih' => 'Gasebo',
                'total' => $gasebo->sum('total_bayar'),
                'kolom' => ['Waktu', 'Petugas', 'Nama Gasebo', 'Jenis', 'Durasi', 'Harga/Jam', 'Total'],
                'baris' => $gasebo->map(fn($t) => [
                    $t->created_at->format('d/m H:i'),
                    $t->petugas->name ?? '-',
                    $t->gasebo->nama_gasebo ?? '-',
                    ucfirst($t->gasebo->jenis ?? '-'),
                    $t->durasi_jam . ' jam',
                    'Rp ' . number_format($t->harga_per_jam, 0, ',', '.'),
                    'Rp ' . number_format($t->total_bayar, 0, ',', '.'),
                ]),
            ];
        }

        if (in_array($filterKategori, ['semua', 'ikan_hias'])) {
            $ikanHias = IkanHiasPenjualan::with('user')
                ->whereBetween('created_at', [$dari, $sampai])
                ->latest()->get();

            $data['ikan_hias'] = [
                'label' => '🐠 Ikan Hias',
                'label_bersih' => 'Ikan Hias',
                'total' => $ikanHias->sum('total_bayar'),
                'kolom' => ['Waktu', 'Petugas', 'Jumlah', 'Harga Satuan', 'Total'],
                'baris' => $ikanHias->map(fn($t) => [
                    $t->created_at->format('d/m H:i'),
                    $t->user->name ?? '-',
                    $t->jumlah_ikan . ' ekor',
                    'Rp ' . number_format($t->harga_satuan, 0, ',', '.'),
                    'Rp ' . number_format($t->total_bayar, 0, ',', '.'),
                ]),
            ];
        }

        $grandTotal = collect($data)->sum('total');

        return [
            'data' => $data,
            'grandTotal' => $grandTotal,
            'filterKategori' => $filterKategori,
            'filterTipe' => $filterTipe,
            'filterTanggal' => $filterTanggal,
            'filterBulan' => $filterBulan,
            'labelPeriode' => $labelPeriode,
        ];
    }

    // Halaman web laporan
    public function laporan(Request $request)
    {
        return view('admin.laporan', $this->generateLaporanData($request));
    }

    // Export PDF
    public function laporanPdf(Request $request)
    {
        $data = $this->generateLaporanData($request);

        $pdf = Pdf::loadView('admin.laporan_pdf', $data)->setPaper('a4', 'portrait');

        $namaFile = 'laporan-pendapatan-' . \Illuminate\Support\Str::slug($data['labelPeriode']) . '.pdf';

        return $pdf->download($namaFile);
    }

    // Export Excel
    public function laporanExcel(Request $request)
    {
        $data = $this->generateLaporanData($request);

        $namaFile = 'laporan-pendapatan-' . \Illuminate\Support\Str::slug($data['labelPeriode']) . '.xlsx';

        return Excel::download(new LaporanExport($data), $namaFile);
    }



    // Halaman daftar lapak UMKM
    public function umkm()
    {
        $lapak = LapakUmkm::all();
        $bulanIni = now()->month;
        $tahunIni = now()->year;
        return view('admin.umkm.index', compact('lapak', 'bulanIni', 'tahunIni'));
    }

    // Form tambah lapak baru
    public function tambahLapak()
    {
        return view('admin.umkm.tambah');
    }

    // Simpan lapak baru
    public function simpanLapak(Request $request)
    {
        $request->validate([
            'nama_pedagang' => 'required|string|max:100',
            'nama_usaha'    => 'required|string|max:100',
            'jenis_usaha'   => 'required|string|max:100',
            'no_telepon'    => 'nullable|string|max:15',
            'tarif_bulanan' => 'required|integer|min:0',
            'tanggal_mulai' => 'required|date',
        ]);

        LapakUmkm::create($request->all());

        return redirect()->route('admin.umkm')
                        ->with('success', 'Lapak UMKM berhasil ditambahkan!');
    }

    // Form catat pembayaran
    public function catatPembayaran($id)
    {
        $lapak = LapakUmkm::findOrFail($id);
        return view('admin.umkm.bayar', compact('lapak'));
    }

    // Simpan pembayaran
    public function simpanPembayaran(Request $request, $id)
    {
        $request->validate([
            'bulan'         => 'required|integer|min:1|max:12',
            'tahun'         => 'required|integer|min:2000',
            'tanggal_bayar' => 'required|date',
            'catatan'       => 'nullable|string',
        ]);

        $lapak = LapakUmkm::findOrFail($id);

        // Cek apakah bulan ini sudah dibayar
        if ($lapak->sudahBayar($request->bulan, $request->tahun)) {
            return redirect()->route('admin.umkm')
                            ->with('error', 'Lapak ini sudah membayar untuk bulan tersebut!');
        }

        PembayaranUmkm::create([
            'lapak_id'      => $id,
            'user_id'       => auth()->id(),
            'bulan'         => $request->bulan,
            'tahun'         => $request->tahun,
            'jumlah_bayar'  => $lapak->tarif_bulanan,
            'tanggal_bayar' => $request->tanggal_bayar,
            'status'        => 'lunas',
            'catatan'       => $request->catatan,
        ]);

        return redirect()->route('admin.umkm')
                        ->with('success', 'Pembayaran berhasil dicatat!');
    }

    // Riwayat pembayaran per lapak
    public function riwayatPembayaran($id)
    {
        $lapak = LapakUmkm::findOrFail($id);
        $pembayaran = PembayaranUmkm::where('lapak_id', $id)
                                    ->orderBy('tahun', 'desc')
                                    ->orderBy('bulan', 'desc')
                                    ->get();
        return view('admin.umkm.riwayat', compact('lapak', 'pembayaran'));
    }

    // Nonaktifkan lapak
    public function nonaktifkanLapak($id)
    {
        $lapak = LapakUmkm::findOrFail($id);
        $lapak->update(['status' => 'nonaktif']);

        return redirect()->route('admin.umkm')
                        ->with('success', 'Lapak berhasil dinonaktifkan!');
    }

    // Form edit lapak
    public function editLapak($id)
    {
        $lapak = LapakUmkm::findOrFail($id);
        return view('admin.umkm.edit', compact('lapak'));
    }

    // Update lapak
    public function updateLapak(Request $request, $id)
    {
        $request->validate([
            'nama_pedagang' => 'required|string|max:100',
            'nama_usaha'    => 'required|string|max:100',
            'jenis_usaha'   => 'required|string|max:100',
            'no_telepon'    => 'nullable|string|max:15',
            'tarif_bulanan' => 'required|integer|min:0',
            'tanggal_mulai' => 'required|date',
        ]);

        $lapak = LapakUmkm::findOrFail($id);
        $lapak->update($request->all());

        return redirect()->route('admin.umkm')
                        ->with('success', 'Data lapak berhasil diperbarui!');
    }

    // Halaman daftar paket wisata
    public function paketWisata()
    {
        $paket = PaketWisata::all();
        return view('admin.paket_wisata_tour.index', compact('paket'));
    }

    // Simpan paket wisata baru
    public function simpanPaketWisata(Request $request)
    {
        $request->validate([
            'nama_paket'      => 'required|string|max:100',
            'deskripsi'       => 'nullable|string',
            'fasilitas'       => 'nullable|string',
            'harga_per_orang' => 'required|integer|min:0',
            'minimal_orang'   => 'required|integer|min:1',
        ]);

        PaketWisata::create($request->all());

        return redirect()->route('admin.paket_wisata_tour')
                        ->with('success', 'Paket wisata berhasil ditambahkan!');
    }

    // Edit paket wisata
    public function editPaketWisata($id)
    {
        $paket = PaketWisata::findOrFail($id);
        return view('admin.paket_wisata_tour.edit', compact('paket'));
    }

    // Update paket wisata
    public function updatePaketWisata(Request $request, $id)
    {
        $request->validate([
            'nama_paket'      => 'required|string|max:100',
            'deskripsi'       => 'nullable|string',
            'fasilitas'       => 'nullable|string',
            'harga_per_orang' => 'required|integer|min:0',
            'minimal_orang'   => 'required|integer|min:1',
        ]);

        $paket = PaketWisata::findOrFail($id);
        $paket->update($request->all());

        return redirect()->route('admin.paket_wisata_tour')
                        ->with('success', 'Paket wisata berhasil diperbarui!');
    }

    // Aktifkan paket wisata
    public function aktifkanPaketWisata($id)
    {
        $paket = PaketWisata::findOrFail($id);
        $paket->update(['aktif' => true]);

        return redirect()->route('admin.paket_wisata_tour')
                        ->with('success', 'Paket wisata berhasil diaktifkan!');
    }    

    // Nonaktifkan paket wisata
    public function nonaktifkanPaketWisata($id)
    {
        $paket = PaketWisata::findOrFail($id);
        $paket->update(['aktif' => false]);

        return redirect()->route('admin.paket_wisata_tour')
                        ->with('success', 'Paket wisata berhasil dinonaktifkan!');
    }

    // Daftar gasebo
    public function gasebo()
    {
        $gasebo = Gasebo::latest()->get();
        return view('admin.gasebo.index', compact('gasebo'));
    }

    // Form tambah gasebo
    public function tambahGasebo()
    {
        return view('admin.gasebo.tambah');
    }

    // Simpan gasebo baru
    public function simpanGasebo(Request $request)
    {
        $request->validate([
            'nama_gasebo'    => 'required|string|max:100',
            'jenis'          => 'required|in:kecil,besar',
            'harga_per_jam'  => 'required|integer|min:0',
            'durasi_minimal' => 'required|integer|min:1',
        ]);

        Gasebo::create($request->all());

        return redirect()->route('admin.gasebo')
                        ->with('success', 'Data gasebo berhasil ditambahkan!');
    }

    // Form edit gasebo
    public function editGasebo($id)
    {
        $gasebo = Gasebo::findOrFail($id);
        return view('admin.gasebo.edit', compact('gasebo'));
    }

    // Update gasebo
    public function updateGasebo(Request $request, $id)
    {
        $request->validate([
            'nama_gasebo'    => 'required|string|max:100',
            'jenis'          => 'required|in:kecil,besar',
            'harga_per_jam'  => 'required|integer|min:0',
            'durasi_minimal' => 'required|integer|min:1',
        ]);

        Gasebo::findOrFail($id)->update($request->all());

        return redirect()->route('admin.gasebo')
                        ->with('success', 'Data gasebo berhasil diperbarui!');
    }

    // Nonaktifkan gasebo
    public function nonaktifkanGasebo($id)
    {
        Gasebo::findOrFail($id)->update(['aktif' => false]);
        return redirect()->route('admin.gasebo')
                        ->with('success', 'Gasebo berhasil dinonaktifkan.');
    }

    // Aktifkan gasebo
    public function aktifkanGasebo($id)
    {
        Gasebo::findOrFail($id)->update(['aktif' => true]);
        return redirect()->route('admin.gasebo')
                        ->with('success', 'Gasebo berhasil diaktifkan.');
    }

}
