@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')

    <h2 class="text-2xl font-bold text-gray-800 mb-6">Dashboard Admin</h2>

    {{-- Baris 1: Total Hari Ini & Bulan Ini --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">

        <div class="bg-green-700 rounded-xl shadow p-5 text-white">
            <p class="text-green-200 text-xs font-medium uppercase tracking-wide">Total Pendapatan Hari Ini</p>
            <p class="text-3xl font-bold mt-1">Rp {{ number_format($totalHariIni, 0, ',', '.') }}</p>
            <p class="text-green-300 text-xs mt-2">{{ now()->translatedFormat('l, d F Y') }}</p>
        </div>

        <div class="bg-emerald-900 rounded-xl shadow p-5 text-white">
            <p class="text-emerald-200 text-xs font-medium uppercase tracking-wide">Total Pendapatan Bulan Ini</p>
            <p class="text-3xl font-bold mt-1">Rp {{ number_format($totalBulanIni, 0, ',', '.') }}</p>
            <p class="text-emerald-300 text-xs mt-2">{{ now()->translatedFormat('F Y') }}</p>
        </div>

    </div>

    {{-- Baris 2: Rincian Pendapatan Hari Ini per kategori --}}
    <div class="mb-6">
        <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-3">Rincian Hari Ini</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
            @foreach ($kategoriLabel as $key => $k)
                <div class="bg-white rounded-xl shadow p-5">
                    <p class="text-gray-500 text-xs font-medium uppercase tracking-wide">{{ $k['icon'] }} {{ $k['label'] }}</p>
                    <p class="text-lg font-bold text-gray-800 mt-1">Rp {{ number_format($pendapatanHariIni[$key], 0, ',', '.') }}</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Baris 3: Rincian Pendapatan Bulan Ini per kategori --}}
    <div class="mb-6">
        <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide mb-3">Rincian Bulan Ini</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
            @foreach ($kategoriLabel as $key => $k)
                <div class="bg-white rounded-xl shadow p-5 border border-emerald-100">
                    <p class="text-gray-500 text-xs font-medium uppercase tracking-wide">{{ $k['icon'] }} {{ $k['label'] }}</p>
                    <p class="text-lg font-bold text-emerald-800 mt-1">Rp {{ number_format($pendapatanBulanIni[$key], 0, ',', '.') }}</p>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Baris 4: Petugas aktif + Stok hampir habis --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wide">👥 Petugas Aktif</p>
            <p class="text-xl font-bold text-gray-800 mt-1">{{ $totalPetugas }} orang</p>
        </div>

        <div class="md:col-span-2 bg-white rounded-xl shadow p-5">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wide mb-3">⚠️ Stok Hampir Habis</p>
            @if ($stokHampirHabis->isEmpty())
                <p class="text-sm text-green-600 font-medium">✅ Semua stok aman</p>
            @else
                <div class="space-y-2">
                    @foreach ($stokHampirHabis as $stok)
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-700 capitalize">{{ str_replace('_', ' ', $stok->jenis) }}</span>
                            <span class="text-xs font-semibold px-2 py-0.5 rounded-full
                                {{ $stok->tersedia <= 0 ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ $stok->tersedia }} tersisa
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- Baris 5: Grafik pendapatan harian --}}
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Grafik Pendapatan Harian</h3>
            <div class="flex gap-2">
                <button onclick="filterGrafikHarian(7)"  id="btnHari7"
                        class="px-3 py-1 text-xs rounded-lg bg-green-700 text-white font-medium">
                    7 Hari
                </button>
                <button onclick="filterGrafikHarian(14)" id="btnHari14"
                        class="px-3 py-1 text-xs rounded-lg bg-gray-100 text-gray-600 font-medium hover:bg-gray-200">
                    14 Hari
                </button>
                <button onclick="filterGrafikHarian(30)" id="btnHari30"
                        class="px-3 py-1 text-xs rounded-lg bg-gray-100 text-gray-600 font-medium hover:bg-gray-200">
                    30 Hari
                </button>
            </div>
        </div>
        <canvas id="grafikPendapatanHarian" class="w-full" height="100"></canvas>
    </div>

    {{-- Baris 6: Grafik pendapatan bulanan --}}
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Grafik Pendapatan Bulanan</h3>
            <div class="flex gap-2">
                <button onclick="filterGrafikBulanan(6)"  id="btnBulan6"
                        class="px-3 py-1 text-xs rounded-lg bg-emerald-800 text-white font-medium">
                    6 Bulan
                </button>
                <button onclick="filterGrafikBulanan(12)" id="btnBulan12"
                        class="px-3 py-1 text-xs rounded-lg bg-gray-100 text-gray-600 font-medium hover:bg-gray-200">
                    12 Bulan
                </button>
            </div>
        </div>
        <canvas id="grafikPendapatanBulanan" class="w-full" height="100"></canvas>
    </div>

    {{-- Baris 7: Aktivitas terkini --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Aktivitas Terkini Hari Ini</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Waktu</th>
                        <th class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Kategori</th>
                        <th class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Detail</th>
                        <th class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($aktivitasTerkini as $a)
                        <tr>
                            <td class="px-4 py-3 text-gray-600">{{ $a['waktu'] }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 text-xs font-medium rounded-full
                                    @if($a['kategori'] == 'Tiket Masuk') bg-blue-100 text-blue-700
                                    @elseif($a['kategori'] == 'Tubing') bg-orange-100 text-orange-700
                                    @elseif(str_contains($a['kategori'], 'Kolam')) bg-cyan-100 text-cyan-700
                                    @elseif($a['kategori'] == 'Kuliner') bg-purple-100 text-purple-700
                                    @elseif($a['kategori'] == 'Gazebo') bg-amber-100 text-amber-700
                                    @elseif($a['kategori'] == 'Ikan Hias') bg-pink-100 text-pink-700
                                    @else bg-green-100 text-green-700
                                    @endif">
                                    {{ $a['kategori'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ $a['detail'] }}</td>
                            <td class="px-4 py-3 text-gray-800 font-medium text-right">Rp {{ number_format($a['total'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-gray-400">
                                Belum ada aktivitas hari ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Script grafik --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <script>
        const dataHarian  = @json($grafikHarian);
        const dataBulanan = @json($grafikBulanan);

        let chartHarian  = null;
        let chartBulanan = null;

        // === Grafik Harian ===
        function filterGrafikHarian(hari) {
            ['btnHari7', 'btnHari14', 'btnHari30'].forEach(id => {
                document.getElementById(id).className =
                    'px-3 py-1 text-xs rounded-lg bg-gray-100 text-gray-600 font-medium hover:bg-gray-200';
            });
            document.getElementById('btnHari' + hari).className =
                'px-3 py-1 text-xs rounded-lg bg-green-700 text-white font-medium';

            const filtered = dataHarian.slice(-hari);
            const labels = filtered.map(d => d.label);
            const values = filtered.map(d => d.total);

            if (chartHarian) {
                chartHarian.data.labels = labels;
                chartHarian.data.datasets[0].data = values;
                chartHarian.update();
            } else {
                const ctx = document.getElementById('grafikPendapatanHarian').getContext('2d');
                chartHarian = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Pendapatan (Rp)',
                            data: values,
                            backgroundColor: 'rgba(21, 128, 61, 0.15)',
                            borderColor: 'rgba(21, 128, 61, 1)',
                            borderWidth: 2,
                            borderRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: ctx => 'Rp ' + ctx.raw.toLocaleString('id-ID')
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: val => 'Rp ' + val.toLocaleString('id-ID')
                                }
                            }
                        }
                    }
                });
            }
        }

        // === Grafik Bulanan ===
        function filterGrafikBulanan(bulan) {
            ['btnBulan6', 'btnBulan12'].forEach(id => {
                document.getElementById(id).className =
                    'px-3 py-1 text-xs rounded-lg bg-gray-100 text-gray-600 font-medium hover:bg-gray-200';
            });
            document.getElementById('btnBulan' + bulan).className =
                'px-3 py-1 text-xs rounded-lg bg-emerald-800 text-white font-medium';

            const filtered = dataBulanan.slice(-bulan);
            const labels = filtered.map(d => d.label);
            const values = filtered.map(d => d.total);

            if (chartBulanan) {
                chartBulanan.data.labels = labels;
                chartBulanan.data.datasets[0].data = values;
                chartBulanan.update();
            } else {
                const ctx = document.getElementById('grafikPendapatanBulanan').getContext('2d');
                chartBulanan = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Pendapatan (Rp)',
                            data: values,
                            backgroundColor: 'rgba(6, 78, 59, 0.15)',
                            borderColor: 'rgba(6, 78, 59, 1)',
                            borderWidth: 2,
                            borderRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: ctx => 'Rp ' + ctx.raw.toLocaleString('id-ID')
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: val => 'Rp ' + val.toLocaleString('id-ID')
                                }
                            }
                        }
                    }
                });
            }
        }

        // Tampilan default saat load
        filterGrafikHarian(7);
        filterGrafikBulanan(6);
    </script>

@endsection