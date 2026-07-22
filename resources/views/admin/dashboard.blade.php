@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')

    <h2 class="text-2xl font-bold text-gray-800 mb-6">Dashboard Admin</h2>

    {{-- Baris 1: Kartu ringkasan hari ini --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">

        <div class="lg:col-span-2 bg-green-700 rounded-xl shadow p-5 text-white">
            <p class="text-green-200 text-xs font-medium uppercase tracking-wide">Total Pendapatan Hari Ini</p>
            <p class="text-3xl font-bold mt-1">Rp {{ number_format($totalHariIni, 0, ',', '.') }}</p>
            <p class="text-green-300 text-xs mt-2">{{ now()->translatedFormat('l, d F Y') }}</p>
        </div>

        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wide">🎫 Tiket Masuk</p>
            <p class="text-xl font-bold text-gray-800 mt-1">Rp {{ number_format($pendapatanHariIni['tiket_masuk'], 0, ',', '.') }}</p>
        </div>

        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wide">🚣 Tubing</p>
            <p class="text-xl font-bold text-gray-800 mt-1">Rp {{ number_format($pendapatanHariIni['tubing'], 0, ',', '.') }}</p>
        </div>

        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wide">🏊 Kolam</p>
            <p class="text-xl font-bold text-gray-800 mt-1">Rp {{ number_format($pendapatanHariIni['kolam'], 0, ',', '.') }}</p>
        </div>

        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wide">🍽️ Kuliner</p>
            <p class="text-xl font-bold text-gray-800 mt-1">Rp {{ number_format($pendapatanHariIni['kuliner'], 0, ',', '.') }}</p>
        </div>

    </div>

    {{-- Baris 2: Pakan ikan + petugas aktif --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">

        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wide">🐟 Pakan Ikan</p>
            <p class="text-xl font-bold text-gray-800 mt-1">Rp {{ number_format($pendapatanHariIni['pakan_ikan'], 0, ',', '.') }}</p>
        </div>

        <div class="bg-white rounded-xl shadow p-5">
            <p class="text-gray-500 text-xs font-medium uppercase tracking-wide">👥 Petugas Aktif</p>
            <p class="text-xl font-bold text-gray-800 mt-1">{{ $totalPetugas }} orang</p>
        </div>

        {{-- Stok hampir habis --}}
        <div class="col-span-2 bg-white rounded-xl shadow p-5">
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

    {{-- Baris 3: Grafik pendapatan --}}
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Grafik Pendapatan</h3>
            <div class="flex gap-2">
                <button onclick="filterGrafik(7)"  id="btn7"
                        class="px-3 py-1 text-xs rounded-lg bg-green-700 text-white font-medium">
                    7 Hari
                </button>
                <button onclick="filterGrafik(14)" id="btn14"
                        class="px-3 py-1 text-xs rounded-lg bg-gray-100 text-gray-600 font-medium hover:bg-gray-200">
                    14 Hari
                </button>
                <button onclick="filterGrafik(30)" id="btn30"
                        class="px-3 py-1 text-xs rounded-lg bg-gray-100 text-gray-600 font-medium hover:bg-gray-200">
                    30 Hari
                </button>
            </div>
        </div>
        <canvas id="grafikPendapatan" class="w-full" height="100"></canvas>
    </div>

    {{-- Baris 4: Aktivitas terkini --}}
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
        const allData = @json($grafikData);
        let chart = null;

        function filterGrafik(hari) {
            // Update tombol aktif
            ['btn7', 'btn14', 'btn30'].forEach(id => {
                const btn = document.getElementById(id);
                btn.className = 'px-3 py-1 text-xs rounded-lg bg-gray-100 text-gray-600 font-medium hover:bg-gray-200';
            });
            document.getElementById('btn' + hari).className =
                'px-3 py-1 text-xs rounded-lg bg-green-700 text-white font-medium';

            const filtered = allData.slice(-hari);
            const labels = filtered.map(d => d.label);
            const values = filtered.map(d => d.total);

            if (chart) {
                chart.data.labels = labels;
                chart.data.datasets[0].data = values;
                chart.update();
            } else {
                const ctx = document.getElementById('grafikPendapatan').getContext('2d');
                chart = new Chart(ctx, {
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

        // Tampilkan 7 hari pertama saat load
        filterGrafik(7);
    </script>

@endsection