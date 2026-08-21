@extends('admin.layouts.app')

@section('title', 'Laporan')

@section('content')

    {{-- Header (disembunyikan saat print) --}}
    <div class="flex items-center justify-between mb-6 print:hidden">
        <h2 class="text-2xl font-bold text-gray-800">Laporan Pendapatan</h2>
        <div class="flex gap-2">
            <a href="{{ route('admin.laporan.pdf', request()->query()) }}"
                class="flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                📄 Simpan PDF
            </a>
            <a href="{{ route('admin.laporan.excel', request()->query()) }}"
                class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                📊 Simpan Excel
            </a>
        </div>
    </div>

    {{-- Form filter (disembunyikan saat print) --}}
    <div class="bg-white rounded-xl shadow p-5 mb-6 print:hidden">
        <form method="GET" action="{{ route('admin.laporan') }}" class="flex flex-wrap gap-4 items-center">

            {{-- Filter tipe --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Periode</label>
                <select name="tipe" onchange="this.form.submit()"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    <option value="hari" {{ $filterTipe == 'hari' ? 'selected' : '' }}>Per Hari</option>
                    <option value="bulan" {{ $filterTipe == 'bulan' ? 'selected' : '' }}>Per Bulan</option>
                </select>
            </div>

            {{-- Filter tanggal / bulan --}}
            @if ($filterTipe === 'bulan')
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Bulan</label>
                    <input type="month" name="bulan" value="{{ $filterBulan }}"
                        class="min-w-[160px] border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                </div>
            @else
                <div>
                    <label class="block text-xs font-medium text-gray-600 mb-1">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ $filterTanggal }}"
                        class="min-w-[160px] border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                </div>
            @endif

            {{-- Filter kategori --}}
            <div>
                <label class="block text-xs font-medium text-gray-600 mb-1">Kategori</label>
                <select name="kategori"
                    class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    <option value="semua" {{ $filterKategori == 'semua' ? 'selected' : '' }}>Semua Kategori</option>
                    <option value="tiket_masuk" {{ $filterKategori == 'tiket_masuk' ? 'selected' : '' }}>Tiket Masuk</option>
                    <option value="tubing" {{ $filterKategori == 'tubing' ? 'selected' : '' }}>Tubing</option>
                    <option value="kolam" {{ $filterKategori == 'kolam' ? 'selected' : '' }}>Kolam</option>
                    <option value="kuliner" {{ $filterKategori == 'kuliner' ? 'selected' : '' }}>Kuliner</option>
                    <option value="pakan_ikan" {{ $filterKategori == 'pakan_ikan' ? 'selected' : '' }}>Pakan Ikan</option>
                    <option value="gazebo" {{ $filterKategori == 'gazebo' ? 'selected' : '' }}>Gazebo</option>
                    <option value="ikan_hias" {{ $filterKategori == 'ikan_hias' ? 'selected' : '' }}>Ikan Hias</option>
                </select>
            </div>

            <button type="submit"
                class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium">
                Tampilkan
            </button>
        </form>
    </div>

    {{-- Header laporan (muncul saat print) --}}
    <div class="hidden print:block mb-6 text-center border-b pb-4">
        <h1 class="text-2xl font-bold">Laporan Pendapatan Desa Wisata</h1>
        <p class="text-gray-600 mt-1">Periode: {{ $labelPeriode }}</p>
        <p class="text-gray-500 text-sm">Dicetak pada: {{ now()->translatedFormat('d F Y, H:i') }}</p>
    </div>

    {{-- Ringkasan grand total --}}
    <div
        class="bg-green-700 rounded-xl p-5 mb-6 text-white print:bg-white print:border print:border-gray-300 print:text-gray-800">
        <p class="text-green-200 text-sm print:text-gray-500">Total Pendapatan — {{ $labelPeriode }}</p>
        <p class="text-3xl font-bold mt-1">Rp {{ number_format($grandTotal, 0, ',', '.') }}</p>
    </div>

    {{-- Tabel per kategori --}}
    @forelse ($data as $key => $kategori)
        <div
            class="bg-white rounded-xl shadow mb-6 overflow-hidden print:shadow-none print:border print:border-gray-200 print:mb-8">

            {{-- Header kategori --}}
            <div class="flex items-center justify-between px-6 py-4 border-b bg-gray-50 print:bg-white">
                <h3 class="font-semibold text-gray-800">{{ $kategori['label'] }}</h3>
                <span class="font-bold text-green-700">Rp {{ number_format($kategori['total'], 0, ',', '.') }}</span>
            </div>

            @if ($kategori['baris']->isEmpty())
                <div class="px-6 py-6 text-center text-sm text-gray-400">
                    Tidak ada transaksi pada periode ini.
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                @foreach ($kategori['kolom'] as $kolom)
                                    <th class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">{{ $kolom }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach ($kategori['baris'] as $baris)
                                <tr class="hover:bg-gray-50 print:hover:bg-white">
                                    @foreach ($baris as $sel)
                                        <td class="px-4 py-2 text-gray-700">{{ $sel }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-t bg-gray-50 print:bg-white">
                            <tr>
                                <td colspan="{{ count($kategori['kolom']) - 1 }}"
                                    class="px-4 py-2 text-sm font-semibold text-gray-600 text-right">
                                    Subtotal {{ $kategori['label'] }}:
                                </td>
                                <td class="px-4 py-2 font-bold text-green-700">
                                    Rp {{ number_format($kategori['total'], 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    @empty
        <div class="bg-white rounded-xl shadow p-10 text-center text-gray-400">
            Tidak ada data untuk ditampilkan.
        </div>
    @endforelse

    {{-- Grand total bawah (untuk print) --}}
    <div class="hidden print:block border-t-2 border-gray-800 pt-4 mt-4 text-right">
        <p class="text-lg font-bold">Grand Total: Rp {{ number_format($grandTotal, 0, ',', '.') }}</p>
    </div>

    {{-- CSS Print --}}
    <style>
        @media print {

            /* Sembunyikan elemen yang tidak perlu */
            nav,
            aside,
            header,
            .print\:hidden {
                display: none !important;
            }

            /* Konten utama full width */
            main {
                padding: 0 !important;
                margin: 0 !important;
            }

            body {
                background: white !important;
                font-size: 12px;
            }

            /* Pastikan tabel tidak terpotong antar halaman */
            table {
                page-break-inside: auto;
            }

            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }

            /* Tiap kategori mulai di halaman baru kalau terlalu panjang */
            .print\:mb-8 {
                margin-bottom: 2rem;
            }
        }
    </style>

@endsection