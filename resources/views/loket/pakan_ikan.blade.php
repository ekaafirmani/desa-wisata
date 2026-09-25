@extends('loket.layouts.app')

@section('title', 'Pakan Ikan')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Pakan Ikan</h2>
        <div class="text-right">
            <p class="text-xs text-gray-500">Total Hari Ini</p>
            <p class="text-lg font-bold text-green-700">Rp {{ number_format($totalHariIni, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Form --}}
        <div class="bg-white rounded-xl shadow p-6 lg:col-span-1">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Jual Pakan Ikan</h3>
            <form method="POST" action="{{ route('loket.pakan_ikan.simpan') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Porsi</label>
                    <input type="number" name="jumlah_porsi" min="1" value="1"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">
                </div>
                <p class="text-xs text-gray-500">
                    Harga: Rp {{ number_format(\App\Services\PakanIkanService::HARGA_PER_PORSI, 0, ',', '.') }} / porsi
                    @if ($stokPakanIkan)
                        — Stok: {{ $stokPakanIkan->tersedia }} porsi
                    @endif
                </p>
                <button type="submit"
                        class="w-full bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    Simpan
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
                            <th class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Jumlah Porsi</th>
                            <th class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($riwayatPakanIkan as $r)
                            <tr>
                                <td class="px-3 py-2 text-gray-600">{{ $r->created_at->format('H:i') }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ $r->jumlah_porsi }}</td>
                                <td class="px-3 py-2 font-medium text-gray-800">Rp {{ number_format($r->total_bayar, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-3 py-6 text-center text-gray-400">Belum ada transaksi hari ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @include('loket.partials.pagination', ['items' => $riwayatPakanIkan])
        </div>

    </div>

@endsection