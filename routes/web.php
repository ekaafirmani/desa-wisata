<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LoketController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TubingController;
use App\Http\Controllers\KulinerController;
use App\Http\Controllers\KolamController;
use App\Http\Controllers\PaketWisataController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Routes Admin
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    //UMKM
    Route::get('/umkm', [AdminController::class, 'umkm'])->name('umkm');
    Route::get('/umkm/tambah', [AdminController::class, 'tambahLapak'])->name('umkm.tambah');
    Route::post('/umkm/simpan', [AdminController::class, 'simpanLapak'])->name('umkm.simpan');
    Route::get('/umkm/{id}/bayar', [AdminController::class, 'catatPembayaran'])->name('umkm.bayar');
    Route::post('/umkm/{id}/bayar', [AdminController::class, 'simpanPembayaran'])->name('umkm.simpan_bayar');
    Route::get('/umkm/{id}/riwayat', [AdminController::class, 'riwayatPembayaran'])->name('umkm.riwayat');
    Route::patch('/umkm/{id}/nonaktifkan', [AdminController::class, 'nonaktifkanLapak'])->name('umkm.nonaktifkan');
    Route::get('/umkm/{id}/edit', [AdminController::class, 'editLapak'])->name('umkm.edit');
    Route::patch('/umkm/{id}/update', [AdminController::class, 'updateLapak'])->name('umkm.update');

    // Manajemen petugas
    Route::get('/petugas/{id}/edit', [AdminController::class, 'editPetugas'])->name('petugas.edit');
    Route::put('/petugas/{id}/update', [AdminController::class, 'updatePetugas'])->name('petugas.update');
    Route::get('/petugas', [AdminController::class, 'petugas'])->name('petugas');
    Route::get('/petugas/tambah', [AdminController::class, 'tambahPetugas'])->name('petugas.tambah');
    Route::post('/petugas/simpan', [AdminController::class, 'simpanPetugas'])->name('petugas.simpan');
    Route::patch('/petugas/{id}/nonaktifkan', [AdminController::class, 'nonaktifkanPetugas'])->name('petugas.nonaktifkan');
    Route::patch('/petugas/{id}/aktifkan', [AdminController::class, 'aktifkanPetugas'])->name('petugas.aktifkan');

    // Paket tubing
    Route::get('/paket-tubing', [AdminController::class, 'paketTubing'])->name('paket_tubing');
    Route::get('/paket-tubing/tambah', [AdminController::class, 'tambahPaketTubing'])->name('paket_tubing.tambah');
    Route::post('/paket-tubing/simpan', [AdminController::class, 'simpanPaketTubing'])->name('paket_tubing.simpan');
    Route::get('/paket-tubing/{id}/edit', [AdminController::class, 'editPaketTubing'])->name('paket_tubing.edit');
    Route::put('/paket-tubing/{id}/update', [AdminController::class, 'updatePaketTubing'])->name('paket_tubing.update');
    Route::patch('/paket-tubing/{id}/nonaktifkan', [AdminController::class, 'nonaktifkanPaketTubing'])->name('paket_tubing.nonaktifkan');
    Route::patch('/paket-tubing/{id}/aktifkan', [AdminController::class, 'aktifkanPaketTubing'])->name('paket_tubing.aktifkan');

    // Menu kuliner
    Route::get('/menu-kuliner', [AdminController::class, 'menuKuliner'])->name('menu_kuliner');
    Route::get('/menu-kuliner/tambah', [AdminController::class, 'tambahMenuKuliner'])->name('menu_kuliner.tambah');
    Route::post('/menu-kuliner/simpan', [AdminController::class, 'simpanMenuKuliner'])->name('menu_kuliner.simpan');
    Route::get('/menu-kuliner/{id}/edit', [AdminController::class, 'editMenuKuliner'])->name('menu_kuliner.edit');
    Route::put('/menu-kuliner/{id}/update', [AdminController::class, 'updateMenuKuliner'])->name('menu_kuliner.update');
    Route::patch('/menu-kuliner/{id}/nonaktifkan', [AdminController::class, 'nonaktifkanMenuKuliner'])->name('menu_kuliner.nonaktifkan');
    Route::patch('/menu-kuliner/{id}/aktifkan', [AdminController::class, 'aktifkanMenuKuliner'])->name('menu_kuliner.aktifkan');

    // Stok
    Route::get('/stok', [AdminController::class, 'stok'])->name('stok');
    Route::get('/stok/tambah', [AdminController::class, 'tambahStok'])->name('stok.tambah');
    Route::post('/stok/simpan', [AdminController::class, 'simpanStok'])->name('stok.simpan');
    Route::patch('/stok/{id}', [AdminController::class, 'updateStok'])->name('stok.update');

    // Pengeluaran
    Route::get('/pengeluaran', [AdminController::class, 'pengeluaran'])->name('pengeluaran');
    Route::get('/pengeluaran/tambah', [AdminController::class, 'tambahPengeluaran'])->name('pengeluaran.tambah');
    Route::post('/pengeluaran/simpan', [AdminController::class, 'simpanPengeluaran'])->name('pengeluaran.simpan');
    Route::get('/pengeluaran/{id}/edit', [AdminController::class, 'editPengeluaran'])->name('pengeluaran.edit');
    Route::patch('/pengeluaran/{id}/update', [AdminController::class, 'updatePengeluaran'])->name('pengeluaran.update');
    Route::delete('/pengeluaran/{id}', [AdminController::class, 'hapusPengeluaran'])->name('pengeluaran.hapus');

   // Master data gasebo
    Route::get('/gasebo', [AdminController::class, 'gasebo'])->name('gasebo');
    Route::get('/gasebo/tambah', [AdminController::class, 'tambahGasebo'])->name('gasebo.tambah');
    Route::post('/gasebo/simpan', [AdminController::class, 'simpanGasebo'])->name('gasebo.simpan');
    Route::get('/gasebo/{id}/edit', [AdminController::class, 'editGasebo'])->name('gasebo.edit');
    Route::put('/gasebo/{id}/update', [AdminController::class, 'updateGasebo'])->name('gasebo.update');
    Route::patch('/gasebo/{id}/nonaktifkan', [AdminController::class, 'nonaktifkanGasebo'])->name('gasebo.nonaktifkan');
    Route::patch('/gasebo/{id}/aktifkan', [AdminController::class, 'aktifkanGasebo'])->name('gasebo.aktifkan');

    // Laporan
    Route::get('/laporan', [AdminController::class, 'laporan'])->name('laporan');
    Route::get('/laporan/pdf', [AdminController::class, 'laporanPdf'])->name('laporan.pdf');
    Route::get('/laporan/excel', [AdminController::class, 'laporanExcel'])->name('laporan.excel');

    // Paket Wisata (Admin)
    Route::get('/paket-wisata-tour', [AdminController::class, 'paketWisata'])->name('paket_wisata_tour');
    Route::post('/paket-wisata-tour/simpan', [AdminController::class, 'simpanPaketWisata'])->name('paket_wisata_tour.simpan');
    Route::get('/paket-wisata-tour/{id}/edit', [AdminController::class, 'editPaketWisata'])->name('paket_wisata_tour.edit');
    Route::patch('/paket-wisata-tour/{id}/update', [AdminController::class, 'updatePaketWisata'])->name('paket_wisata_tour.update');
    Route::patch('/paket-wisata/{id}/aktifkan', [AdminController::class, 'aktifkanPaketWisata'])->name('paket_wisata_tour.aktifkan');
    Route::patch('/paket-wisata-tour/{id}/nonaktifkan', [AdminController::class, 'nonaktifkanPaketWisata'])->name('paket_wisata_tour.nonaktifkan');
});


// Routes Loket
Route::middleware(['auth', 'role:loket'])->prefix('loket')->name('loket.')->group(function () {
    Route::get('/tiket', [LoketController::class, 'tiket'])->name('tiket');
    Route::post('/simpan', [LoketController::class, 'simpan'])->name('simpan');

    Route::get('/pakan-ikan', [LoketController::class, 'pakanIkan'])->name('pakan_ikan');
    Route::post('/pakan-ikan/simpan', [LoketController::class, 'simpanPakanIkan'])->name('pakan_ikan.simpan');

    Route::get('/gasebo', [LoketController::class, 'gasebo'])->name('gasebo');
    Route::post('/gasebo/simpan', [LoketController::class, 'simpanGasebo'])->name('gasebo.simpan');
    Route::patch('/gasebo/{id}/selesai', [LoketController::class, 'selesaiGasebo'])->name('gasebo.selesai');
});

// Routes Tubing (mini & dewasa)
Route::middleware(['auth', 'role:tubing_mini,tubing_dewasa'])->prefix('tubing')->name('tubing.')->group(function () {
    Route::get('/dashboard', [TubingController::class, 'dashboard'])->name('dashboard');
    Route::post('/simpan', [TubingController::class, 'simpan'])->name('simpan');
});

//Routes Paket Wisata
Route::middleware(['auth', 'role:paket_wisata'])->prefix('paket-wisata')->name('paket_wisata.')->group(function () {
    Route::get('/dashboard', [PaketWisataController::class, 'dashboard'])->name('dashboard');
    Route::post('/simpan', [PaketWisataController::class, 'simpan'])->name('simpan');
    Route::get('/rekap', [PaketWisataController::class, 'rekap'])->name('rekap');
});

// Routes Kuliner
Route::middleware(['auth', 'role:kuliner'])->prefix('kuliner')->name('kuliner.')->group(function () {
    Route::get('/dashboard', [KulinerController::class, 'dashboard'])->name('dashboard');
    Route::post('/simpan', [KulinerController::class, 'simpanTransaksi'])->name('simpan');
    Route::post('/pakan-ikan/simpan', [KulinerController::class, 'simpanPakanIkan'])->name('pakan_ikan.simpan');
    Route::get('/pakan-ikan', [KulinerController::class, 'pakanIkan'])->name('pakan_ikan');

    // Kelola menu (oleh petugas kuliner)
    Route::get('/menu', [KulinerController::class, 'menu'])->name('menu');
    Route::get('/menu/tambah', [KulinerController::class, 'tambahMenu'])->name('menu.tambah');
    Route::post('/menu/simpan', [KulinerController::class, 'simpanMenu'])->name('menu.simpan');
    Route::patch('/menu/{id}/nonaktifkan', [KulinerController::class, 'nonaktifkanMenu'])->name('menu.nonaktifkan');
    Route::patch('/menu/{id}/aktifkan', [KulinerController::class, 'aktifkanMenu'])->name('menu.aktifkan');
    Route::get('/menu/{id}/edit', [KulinerController::class, 'editMenu'])->name('menu.edit');
    Route::put('/menu/{id}/update', [KulinerController::class, 'updateMenu'])->name('menu.update');

    //Gsebo
    Route::get('/gasebo', [KulinerController::class, 'gasebo'])->name('gasebo');
    Route::post('/gasebo/simpan', [KulinerController::class, 'simpanGasebo'])->name('gasebo.simpan');
    Route::patch('/gasebo/{id}/selesai', [KulinerController::class, 'selesaiGasebo'])->name('gasebo.selesai');
});

// Routes Kolam
Route::middleware(['auth', 'role:kolam'])->prefix('kolam')->name('kolam.')->group(function () {
    Route::get('/tiket', [KolamController::class, 'tiket'])->name('tiket');
    Route::post('/tiket/simpan', [KolamController::class, 'simpanTiket'])->name('tiket.simpan');

    Route::get('/pelampung', [KolamController::class, 'pelampung'])->name('pelampung');
    Route::post('/pelampung/simpan', [KolamController::class, 'simpanSewaPelampung'])->name('pelampung.simpan');
    Route::patch('/pelampung/{id}/kembali', [KolamController::class, 'tandaiKembali'])->name('pelampung.kembali');

    Route::get('/pakan-ikan', [KolamController::class, 'pakanIkan'])->name('pakan_ikan');
    Route::post('/pakan-ikan/simpan', [KolamController::class, 'simpanPakanIkan'])->name('pakan_ikan.simpan');

    Route::get('/ikan-hias', [KolamController::class, 'ikanHias'])->name('ikan_hias');
    Route::post('/ikan-hias/simpan', [KolamController::class, 'simpanIkanHias'])->name('ikan_hias.simpan');
});

require __DIR__ . '/auth.php';