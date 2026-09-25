@extends('admin.layouts.app') 

@section('title', 'Menu Kuliner') 

@section('content')

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Menu Kuliner</h2>
        <a href="{{ route('admin.menu_kuliner.tambah') }}"
           class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium">
            + Tambah Menu
        </a>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Nama Menu</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Kategori</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Harga</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Ditambahkan Pada</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($menu as $m)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-800">{{ $m->nama_menu }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600 capitalize">{{ $m->kategori }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">Rp {{ number_format($m->harga, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if ($m->tersedia)
                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Tersedia</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Tidak Tersedia</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $m->created_at->format('d M Y, H:i') }}</td>
                        <td class="px-6 py-4 text-sm text-right space-x-3">
                            <a href="{{ route('admin.menu_kuliner.edit', $m->id) }}"
                               class="text-blue-600 hover:underline text-xs">
                                Edit
                            </a>

                            @if ($m->tersedia)
                                <form method="POST" action="{{ route('admin.menu_kuliner.nonaktifkan', $m->id) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-red-600 hover:underline text-xs">
                                        Tandai Tidak Tersedia
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.menu_kuliner.aktifkan', $m->id) }}" class="inline">
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
                            Belum ada data menu kuliner.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @include('admin.partials.pagination', ['items' => $menu])

@endsection