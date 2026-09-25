@extends('admin.layouts.app')

@section('title', 'Manajemen Gasebo')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Manajemen Gasebo</h2>
        <a href="{{ route('admin.gasebo.tambah') }}"
           class="bg-green-700 hover:bg-green-800 text-white text-sm font-medium px-4 py-2 rounded-lg">
            + Tambah Gasebo
        </a>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-left text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Nama</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Jenis</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Harga/Jam</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Durasi Min.</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Status Sewa</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase">Aktif</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($gasebo as $g)
                    <tr>
                        <td class="px-4 py-3 text-gray-800 font-medium">{{ $g->nama_gasebo }}</td>
                        <td class="px-4 py-3 text-gray-600 capitalize">{{ $g->jenis }}</td>
                        <td class="px-4 py-3 text-gray-600">Rp {{ number_format($g->harga_per_jam, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-gray-600">{{ $g->durasi_minimal }} jam</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full
                                {{ $g->status === 'disewa' ? 'bg-amber-100 text-amber-700' : 'bg-green-100 text-green-700' }}">
                                {{ ucfirst($g->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-0.5 text-xs font-medium rounded-full
                                {{ $g->aktif ? 'bg-blue-100 text-blue-700' : 'bg-gray-200 text-gray-600' }}">
                                {{ $g->aktif ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-2 whitespace-nowrap">
                            <a href="{{ route('admin.gasebo.edit', $g->id) }}"
                               class="text-xs font-medium text-blue-600 hover:underline">Edit</a>

                            @if ($g->aktif)
                                <form action="{{ route('admin.gasebo.nonaktifkan', $g->id) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Nonaktifkan gasebo {{ $g->nama_gasebo }}?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-xs font-medium text-red-600 hover:underline">
                                        Nonaktifkan
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('admin.gasebo.aktifkan', $g->id) }}" method="POST" class="inline"
                                      onsubmit="return confirm('Aktifkan gasebo {{ $g->nama_gasebo }}?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-xs font-medium text-green-600 hover:underline">
                                        Aktifkan
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                            Belum ada data gasebo.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection