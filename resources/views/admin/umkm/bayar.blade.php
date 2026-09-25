@extends('admin.layouts.app')

@section('title', 'Catat Pembayaran UMKM')

@section('content')

    <div class="mb-6">
        <a href="{{ route('admin.umkm') }}" class="text-gray-500 hover:text-gray-700 text-sm">
            ← Kembali ke daftar UMKM
        </a>
        <h2 class="text-2xl font-bold text-gray-800 mt-2">Catat Pembayaran Sewa</h2>
        <p class="text-gray-500 text-sm mt-1">{{ $lapak->nama_usaha }} — {{ $lapak->nama_pedagang }}</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Info lapak --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="font-semibold text-gray-700 mb-4">Informasi Lapak</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-500">Nama Pedagang</span>
                    <span class="font-medium">{{ $lapak->nama_pedagang }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Nama Usaha</span>
                    <span class="font-medium">{{ $lapak->nama_usaha }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Jenis Usaha</span>
                    <span class="font-medium">{{ $lapak->jenis_usaha }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">No. Telepon</span>
                    <span class="font-medium">{{ $lapak->no_telepon ?? '-' }}</span>
                </div>
                <div class="flex justify-between border-t pt-3">
                    <span class="text-gray-500">Tarif Bulanan</span>
                    <span class="font-bold text-green-700">
                        Rp {{ number_format($lapak->tarif_bulanan, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Form pembayaran --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="font-semibold text-gray-700 mb-4">Form Pembayaran</h3>

            @if($errors->any())
                <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-800 rounded-lg text-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.umkm.simpan_bayar', $lapak->id) }}" class="space-y-4">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                        <select name="bulan"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                            @foreach([1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'] as $num => $nama)
                                <option value="{{ $num }}" {{ now()->month == $num ? 'selected' : '' }}>
                                    {{ $nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                        <input type="number" name="tahun"
                               value="{{ now()->year }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bayar</label>
                    <input type="date" name="tanggal_bayar"
                           value="{{ now()->toDateString() }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Bayar</label>
                    <div class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm bg-gray-50 text-gray-700">
                        Rp {{ number_format($lapak->tarif_bulanan, 0, ',', '.') }}
                        <span class="text-gray-400 text-xs">(otomatis sesuai tarif)</span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan <span class="text-gray-400">(opsional)</span></label>
                    <textarea name="catatan" rows="2"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                              placeholder="Catatan tambahan jika ada..."></textarea>
                </div>

                <div class="pt-2 flex gap-3">
                    <button type="submit"
                            class="flex-1 bg-green-700 text-white py-2 rounded-lg hover:bg-green-800 font-medium">
                        Simpan Pembayaran
                    </button>
                    <a href="{{ route('admin.umkm') }}"
                       class="flex-1 text-center bg-gray-100 text-gray-700 py-2 rounded-lg hover:bg-gray-200 font-medium">
                        Batal
                    </a>
                </div>

            </form>
        </div>

    </div>

@endsection