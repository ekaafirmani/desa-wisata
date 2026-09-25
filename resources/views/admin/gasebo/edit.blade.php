@extends('admin.layouts.app')

@section('title', 'Edit Gasebo')

@section('content')

    <h2 class="text-2xl font-bold text-gray-800 mb-6">Edit Gasebo</h2>

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
        <form action="{{ route('admin.gasebo.update', $gasebo->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="text-xs font-medium text-gray-600">Nama Gasebo</label>
                    <input type="text" name="nama_gasebo" value="{{ old('nama_gasebo', $gasebo->nama_gasebo) }}" required
                           class="w-full mt-1 border rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600">Jenis</label>
                    <select name="jenis" required class="w-full mt-1 border rounded-lg px-3 py-2 text-sm">
                        <option value="kecil" {{ old('jenis', $gasebo->jenis) === 'kecil' ? 'selected' : '' }}>Kecil</option>
                        <option value="besar" {{ old('jenis', $gasebo->jenis) === 'besar' ? 'selected' : '' }}>Besar</option>
                    </select>
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600">Harga per Jam (Rp)</label>
                    <input type="number" name="harga_per_jam" value="{{ old('harga_per_jam', $gasebo->harga_per_jam) }}" min="0" required
                           class="w-full mt-1 border rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="text-xs font-medium text-gray-600">Durasi Minimal (jam)</label>
                    <input type="number" name="durasi_minimal" value="{{ old('durasi_minimal', $gasebo->durasi_minimal) }}" min="1" required
                           class="w-full mt-1 border rounded-lg px-3 py-2 text-sm">
                </div>
            </div>

            <div class="flex justify-end gap-2 mt-6">
                <a href="{{ route('admin.gasebo') }}"
                   class="px-4 py-2 text-sm rounded-lg text-gray-600 hover:bg-gray-100">Batal</a>
                <button type="submit"
                        class="px-4 py-2 text-sm rounded-lg bg-green-700 text-white hover:bg-green-800">Simpan Perubahan</button>
            </div>
        </form>
    </div>

@endsection