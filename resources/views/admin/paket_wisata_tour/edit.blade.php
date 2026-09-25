@extends('admin.layouts.app')

@section('title', 'Edit Paket Wisata')

@section('content')

    <div class="mb-6">
        <a href="{{ route('admin.paket_wisata_tour') }}" class="text-gray-500 hover:text-gray-700 text-sm">
            ← Kembali ke daftar paket wisata
        </a>
        <h2 class="text-2xl font-bold text-gray-800 mt-2">Edit Paket Wisata</h2>
    </div>

    <div class="bg-white rounded-xl shadow p-6 max-w-lg">

        @if($errors->any())
            <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-800 rounded-lg text-sm">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.paket_wisata_tour.update', $paket->id) }}" class="space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Paket</label>
                <input type="text" name="nama_paket"
                       value="{{ old('nama_paket', $paket->nama_paket) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi <span class="text-gray-400">(opsional)</span></label>
                <textarea name="deskripsi" rows="2"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">{{ old('deskripsi', $paket->deskripsi) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fasilitas <span class="text-gray-400">(opsional)</span></label>
                <textarea name="fasilitas" rows="2"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">{{ old('fasilitas', $paket->fasilitas) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga/Orang (Rp)</label>
                    <input type="number" name="harga_per_orang"
                           value="{{ old('harga_per_orang', $paket->harga_per_orang) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Minimal Orang</label>
                    <input type="number" name="minimal_orang"
                           value="{{ old('minimal_orang', $paket->minimal_orang) }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
            </div>

            <div class="pt-2 flex gap-3">
                <button type="submit"
                        class="flex-1 bg-green-700 text-white py-2 rounded-lg hover:bg-green-800 font-medium">
                    Simpan Perubahan
                </button>
                <a href="{{ route('admin.paket_wisata_tour') }}"
                   class="flex-1 text-center bg-gray-100 text-gray-700 py-2 rounded-lg hover:bg-gray-200 font-medium">
                    Batal
                </a>
            </div>

        </form>
    </div>

@endsection