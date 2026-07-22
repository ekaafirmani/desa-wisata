@extends('admin.layouts.app')

@section('title', 'Edit Paket Tubing')

@section('content')

    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Edit Paket Tubing</h2>
    </div>

    <div class="bg-white rounded-xl shadow p-6 max-w-lg">
        <form method="POST" action="{{ route('admin.paket_tubing.update', $paket->id) }}" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Paket</label>
                <input type="text" name="nama_paket" value="{{ old('nama_paket', $paket->nama_paket) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                @error('nama_paket')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis</label>
                <select name="jenis"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    <option value="mini" {{ old('jenis', $paket->jenis) == 'mini' ? 'selected' : '' }}>Mini</option>
                    <option value="dewasa" {{ old('jenis', $paket->jenis) == 'dewasa' ? 'selected' : '' }}>Dewasa</option>
                </select>
                @error('jenis')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fasilitas</label>
                <textarea name="fasilitas" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">{{ old('fasilitas', $paket->fasilitas) }}</textarea>
                @error('fasilitas')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Harga</label>
                <input type="number" name="harga" min="0" value="{{ old('harga', $paket->harga) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                @error('harga')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-2 flex gap-3">
                <button type="submit"
                        class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.paket_tubing') }}"
                   class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 hover:bg-gray-100">
                    Batal
                </a>
            </div>
        </form>
    </div>

@endsection