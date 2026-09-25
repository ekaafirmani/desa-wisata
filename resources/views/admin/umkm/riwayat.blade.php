@extends('admin.layouts.app')

@section('title', 'Riwayat Pembayaran UMKM')

@section('content')

    <div class="mb-6">
        <a href="{{ route('admin.umkm') }}" class="text-gray-500 hover:text-gray-700 text-sm">
            ← Kembali ke daftar UMKM
        </a>
        <h2 class="text-2xl font-bold text-gray-800 mt-2">Riwayat Pembayaran</h2>
        <p class="text-gray-500 text-sm mt-1">{{ $lapak->nama_usaha }} — {{ $lapak->nama_pedagang }}</p>
    </div>

    {{-- Ringkasan --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-gray-500 text-xs">Total Pembayaran</p>
            <p class="text-xl font-bold text-green-700 mt-1">
                Rp {{ number_format($pembayaran->sum('jumlah_bayar'), 0, ',', '.') }}
            </p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-gray-500 text-xs">Jumlah Bulan Lunas</p>
            <p class="text-xl font-bold text-blue-700 mt-1">
                {{ $pembayaran->where('status', 'lunas')->count() }} bulan
            </p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-gray-500 text-xs">Tarif Bulanan</p>
            <p class="text-xl font-bold text-gray-700 mt-1">
                Rp {{ number_format($lapak->tarif_bulanan, 0, ',', '.') }}
            </p>
        </div>
    </div>

    {{-- Tabel riwayat --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">No</th>
                        <th class="px-6 py-3 text-left">Bulan</th>
                        <th class="px-6 py-3 text-left">Tahun</th>
                        <th class="px-6 py-3 text-left">Tanggal Bayar</th>
                        <th class="px-6 py-3 text-left">Jumlah</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pembayaran as $index => $p)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 font-medium">{{ $p->nama_bulan }}</td>
                        <td class="px-6 py-4">{{ $p->tahun }}</td>
                        <td class="px-6 py-4">{{ $p->tanggal_bayar->format('d/m/Y') }}</td>
                        <td class="px-6 py-4">
                            Rp {{ number_format($p->jumlah_bayar, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            @if($p->status == 'lunas')
                                <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">Lunas</span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700">Belum Lunas</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-500">{{ $p->catatan ?? '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                            Belum ada riwayat pembayaran.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection