@extends('admin.layouts.app')

@section('title', 'Kelola UMKM')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Kelola UMKM</h2>
        <a href="{{ route('admin.umkm.tambah') }}"
           class="bg-green-700 text-white px-4 py-2 rounded-lg hover:bg-green-800 text-sm">
            + Tambah Lapak
        </a>
        
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">No</th>
                        <th class="px-6 py-3 text-left">Nama Pedagang</th>
                        <th class="px-6 py-3 text-left">Nama Usaha</th>
                        <th class="px-6 py-3 text-left">Jenis Usaha</th>
                        <th class="px-6 py-3 text-left">Tarif/Bulan</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Bayar Bulan Ini</th>
                        <th class="px-6 py-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($lapak as $index => $l)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">{{ ($lapak->firstItem() ?? 0) + $index }}</td>
                        <td class="px-6 py-4 font-medium">{{ $l->nama_pedagang }}</td>
                        <td class="px-6 py-4">{{ $l->nama_usaha }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $l->jenis_usaha }}</td>
                        <td class="px-6 py-4">Rp {{ number_format($l->tarif_bulanan, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">
                            @if($l->status == 'aktif')
                                <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">Aktif</span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            @if($l->sudahBayar($bulanIni, $tahunIni))
                                <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">✓ Lunas</span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700">✗ Belum</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.umkm.bayar', $l->id) }}"
                                   class="text-blue-600 hover:underline text-xs">Catat Bayar</a>
                                 <a href="{{ route('admin.umkm.edit', $l->id) }}"
                                    class="text-yellow-600 hover:underline text-xs">Edit</a>
                                <a href="{{ route('admin.umkm.riwayat', $l->id) }}"
                                   class="text-gray-600 hover:underline text-xs">Riwayat</a>
                                @if($l->status == 'aktif')
                                <form method="POST" action="{{ route('admin.umkm.nonaktifkan', $l->id) }}"
                                      onsubmit="return confirm('Nonaktifkan lapak {{ $l->nama_usaha }}?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-red-600 hover:underline text-xs">Nonaktifkan</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-400">
                            Belum ada lapak UMKM. Tambah lapak dulu!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @include('admin.partials.pagination', ['items' => $lapak])

@endsection