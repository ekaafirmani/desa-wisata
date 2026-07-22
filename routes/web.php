<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\LoketController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TubingController;
use App\Http\Controllers\KulinerController;
use App\Http\Controllers\KolamController;
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

    // Manajemen petugas
    Route::get('/petugas/{id}/edit', [AdminController::class, 'editPetugas'])->name('petugas.edit');
    Route::put('/petugas/{id}/update', [AdminController::class, 'updatePetugas'])->name('petugas.update');
    Route::get('/petugas', [AdminController::class, 'petugas'])->name('petugas');
    Route::get('/petugas/tambah', [AdminController::class, 'tambahPetugas'])->name('petugas.tambah');
    Route::post('/petugas/simpan', [AdminController::class, 'simpanPetugas'])->name('petugas.simpan');
    Route::patch('/petugas/{id}/nonaktifkan', [AdminController::class, 'nonaktifkanPetugas'])->name('petugas.nonaktifkan');
    Route::patch('/petugas/{id}/aktifkan', [AdminController::class, 'aktifkanPetugas'])->name('petugas.aktifkan');

    // Paket tubing
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

    // Laporan
    Route::get('/laporan', [AdminController::class, 'laporan'])->name('laporan');
});

// Routes Loket
Route::middleware(['auth', 'role:loket'])->prefix('loket')->name('loket.')->group(function () {
    Route::get('/tiket', [LoketController::class, 'tiket'])->name('tiket');
    Route::post('/simpan', [LoketController::class, 'simpan'])->name('simpan');

    Route::get('/pakan-ikan', [LoketController::class, 'pakanIkan'])->name('pakan_ikan');
    Route::post('/pakan-ikan/simpan', [LoketController::class, 'simpanPakanIkan'])->name('pakan_ikan.simpan');
});

// Routes Tubing (mini & dewasa)
Route::middleware(['auth', 'role:tubing_mini,tubing_dewasa'])->prefix('tubing')->name('tubing.')->group(function () {
    Route::get('/dashboard', [TubingController::class, 'dashboard'])->name('dashboard');
    Route::post('/simpan', [TubingController::class, 'simpan'])->name('simpan');
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
});

require __DIR__.'/auth.php';