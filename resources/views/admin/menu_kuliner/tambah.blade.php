@extends('admin.layouts.app')

@section('title', 'Tambah Menu Kuliner')

@section('content')

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Tambah Menu Kuliner</h2>
    </div>

    <div class="bg-white rounded-xl shadow p-6 max-w-lg">
        <form method="POST" action="{{ route('admin.menu_kuliner.simpan') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Menu</label>
                <input type="text" name="nama_menu" value="{{ old('nama_menu') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                @error('nama_menu')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select name="kategori"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    <option value="">-- Pilih Kategori --</option>
                    <option value="makanan" {{ old('kategori') == 'makanan' ? 'selected' : '' }}>Makanan</option>
                    <option value="minuman" {{ old('kategori') == 'minuman' ? 'selected' : '' }}>Minuman</option>
                    <option value="snack" {{ old('kategori') == 'snack' ? 'selected' : '' }}>Snack</option>
                </select>
                @error('kategori')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
                <input type="number" name="harga" min="0" value="{{ old('harga') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                @error('harga')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-2 flex gap-3">
                <button type="submit"
                        class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    Simpan
                </button>
                <a href="{{ route('admin.menu_kuliner') }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100">
                    Batal
                </a>
            </div>
        </form>
    </div>

@endsection