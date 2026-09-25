@extends('admin.layouts.app')

@section('title', 'Kelola Paket Tubing')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Paket Tubing</h2>
        <a href="{{ route('admin.paket_tubing.tambah') }}"
           class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium">
            + Tambah Paket
        </a>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Nama Paket</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Jenis</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Fasilitas</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Harga</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($paket as $p)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-800">{{ $p->nama_paket }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 capitalize">{{ $p->jenis }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate">{{ $p->fasilitas }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">Rp {{ number_format($p->harga, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if ($p->aktif)
                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Tersedia</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Tidak Tersedia</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-right space-x-3">
                            <a href="{{ route('admin.paket_tubing.edit', $p->id) }}"
                               class="text-blue-600 hover:underline text-xs">
                                Edit
                            </a>

                            @if ($p->aktif)
                                <form method="POST" action="{{ route('admin.paket_tubing.nonaktifkan', $p->id) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-red-600 hover:underline text-xs">
                                        Tandai Tidak Tersedia
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.paket_tubing.aktifkan', $p->id) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-green-600 hover:underline text-xs">
                                        Tandai Tersedia
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-400">
                            Belum ada data paket tubing.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @include('admin.partials.pagination', ['items' => $paket])

@endsection