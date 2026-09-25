@extends('admin.layouts.app')

@section('title', 'Tambah Pengeluaran')

@section('content')

    <div class="mb-6">
        <a href="{{ route('admin.pengeluaran') }}" class="text-gray-500 hover:text-gray-700 text-sm">
            ← Kembali ke daftar pengeluaran
        </a>
        <h2 class="text-2xl font-bold text-gray-800 mt-2">Tambah Pengeluaran</h2>
    </div>

    @if ($errors->any())
        <div class="mb-4 bg-red-100 text-red-700 text-sm px-4 py-3 rounded-lg">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow p-6 max-w-lg">
        <form action="{{ route('admin.pengeluaran.simpan') }}" method="POST">
            @csrf

            <div class="space-y-4">
                <div>
                    <label for="nama_pengeluaran" class="text-xs font-medium text-gray-600">Nama Pengeluaran</label>
                    <input id="nama_pengeluaran" type="text" name="nama_pengeluaran"
                           value="{{ old('nama_pengeluaran') }}" maxlength="255" required
                           class="w-full mt-1 border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div>
                    <label for="nominal" class="text-xs font-medium text-gray-600">Nominal (Rp)</label>
                    <input id="nominal" type="number" name="nominal" value="{{ old('nominal') }}"
                           min="0" step="1" required
                           class="w-full mt-1 border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div>
                    <label for="tanggal" class="text-xs font-medium text-gray-600">Tanggal</label>
                    <input id="tanggal" type="date" name="tanggal"
                           value="{{ old('tanggal', now()->toDateString()) }}" required
                           class="w-full mt-1 border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <a href="{{ route('admin.pengeluaran') }}"
                   class="px-4 py-2 text-sm rounded-lg text-gray-600 hover:bg-gray-100">Batal</a>
                <button type="submit"
                        class="px-4 py-2 text-sm rounded-lg bg-green-700 text-white hover:bg-green-800">Simpan</button>
            </div>
        </form>
    </div>

@endsection
