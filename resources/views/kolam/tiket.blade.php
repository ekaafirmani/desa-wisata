@extends('kolam.layouts.app')

@section('title', 'Tiket Kolam')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Tiket Masuk Kolam</h2>
        <div class="text-right">
            <p class="text-xs text-gray-500">Total Hari Ini</p>
            <p class="text-lg font-bold text-green-700">Rp {{ number_format($totalHariIni, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Form --}}
        <div class="bg-white rounded-xl shadow p-6 lg:col-span-1">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Transaksi Baru</h3>
            <form method="POST" action="{{ route('kolam.tiket.simpan') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Orang</label>
                    <input type="number" name="jumlah_orang" id="jumlah_orang" min="1" value="1" oninput="hitungTotal()"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-600">
                </div>

                <div class="bg-cyan-50 rounded-lg p-4">
                    <p class="text-xs text-gray-500">Harga: Rp {{ number_format($hargaTiketKolam, 0, ',', '.') }} / orang</p>
                    <p class="text-2xl font-bold text-cyan-700" id="totalBayar">Rp {{ number_format($hargaTiketKolam, 0, ',', '.') }}</p>
                </div>

                <button type="submit"
                        class="w-full bg-cyan-700 hover:bg-cyan-800 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    Simpan Transaksi
                </button>
            </form>
        </div>

        {{-- Riwayat --}}
        <div class="bg-white rounded-xl shadow p-6 lg:col-span-2">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Hari Ini</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 border-b">
                        <tr>
                            <th class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Waktu</th>
                            <th class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Jumlah Orang</th>
                            <th class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($riwayatTiket as $r)
                            <tr>
                                <td class="px-3 py-2 text-gray-600">{{ $r->created_at->format('H:i') }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ $r->jumlah_orang }}</td>
                                <td class="px-3 py-2 font-medium text-gray-800">Rp {{ number_format($r->total_bayar, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-3 py-6 text-center text-gray-400">Belum ada transaksi hari ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <script>
        function hitungTotal() {
            const jumlah = parseInt(document.getElementById('jumlah_orang').value) || 0;
            const total = jumlah * {{ $hargaTiketKolam }};
            document.getElementById('totalBayar').textContent = 'Rp ' + total.toLocaleString('id-ID');
        }
    </script>

@endsection