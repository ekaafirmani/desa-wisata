@extends('admin.layouts.app')

@section('title', 'Pengeluaran')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">Pengeluaran</h2>
            <p class="text-sm text-gray-500 mt-1">Daftar pengeluaran bulan {{ now()->translatedFormat('F Y') }}</p>
        </div>
        <a href="{{ route('admin.pengeluaran.tambah') }}"
           class="bg-green-700 hover:bg-green-800 text-white text-sm font-medium px-4 py-2 rounded-lg">
            + Tambah Pengeluaran
        </a>
    </div>

    {{-- Ringkasan total --}}
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow p-4 md:col-span-2">
            <p class="text-xs text-gray-500">Total Pengeluaran Bulan Ini</p>
            <p class="text-2xl font-bold text-red-600 mt-1">
                Rp {{ number_format($totalBulanIni, 0, ',', '.') }}
            </p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs text-gray-500">Operasional</p>
            <p class="text-lg font-bold text-gray-700 mt-1">
                Rp {{ number_format($perKategori['operasional'] ?? 0, 0, ',', '.') }}
            </p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs text-gray-500">Perawatan</p>
            <p class="text-lg font-bold text-gray-700 mt-1">
                Rp {{ number_format($perKategori['perawatan'] ?? 0, 0, ',', '.') }}
            </p>
        </div>
        <div class="bg-white rounded-xl shadow p-4">
            <p class="text-xs text-gray-500">Honor</p>
            <p class="text-lg font-bold text-gray-700 mt-1">
                Rp {{ number_format($perKategori['honor'] ?? 0, 0, ',', '.') }}
            </p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">No</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Nama Pengeluaran</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Kategori</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Nominal</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse ($pengeluaran as $index => $p)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-600">{{ ($pengeluaran->firstItem() ?? 0) + $index }}</td>
                            <td class="px-4 py-3 text-gray-800 font-medium">{{ $p->nama_pengeluaran }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-medium
                                    {{ $p->kategori == 'operasional' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $p->kategori == 'perawatan' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                    {{ $p->kategori == 'honor' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $p->kategori == 'lainnya' ? 'bg-gray-100 text-gray-700' : '' }}">
                                    {{ ucfirst($p->kategori ?? 'lainnya') }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-600">Rp {{ number_format($p->nominal, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-gray-600">{{ $p->tanggal->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-right whitespace-nowrap">
                                <a href="{{ route('admin.pengeluaran.edit', $p->id) }}"
                                   class="text-xs font-medium text-blue-600 hover:underline mr-3">Edit</a>
                                <form action="{{ route('admin.pengeluaran.hapus', $p->id) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Hapus pengeluaran {{ $p->nama_pengeluaran }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs font-medium text-red-600 hover:underline">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-400">
                                Belum ada pengeluaran bulan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @include('admin.partials.pagination', ['items' => $pengeluaran])
    </div>

@endsection