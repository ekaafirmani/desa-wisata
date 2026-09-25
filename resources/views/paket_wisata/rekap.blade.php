@extends('paket_wisata.layouts.app')

@section('title', 'Rekap Harian Paket Wisata')

@section('content')

    <h2 class="text-2xl font-bold text-gray-800 mb-6">Rekap Harian Paket Wisata</h2>

    {{-- Filter tanggal --}}
    <div class="bg-white rounded-xl shadow p-4 mb-6">
        <form method="GET" action="{{ route('paket_wisata.rekap') }}" class="flex gap-3 items-end">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Tanggal</label>
                <input type="date" name="tanggal" value="{{ $tanggal }}"
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit"
                    class="bg-blue-700 text-white px-4 py-2 rounded-lg hover:bg-blue-800 text-sm">
                Tampilkan
            </button>
        </form>
    </div>

    {{-- Ringkasan --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-gray-500 text-xs">Total Pendapatan</p>
            <p class="text-xl font-bold text-blue-700 mt-1">
                Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
            </p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-gray-500 text-xs">Total Transaksi</p>
            <p class="text-xl font-bold text-gray-700 mt-1">
                {{ $transaksi->count() }} transaksi
            </p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-gray-500 text-xs">Total Peserta</p>
            <p class="text-xl font-bold text-gray-700 mt-1">
                {{ $transaksi->sum('jumlah_orang') }} orang
            </p>
        </div>
    </div>

    {{-- Tabel rekap --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">No</th>
                        <th class="px-6 py-3 text-left">Waktu</th>
                        <th class="px-6 py-3 text-left">Paket</th>
                        <th class="px-6 py-3 text-left">Nama Pemesan</th>
                        <th class="px-6 py-3 text-left">Jumlah Orang</th>
                        <th class="px-6 py-3 text-left">Total Bayar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($transaksi as $index => $t)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $t->created_at->format('H:i') }}</td>
                        <td class="px-6 py-4 font-medium">{{ $t->paket->nama_paket }}</td>
                        <td class="px-6 py-4">{{ $t->nama_pemesan ?? '-' }}</td>
                        <td class="px-6 py-4">{{ $t->jumlah_orang }} orang</td>
                        <td class="px-6 py-4 font-medium text-blue-700">
                            Rp {{ number_format($t->total_bayar, 0, ',', '.') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                            Tidak ada transaksi pada tanggal ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection