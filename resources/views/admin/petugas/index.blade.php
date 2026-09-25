@extends('admin.layouts.app')

@section('title', 'Manajemen Petugas')

@section('content')


    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Manajemen Petugas</h2>
        <a href="{{ route('admin.petugas.tambah') }}"
           class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium">
            + Tambah Petugas
        </a>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Nama</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Email</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Role</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($petugas as $p)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-800">{{ $p->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $p->email }}</td>
                        <td class="px-6 py-4 text-sm">
                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                {{ $p->role == 'loket' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $p->role == 'tubing_mini' ? 'bg-cyan-100 text-cyan-700' : '' }}
                                {{ $p->role == 'tubing_dewasa' ? 'bg-orange-100 text-orange-700' : '' }}
                                {{ $p->role == 'kolam' ? 'bg-teal-100 text-teal-700' : '' }}
                                {{ $p->role == 'kuliner' ? 'bg-yellow-100 text-yellow-700' : '' }}
                                {{ $p->role == 'paket_wisata' ? 'bg-purple-100 text-purple-700' : '' }}">
                                {{ ucfirst(str_replace('_', ' ', $p->role)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if ($p->aktif)
                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Aktif</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-right space-x-3">
                            <a href="{{ route('admin.petugas.edit', $p->id) }}"
                               class="text-blue-600 hover:underline text-xs">
                                Edit
                            </a>

                            @if ($p->aktif)
                                <form method="POST" action="{{ route('admin.petugas.nonaktifkan', $p->id) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-red-600 hover:underline text-xs">
                                        Nonaktifkan
                                    </button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.petugas.aktifkan', $p->id) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-green-600 hover:underline text-xs">
                                        Aktifkan
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-400">
                            Belum ada data petugas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection