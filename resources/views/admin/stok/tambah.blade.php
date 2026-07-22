@extends('admin.layouts.app')

@section('title', 'Tambah Stok')

@section('content')


    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Tambah Stok Baru</h2>
    </div>

    <div class="bg-white rounded-xl shadow p-6 max-w-lg">
        @if (count($jenisTersedia) === 0)
            <p class="text-sm text-gray-500">
                Semua jenis barang sudah memiliki data stok. Gunakan menu <strong>Update Stok</strong> di halaman sebelumnya untuk mengubah jumlah atau harga.
            </p>
            <a href="{{ route('admin.stok') }}"
               class="inline-block mt-4 px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100">
                Kembali
            </a>
        @else
            <form method="POST" action="{{ route('admin.stok.simpan') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Barang</label>
                    <select name="jenis"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                        <option value="">-- Pilih Jenis --</option>
                        @foreach ($jenisTersedia as $j)
                            <option value="{{ $j }}" {{ old('jenis') == $j ? 'selected' : '' }}>
                                {{ str_replace('_', ' ', ucfirst($j)) }}
                            </option>
                        @endforeach
                    </select>
                    @error('jenis')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Stok</label>
                    <input type="number" name="total_stok" min="0" value="{{ old('total_stok') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    @error('total_stok')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga Satuan</label>
                    <input type="number" name="harga_satuan" min="0" value="{{ old('harga_satuan') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    @error('harga_satuan')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2 flex gap-3">
                    <button type="submit"
                            class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium">
                        Simpan
                    </button>
                    <a href="{{ route('admin.stok') }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100">
                        Batal
                    </a>
                </div>
            </form>
        @endif
    </div>

@endsection