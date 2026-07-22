@extends('loket.layouts.app')

@section('title', 'Tiket Masuk')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Input Tiket Masuk</h2>
        <div class="text-right">
            <p class="text-xs text-gray-500">Total Hari Ini</p>
            <p class="text-lg font-bold text-green-700">Rp {{ number_format($totalHariIni, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Form input transaksi --}}
        <div class="bg-white rounded-xl shadow p-6 lg:col-span-1">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Transaksi Baru</h3>

            <form method="POST" action="{{ route('loket.simpan') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Kendaraan</label>
                    <select name="jenis_kendaraan" id="jenis_kendaraan" onchange="hitungTotal()"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">
                        <option value="">-- Pilih Jenis --</option>
                        @foreach ($hargaTiket as $jenis => $harga)
                            <option value="{{ $jenis }}" data-harga="{{ $harga }}" {{ old('jenis_kendaraan') == $jenis ? 'selected' : '' }}>
                                {{ ucfirst($jenis) }} (Rp {{ number_format($harga, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                    @error('jenis_kendaraan')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Kendaraan</label>
                    <input type="number" name="jumlah" id="jumlah" min="1" value="{{ old('jumlah', 1) }}" onkeyup="hitungTotal()" oninput="hitungTotal()"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">
                    @error('jumlah')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-blue-50 rounded-lg p-4">
                    <p class="text-xs text-gray-500">Total Bayar</p>
                    <p class="text-2xl font-bold text-blue-700" id="totalBayar">Rp 0</p>
                </div>

                <button type="submit"
                        class="w-full bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    Simpan Transaksi
                </button>
            </form>
        </div>

        {{-- Riwayat hari ini --}}
        <div class="bg-white rounded-xl shadow p-6 lg:col-span-2">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Hari Ini</h3>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Waktu</th>
                            <th class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Jenis</th>
                            <th class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Jumlah</th>
                            <th class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Harga Satuan</th>
                            <th class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($riwayatHariIni as $r)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $r->created_at->format('H:i') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-800 capitalize">{{ $r->jenis_kendaraan }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $r->jumlah }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">Rp {{ number_format($r->harga_satuan, 0, ',', '.') }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-800">Rp {{ number_format($r->total_bayar, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-sm text-gray-400">
                                    Belum ada transaksi hari ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script>
        function hitungTotal() {
            const select = document.getElementById('jenis_kendaraan');
            const jumlah = parseInt(document.getElementById('jumlah').value) || 0;
            const selected = select.options[select.selectedIndex];
            const harga = selected ? parseInt(selected.dataset.harga) || 0 : 0;
            const total = harga * jumlah;

            document.getElementById('totalBayar').textContent =
                'Rp ' + total.toLocaleString('id-ID');
        }
    </script>

@endsection