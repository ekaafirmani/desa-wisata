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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    // Halaman dashboard utama admin
    public function dashboard()
    {
    // === Kartu ringkasan HARI INI ===
        $today = today();

        $pendapatanHariIni = [
            'tiket_masuk' => TiketMasuk::whereDate('created_at', $today)->sum('total_bayar'),
            'tubing'      => TransaksiTubing::whereDate('created_at', $today)->sum('total_bayar'),
            'kolam'       => TiketKolam::whereDate('created_at', $today)->sum('total_bayar') +
                            SewaPelampung::whereDate('created_at', $today)->sum('total_bayar'),
            'kuliner'     => TransaksiKuliner::whereDate('created_at', $today)->sum('total_bayar'),
            'pakan_ikan'  => PakanIkan::whereDate('created_at', $today)->sum('total_bayar'),
        ];

        $totalHariIni = array_sum($pendapatanHariIni);

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
                    'waktu'    => $t->created_at->format('H:i'),
                    'kategori' => 'Tiket Masuk',
                    'detail'   => ucfirst($t->jenis_kendaraan) . ' x' . $t->jumlah,
                    'total'    => $t->total_bayar,
                ])
        );

        $aktivitasTerkini = $aktivitasTerkini->merge(
            TransaksiTubing::with('paket')->whereDate('created_at', $today)
                ->latest()->limit(5)
                ->get()->map(fn($t) => [
                    'waktu'    => $t->created_at->format('H:i'),
                    'kategori' => 'Tubing',
                    'detail'   => ($t->paket->nama_paket ?? '-') . ' x' . $t->jumlah_peserta . ' org',
                    'total'    => $t->total_bayar,
                ])
        );

        $aktivitasTerkini = $aktivitasTerkini->merge(
            TiketKolam::whereDate('created_at', $today)
                ->latest()->limit(5)
                ->get()->map(fn($t) => [
                    'waktu'    => $t->created_at->format('H:i'),
                    'kategori' => 'Tiket Kolam',
                    'detail'   => $t->jumlah_orang . ' orang',
                    'total'    => $t->total_bayar,
                ])
        );

        $aktivitasTerkini = $aktivitasTerkini->merge(
            TransaksiKuliner::whereDate('created_at', $today)
                ->latest()->limit(5)
                ->get()->map(fn($t) => [
                    'waktu'    => $t->created_at->format('H:i'),
                    'kategori' => 'Kuliner',
                    'detail'   => 'Transaksi #' . $t->id,
                    'total'    => $t->total_bayar,
                ])
        );

        $aktivitasTerkini = $aktivitasTerkini->merge(
            PakanIkan::whereDate('created_at', $today)
                ->latest()->limit(5)
                ->get()->map(fn($t) => [
                    'waktu'    => $t->created_at->format('H:i'),
                    'kategori' => 'Pakan Ikan',
                    'detail'   => $t->jumlah_porsi . ' porsi (' . $t->titik_jual . ')',
                    'total'    => $t->total_bayar,
                ])
        );

        // Urutkan berdasarkan waktu terbaru, ambil 10
        $aktivitasTerkini = $aktivitasTerkini->sortByDesc('waktu')->take(10)->values();

        // === Data grafik 30 hari terakhir ===
        $grafikData = [];
        for ($i = 29; $i >= 0; $i--) {
            $tanggal = now()->subDays($i)->toDateString();
            $label   = now()->subDays($i)->format('d M');

            $total = TiketMasuk::whereDate('created_at', $tanggal)->sum('total_bayar')
                + TransaksiTubing::whereDate('created_at', $tanggal)->sum('total_bayar')
                + TiketKolam::whereDate('created_at', $tanggal)->sum('total_bayar')
                + SewaPelampung::whereDate('created_at', $tanggal)->sum('total_bayar')
                + TransaksiKuliner::whereDate('created_at', $tanggal)->sum('total_bayar')
                + PakanIkan::whereDate('created_at', $tanggal)->sum('total_bayar');

            $grafikData[] = ['label' => $label, 'total' => $total];
        }

        return view('admin.dashboard', compact(
            'pendapatanHariIni',
            'totalHariIni',
            'totalPetugas',
            'stokHampirHabis',
            'aktivitasTerkini',
            'grafikData'
        ));
    }
        
        // Halaman daftar semua petugas
        public function petugas ()
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
                'name'     => 'required|string|max:100',
                'email'    => 'required|email|unique:users',
                'password' => 'required|min:6',
                'role'     => 'required|in:loket,tubing_mini,tubing_dewasa,kolam,kuliner',
            ]);

            User::create([
                'name'       => $request->name,
                'email'      => $request->email,
                'password'   => Hash::make($request->password),
                'role'       => $request->role,
                'aktif'      => true,
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
            'name'     => 'required|string|max:100',
            'email'    => 'required|email|unique:users,email,' . $petugas->id,
            'password' => 'nullable|min:6',
            'role'     => 'required|in:loket,tubing_mini,tubing_dewasa,kolam,kuliner',
        ]);

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
            'role'  => $request->role,
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
            'jenis'      => 'required|in:mini,dewasa',
            'fasilitas'  => 'required|string',
            'harga'      => 'required|integer|min:0',
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
            'jenis'      => 'required|in:mini,dewasa',
            'fasilitas'  => 'required|string',
            'harga'      => 'required|integer|min:0',
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
            'harga'     => 'required|integer|min:0',
            'kategori'  => 'required|in:makanan,minuman,snack',
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
            'harga'     => 'required|integer|min:0',
            'kategori'  => 'required|in:makanan,minuman,snack',
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
            'jenis'        => 'required|in:pelampung,pakan_ikan|unique:master_stok,jenis',
            'total_stok'   => 'required|integer|min:0',
            'harga_satuan' => 'required|integer|min:0',
        ]);

        MasterStok::create([
            'jenis'        => $request->jenis,
            'total_stok'   => $request->total_stok,
            'tersedia'     => $request->total_stok,
            'harga_satuan' => $request->harga_satuan,
        ]);

        return redirect()->route('admin.stok')
                        ->with('success', 'Stok baru berhasil ditambahkan!');
    }

    // Update stok
    public function updateStok(Request $request, $id)
    {
        $request->validate([
            'total_stok'   => 'required|integer|min:0',
            'harga_satuan' => 'required|integer|min:0',
        ]);

        $stok = MasterStok::findOrFail($id);
        $selisih = $request->total_stok - $stok->total_stok;
        $stok->update([
            'total_stok'   => $request->total_stok,
            'tersedia'     => $stok->tersedia + $selisih,
            'harga_satuan' => $request->harga_satuan,
        ]);

        return redirect()->route('admin.stok')
                        ->with('success', 'Stok berhasil diperbarui!');
    }

    public function laporan(Request $request)
    {
        $filterKategori = $request->get('kategori', 'semua');
        $filterTipe     = $request->get('tipe', 'hari'); // 'hari' atau 'bulan'
        $filterTanggal  = $request->get('tanggal', today()->toDateString());
        $filterBulan    = $request->get('bulan', now()->format('Y-m'));

        // Tentukan rentang tanggal berdasarkan filter
        if ($filterTipe === 'bulan') {
            $dari   = \Carbon\Carbon::parse($filterBulan . '-01')->startOfMonth();
            $sampai = \Carbon\Carbon::parse($filterBulan . '-01')->endOfMonth();
            $labelPeriode = 'Bulan ' . \Carbon\Carbon::parse($filterBulan)->translatedFormat('F Y');
        } else {
            $dari   = \Carbon\Carbon::parse($filterTanggal)->startOfDay();
            $sampai = \Carbon\Carbon::parse($filterTanggal)->endOfDay();
            $labelPeriode = \Carbon\Carbon::parse($filterTanggal)->translatedFormat('l, d F Y');
        }

        $data = [];

        // Tiket Masuk
        if (in_array($filterKategori, ['semua', 'tiket_masuk'])) {
            $tiket = TiketMasuk::with('petugas')
                ->whereBetween('created_at', [$dari, $sampai])
                ->latest()->get();

            $data['tiket_masuk'] = [
                'label'    => '🎫 Tiket Masuk',
                'total'    => $tiket->sum('total_bayar'),
                'kolom'    => ['Waktu', 'Petugas', 'Jenis Kendaraan', 'Jumlah', 'Harga Satuan', 'Total'],
                'baris'    => $tiket->map(fn($t) => [
                    $t->created_at->format('d/m H:i'),
                    $t->petugas->name ?? '-',
                    ucfirst($t->jenis_kendaraan),
                    $t->jumlah,
                    'Rp ' . number_format($t->harga_satuan, 0, ',', '.'),
                    'Rp ' . number_format($t->total_bayar, 0, ',', '.'),
                ]),
            ];
        }

        // Tubing
        if (in_array($filterKategori, ['semua', 'tubing'])) {
            $tubing = TransaksiTubing::with('petugas', 'paket')
                ->whereBetween('created_at', [$dari, $sampai])
                ->latest()->get();

            $data['tubing'] = [
                'label'    => '🚣 Tubing',
                'total'    => $tubing->sum('total_bayar'),
                'kolom'    => ['Waktu', 'Petugas', 'Paket', 'Peserta', 'Total'],
                'baris'    => $tubing->map(fn($t) => [
                    $t->created_at->format('d/m H:i'),
                    $t->petugas->name ?? '-',
                    $t->paket->nama_paket ?? '-',
                    $t->jumlah_peserta . ' orang',
                    'Rp ' . number_format($t->total_bayar, 0, ',', '.'),
                ]),
            ];
        }

        // Kolam (tiket + sewa pelampung)
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
                'total' => $totalKolam,
                'kolom' => ['Waktu', 'Petugas', 'Jenis', 'Jumlah', 'Harga Satuan', 'Total'],
                'baris' => $barisKolam->sortByDesc(fn($b) => $b[0])->values(),
            ];
        }

        // Kuliner
        if (in_array($filterKategori, ['semua', 'kuliner'])) {
            $kuliner = TransaksiKuliner::with('petugas', 'detail.menu')
                ->whereBetween('created_at', [$dari, $sampai])
                ->latest()->get();

            $data['kuliner'] = [
                'label' => '🍽️ Kuliner',
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

        // Pakan Ikan
        if (in_array($filterKategori, ['semua', 'pakan_ikan'])) {
            $pakanIkan = PakanIkan::with('petugas')
                ->whereBetween('created_at', [$dari, $sampai])
                ->latest()->get();

            $data['pakan_ikan'] = [
                'label' => '🐟 Pakan Ikan',
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

        $grandTotal = collect($data)->sum('total');

        return view('admin.laporan', compact(
            'data',
            'grandTotal',
            'filterKategori',
            'filterTipe',
            'filterTanggal',
            'filterBulan',
            'labelPeriode'
        ));
    }

}
