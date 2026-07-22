@extends('tubing.layouts.app')

@section('title', 'Transaksi Tubing')

@section('content')

    <h2 class="text-2xl font-bold text-gray-800 mb-6">
        Input Transaksi Tubing {{ ucfirst($jenis) }}
    </h2>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Form input transaksi --}}
        <div class="bg-white rounded-xl shadow p-6 lg:col-span-1">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Transaksi Baru</h3>

            @if ($paketList->isEmpty())
                <p class="text-sm text-gray-500">
                    Belum ada paket tubing {{ $jenis }} yang tersedia. Hubungi admin untuk menambahkan paket.
                </p>
            @else
                <form method="POST" action="{{ route('tubing.simpan') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Paket</label>
                        <select name="paket_id" id="paket_id" onchange="hitungTotal()"
                                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-600">
                            <option value="">-- Pilih Paket --</option>
                            @foreach ($paketList as $p)
                                <option value="{{ $p->id }}" data-harga="{{ $p->harga }}" {{ old('paket_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama_paket }} (Rp {{ number_format($p->harga, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                        @error('paket_id')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Peserta</label>
                        <input type="number" name="jumlah_peserta" id="jumlah_peserta" min="1" value="{{ old('jumlah_peserta', 1) }}" onkeyup="hitungTotal()" oninput="hitungTotal()"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-orange-600">
                        @error('jumlah_peserta')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="bg-orange-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500">Total Bayar</p>
                        <p class="text-2xl font-bold text-orange-700" id="totalBayar">Rp 0</p>
                    </div>

                    <button type="submit"
                            class="w-full bg-orange-700 hover:bg-orange-800 text-white px-4 py-2 rounded-lg text-sm font-medium">
                        Simpan Transaksi
                    </button>
                </form>
            @endif
        </div>

        {{-- Riwayat hari ini --}}
        <div class="bg-white rounded-xl shadow p-6 lg:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Riwayat Transaksi Hari Ini</h3>
                <div class="text-right">
                    <p class="text-xs text-gray-500">Total Pendapatan</p>
                    <p class="text-lg font-bold text-green-700">Rp {{ number_format($totalHariIni, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Waktu</th>
                            <th class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Paket</th>
                            <th class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Peserta</th>
                            <th class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($riwayatHariIni as $r)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $r->created_at->format('H:i') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-800">{{ $r->paket->nama_paket ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $r->jumlah_peserta }} orang</td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-800">Rp {{ number_format($r->total_bayar, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-400">
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
            const select = document.getElementById('paket_id');
            const jumlah = parseInt(document.getElementById('jumlah_peserta').value) || 0;
            const selected = select.options[select.selectedIndex];
            const harga = selected ? parseInt(selected.dataset.harga) || 0 : 0;
            const total = harga * jumlah;

            document.getElementById('totalBayar').textContent =
                'Rp ' + total.toLocaleString('id-ID');
        }
    </script>

@endsection