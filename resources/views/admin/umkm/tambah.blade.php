@extends('admin.layouts.app')

@section('title', 'Tambah Lapak UMKM')

@section('content')

    <div class="mb-6">
        <a href="{{ route('admin.umkm') }}" class="text-gray-500 hover:text-gray-700 text-sm">
            ← Kembali ke daftar UMKM
        </a>
        <h2 class="text-2xl font-bold text-gray-800 mt-2">Tambah Lapak UMKM</h2>
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

        <form method="POST" action="{{ route('admin.umkm.simpan') }}" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pedagang</label>
                <input type="text" name="nama_pedagang"
                       value="{{ old('nama_pedagang') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                       placeholder="Nama lengkap pedagang">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Usaha</label>
                <input type="text" name="nama_usaha"
                       value="{{ old('nama_usaha') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                       placeholder="Nama toko/usaha">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Usaha</label>
                <input type="text" name="jenis_usaha"
                       value="{{ old('jenis_usaha') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                       placeholder="Contoh: Makanan, Kerajinan, Pakaian">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    No. Telepon <span class="text-gray-400">(opsional)</span>
                </label>
                <input type="text" name="no_telepon"
                       value="{{ old('no_telepon') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                       placeholder="08xxxxxxxxxx">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tarif Bulanan (Rp)</label>
                <input type="number" name="tarif_bulanan"
                       value="{{ old('tarif_bulanan') }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                       placeholder="Contoh: 50000">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai Sewa</label>
                <input type="date" name="tanggal_mulai"
                       value="{{ old('tanggal_mulai', now()->toDateString()) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <div class="pt-2 flex gap-3">
                <button type="submit"
                        class="flex-1 bg-green-700 text-white py-2 rounded-lg hover:bg-green-800 font-medium">
                    Simpan Lapak
                </button>
                <a href="{{ route('admin.umkm') }}"
                   class="flex-1 text-center bg-gray-100 text-gray-700 py-2 rounded-lg hover:bg-gray-200 font-medium">
                    Batal
                </a>
            </div>

        </form>
    </div>

@endsection