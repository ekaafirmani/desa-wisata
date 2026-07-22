<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Manajemen - Desa Wisata</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 font-sans min-h-screen flex flex-col">

    {{-- Navbar --}}
    <nav class="bg-white shadow-sm px-6 py-4 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-green-700 rounded-lg flex items-center justify-center">
                <span class="text-white text-lg">🌿</span>
            </div>
            <span class="font-bold text-gray-800 text-lg">Desa Wisata</span>
        </div>
        <a href="{{ route('login') }}"
           class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium transition">
            Masuk ke Sistem
        </a>
    </nav>

    {{-- Hero Section --}}
    <main class="flex-1 flex items-center">
        <div class="max-w-6xl mx-auto px-6 py-16 w-full">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

                {{-- Kiri: Teks utama --}}
                <div>
                    <span class="inline-block bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full mb-4">
                        Sistem Manajemen Terpadu
                    </span>
                    <h1 class="text-4xl lg:text-5xl font-bold text-gray-900 leading-tight mb-4">
                        Selamat Datang di <span class="text-green-700">Desa Wisata</span>
                    </h1>
                    <p class="text-gray-500 text-lg mb-8 leading-relaxed">
                        Platform pengelolaan operasional wisata yang terintegrasi — mulai dari tiket masuk, wahana tubing, kolam renang, kuliner, hingga stok barang, semua dalam satu sistem.
                    </p>
                    <div class="flex flex-col sm:flex-row gap-3">
                        <a href="{{ route('login') }}"
                           class="bg-green-700 hover:bg-green-800 text-white px-6 py-3 rounded-lg font-medium text-center transition">
                            Masuk ke Sistem →
                        </a>
                    </div>

                    {{-- Info singkat --}}
                    <div class="mt-10 flex flex-wrap gap-6 text-sm text-gray-500">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                            Multi-role petugas
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                            Transaksi real-time
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 bg-green-500 rounded-full"></span>
                            Laporan terpusat
                        </div>
                    </div>
                </div>

                {{-- Kanan: Kartu fitur --}}
                <div class="grid grid-cols-2 gap-4">

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
                        <div class="text-3xl mb-3">🎫</div>
                        <h3 class="font-semibold text-gray-800 mb-1">Loket Tiket</h3>
                        <p class="text-xs text-gray-500">Kelola tiket masuk kendaraan secara cepat dan akurat.</p>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
                        <div class="text-3xl mb-3">🚣</div>
                        <h3 class="font-semibold text-gray-800 mb-1">Wahana Tubing</h3>
                        <p class="text-xs text-gray-500">Transaksi paket tubing mini & dewasa terpisah per petugas.</p>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
                        <div class="text-3xl mb-3">🏊</div>
                        <h3 class="font-semibold text-gray-800 mb-1">Area Kolam</h3>
                        <p class="text-xs text-gray-500">Tiket kolam, sewa pelampung, dan pakan ikan terintegrasi.</p>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
                        <div class="text-3xl mb-3">🍽️</div>
                        <h3 class="font-semibold text-gray-800 mb-1">Kuliner</h3>
                        <p class="text-xs text-gray-500">Kasir kuliner multi-item dengan kelola menu harian.</p>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 hover:shadow-md transition">
                        <div class="text-3xl mb-3">📦</div>
                        <h3 class="font-semibold text-gray-800 mb-1">Kelola Stok</h3>
                        <p class="text-xs text-gray-500">Pantau stok pelampung & pakan ikan secara real-time.</p>
                    </div>

                    <div class="bg-green-700 rounded-xl shadow-sm p-5 hover:bg-green-800 transition">
                        <div class="text-3xl mb-3">📊</div>
                        <h3 class="font-semibold text-white mb-1">Dashboard Admin</h3>
                        <p class="text-xs text-green-200">Rekap pendapatan & manajemen petugas terpusat.</p>
                    </div>

                </div>

            </div>
        </div>
    </main>

    {{-- Footer --}}
    <footer class="bg-white border-t border-gray-100 py-4 px-6 text-center text-xs text-gray-400">
        © {{ date('Y') }} Sistem Manajemen Desa Wisata. All rights reserved.
    </footer>

</body>
</html>