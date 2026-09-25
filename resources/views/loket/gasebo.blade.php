@extends('loket.layouts.app')

@section('title', 'Gasebo')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Sewa Gasebo</h2>
        <div class="text-right">
            <p class="text-xs text-gray-500">Total Hari Ini</p>
            <p class="text-lg font-bold text-blue-700">Rp {{ number_format($totalHariIni, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Form Sewa Baru --}}
        <div class="bg-white rounded-xl shadow p-6 lg:col-span-1">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Sewa Baru</h3>

            @if ($errors->any())
                <div class="mb-4 px-3 py-2 bg-red-100 text-red-700 rounded-lg text-xs">
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('loket.gasebo.simpan') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Gasebo</label>
                    <select name="gasebo_id" id="gasebo_id" onchange="hitungTotalGasebo()" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">
                        <option value="">-- Pilih Gasebo --</option>
                        @forelse ($gaseboList as $g)
                            <option value="{{ $g->id }}"
                                    data-harga="{{ $g->harga_per_jam }}"
                                    data-min="{{ $g->durasi_minimal }}"
                                    {{ old('gasebo_id') == $g->id ? 'selected' : '' }}>
                                {{ $g->nama_gasebo }} — Rp {{ number_format($g->harga_per_jam, 0, ',', '.') }}/jam (min. {{ $g->durasi_minimal }} jam)
                            </option>
                        @empty
                            <option value="" disabled>Tidak ada gasebo tersedia</option>
                        @endforelse
                    </select>
                    @error('gasebo_id')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Penyewa <span class="text-gray-400">(opsional)</span></label>
                    <input type="text" name="nama_penyewa" value="{{ old('nama_penyewa') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">
                    @error('nama_penyewa')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Durasi (jam)</label>
                    <input type="number" name="durasi_jam" id="durasi_jam" min="1" value="{{ old('durasi_jam', 1) }}"
                           oninput="hitungTotalGasebo()"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">
                    <p class="text-xs text-gray-400 mt-1" id="ket_durasi_minimal"></p>
                    @error('durasi_jam')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan <span class="text-gray-400">(opsional)</span></label>
                    <textarea name="catatan" rows="2"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">{{ old('catatan') }}</textarea>
                </div>

                {{-- Total Bayar --}}
                <div class="bg-blue-50 rounded-lg px-4 py-3">
                    <p class="text-xs text-gray-500">Total Bayar</p>
                    <p class="text-xl font-bold text-blue-700" id="total_bayar_display">Rp 0</p>
                </div>

                <button type="submit"
                        class="w-full bg-blue-700 hover:bg-blue-800 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    Simpan
                </button>
            </form>
        </div>

        {{-- Sedang Disewa & Riwayat --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Sedang Disewa --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Sedang Disewa</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Gasebo</th>
                                <th class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Penyewa</th>
                                <th class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Mulai</th>
                                <th class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Selesai (Est.)</th>
                                <th class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse ($sewaAktif as $s)
                                <tr>
                                    <td class="px-3 py-2 text-gray-800 font-medium">{{ $s->gasebo->nama_gasebo ?? '-' }}</td>
                                    <td class="px-3 py-2 text-gray-600">{{ $s->nama_penyewa ?? '-' }}</td>
                                    <td class="px-3 py-2 text-gray-600">{{ $s->waktu_mulai->format('d M, H:i') }}</td>
                                    <td class="px-3 py-2 text-gray-600">{{ $s->waktu_selesai?->format('d M, H:i') ?? '-' }}</td>
                                    <td class="px-3 py-2 text-right">
                                        <form method="POST" action="{{ route('loket.gasebo.selesai', $s->id) }}"
                                              onsubmit="return confirm('Tandai sewa gasebo ini selesai?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-blue-700 hover:underline text-xs font-medium">
                                                Tandai Selesai
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-3 py-6 text-center text-gray-400">Tidak ada gasebo yang sedang disewa.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Riwayat Hari Ini --}}
            <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Riwayat Hari Ini (Selesai)</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 border-b">
                            <tr>
                                <th class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Waktu</th>
                                <th class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Gasebo</th>
                                <th class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Penyewa</th>
                                <th class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Durasi</th>
                                <th class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse ($riwayatHariIni as $r)
                                <tr>
                                    <td class="px-3 py-2 text-gray-600">{{ $r->created_at->format('H:i') }}</td>
                                    <td class="px-3 py-2 text-gray-800 font-medium">{{ $r->gasebo->nama_gasebo ?? '-' }}</td>
                                    <td class="px-3 py-2 text-gray-600">{{ $r->nama_penyewa ?? '-' }}</td>
                                    <td class="px-3 py-2 text-gray-600">{{ $r->durasi_jam }} jam</td>
                                    <td class="px-3 py-2 font-medium text-gray-800 text-right">Rp {{ number_format($r->total_bayar, 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-3 py-6 text-center text-gray-400">Belum ada transaksi selesai hari ini.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

    </div>

    <script>
        function hitungTotalGasebo() {
            const select = document.getElementById('gasebo_id');
            const durasiInput = document.getElementById('durasi_jam');
            const selected = select.options[select.selectedIndex];

            const harga = selected ? (parseInt(selected.getAttribute('data-harga')) || 0) : 0;
            const durasiMin = selected ? (parseInt(selected.getAttribute('data-min')) || 1) : 1;
            const durasi = parseInt(durasiInput.value) || 0;

            durasiInput.min = durasiMin;
            document.getElementById('ket_durasi_minimal').textContent =
                selected && selected.value ? 'Durasi minimal untuk gasebo ini: ' + durasiMin + ' jam' : '';

            const total = harga * durasi;
            document.getElementById('total_bayar_display').textContent =
                'Rp ' + total.toLocaleString('id-ID');
        }

        document.addEventListener('DOMContentLoaded', hitungTotalGasebo);
    </script>

@endsection