@extends('kolam.layouts.app')

@section('title', 'Sewa Pelampung')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Sewa Pelampung</h2>
        <div class="text-right">
            <p class="text-xs text-gray-500">Total Hari Ini</p>
            <p class="text-lg font-bold text-green-700">Rp {{ number_format($totalHariIni, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

        {{-- Form --}}
        <div class="bg-white rounded-xl shadow p-6 lg:col-span-1">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Sewa Baru</h3>
            <form method="POST" action="{{ route('kolam.pelampung.simpan') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Pelampung</label>
                    <input type="number" name="jumlah" min="1" value="1"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-600">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (opsional)</label>
                    <input type="text" name="catatan" maxlength="100" placeholder="Misal: baju merah, dekat gazebo 2"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-cyan-600">
                    <p class="text-xs text-gray-400 mt-1">Bantu cocokkan saat pengembalian, terutama kalau ramai.</p>
                </div>
                <p class="text-xs text-gray-500">
                    Harga: Rp {{ number_format($hargaSewaPelampung, 0, ',', '.') }} / buah
                    @if ($stokPelampung)
                        — Stok: {{ $stokPelampung->tersedia }} buah
                    @endif
                </p>
                <button type="submit"
                        class="w-full bg-cyan-700 hover:bg-cyan-800 text-white px-4 py-2 rounded-lg text-sm font-medium">
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
                            <th class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Jumlah</th>
                            <th class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($riwayatPelampung as $r)
                            <tr>
                                <td class="px-3 py-2 text-gray-600">{{ $r->created_at->format('H:i') }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ $r->jumlah }}</td>
                                <td class="px-3 py-2">
                                    @if ($r->status === 'dipinjam')
                                        <span class="px-2 py-0.5 text-xs bg-yellow-100 text-yellow-700 rounded-full">Dipinjam</span>
                                    @else
                                        <span class="px-2 py-0.5 text-xs bg-green-100 text-green-700 rounded-full">Kembali</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-3 py-6 text-center text-gray-400">Belum ada transaksi hari ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- Pelampung yang masih dipinjam --}}
    <div class="bg-white rounded-xl shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Pelampung yang Masih Dipinjam (Semua Petugas)</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Waktu Sewa</th>
                        <th class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Jumlah</th>
                        <th class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Catatan</th>
                        <th class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase">Petugas</th>
                        <th class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($pelampungDipinjam as $p)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $p->created_at->format('d M, H:i') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-800">{{ $p->jumlah }} buah</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $p->catatan ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $p->petugas->name ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-right">
                                <form method="POST" action="{{ route('kolam.pelampung.kembali', $p->id) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-green-600 hover:underline text-xs">
                                        Tandai Kembali
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-400">
                                Tidak ada pelampung yang sedang dipinjam.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

@endsection