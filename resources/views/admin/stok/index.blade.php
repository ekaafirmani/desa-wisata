@extends('admin.layouts.app')

@section('title', 'Kelola Stok')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Kelola Stok</h2>
        <a href="{{ route('admin.stok.tambah') }}"
           class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium">
            + Tambah Stok Baru
        </a>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <table class="w-full text-left">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Jenis Barang</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Total Stok</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Tersedia</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase">Harga Satuan</th>
                    <th class="px-6 py-3 text-xs font-semibold text-gray-500 uppercase text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse ($stok as $s)
                    <tr>
                        <td class="px-6 py-4 text-sm text-gray-800 capitalize">{{ str_replace('_', ' ', $s->jenis) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $s->total_stok }}</td>
                        <td class="px-6 py-4 text-sm">
                            @if ($s->tersedia <= 0)
                                <span class="px-2 py-1 text-xs font-medium bg-red-100 text-red-700 rounded-full">{{ $s->tersedia }} (Habis)</span>
                            @elseif ($s->tersedia <= 5)
                                <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">{{ $s->tersedia }} (Menipis)</span>
                            @else
                                <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">{{ $s->tersedia }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">Rp {{ number_format($s->harga_satuan, 0, ',', '.') }}</td>
                        <td class="px-6 py-4 text-sm text-right">
                            <button type="button"
                                    onclick="document.getElementById('modal-{{ $s->id }}').classList.remove('hidden')"
                                    class="text-blue-600 hover:underline text-xs">
                                Update Stok
                            </button>
                        </td>
                    </tr>

                    {{-- Modal update stok --}}
                    <div id="modal-{{ $s->id }}" class="hidden fixed inset-0 bg-black/40 flex items-center justify-center z-50">
                        <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-sm">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 capitalize">
                                Update Stok: {{ str_replace('_', ' ', $s->jenis) }}
                            </h3>
                            <form method="POST" action="{{ route('admin.stok.update', $s->id) }}" class="space-y-4">
                                @csrf
                                @method('PATCH')

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Stok</label>
                                    <input type="number" name="total_stok" min="0" value="{{ $s->total_stok }}"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                                    <p class="text-xs text-gray-400 mt-1">Tersedia saat ini: {{ $s->tersedia }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Satuan</label>
                                    <input type="number" name="harga_satuan" min="0" value="{{ $s->harga_satuan }}"
                                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                                </div>

                                <div class="flex gap-3 pt-2">
                                    <button type="submit"
                                            class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium">
                                        Simpan
                                    </button>
                                    <button type="button"
                                            onclick="document.getElementById('modal-{{ $s->id }}').classList.add('hidden')"
                                            class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100">
                                        Batal
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-400">
                            Belum ada data stok.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@endsection