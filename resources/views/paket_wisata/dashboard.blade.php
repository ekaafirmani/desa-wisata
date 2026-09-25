@extends('paket_wisata.layouts.app')

@section('title', 'Dashboard Paket Wisata')

@section('content')

    <h2 class="text-2xl font-bold text-gray-800 mb-6">Transaksi Paket Wisata</h2>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Form transaksi --}}
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="font-semibold text-gray-700 mb-4">Catat Transaksi Baru</h3>

            @if($errors->any())
                <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-800 rounded-lg text-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('paket_wisata.simpan') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Paket</label>
                    <select name="paket_id" id="paketSelect"
                            onchange="updateInfo()"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">-- Pilih Paket --</option>
                        @foreach($paket as $p)
                            <option value="{{ $p->id }}"
                                    data-harga="{{ $p->harga_per_orang }}"
                                    data-minimal="{{ $p->minimal_orang }}"
                                    {{ old('paket_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->nama_paket }} — Rp {{ number_format($p->harga_per_orang, 0, ',', '.') }}/orang
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Info paket --}}
                <div id="infoPaket" class="hidden bg-blue-50 rounded-lg px-4 py-3 text-sm text-blue-700">
                    <p id="infoMinimal"></p>
                    <p id="infoFasilitas"></p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Orang</label>
                    <input type="number" name="jumlah_orang" id="jumlahOrang"
                           value="{{ old('jumlah_orang', 1) }}" min="1"
                           onchange="hitungTotal()"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                {{-- Total bayar --}}
                <div class="bg-gray-50 rounded-lg px-4 py-3">
                    <p class="text-xs text-gray-500">Total Bayar</p>
                    <p id="totalBayar" class="text-xl font-bold text-blue-700">Rp 0</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Pemesan <span class="text-gray-400">(opsional)</span></label>
                    <input type="text" name="nama_pemesan" value="{{ old('nama_pemesan') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="Nama ketua rombongan">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon <span class="text-gray-400">(opsional)</span></label>
                    <input type="text" name="no_telepon" value="{{ old('no_telepon') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                           placeholder="08xxxxxxxxxx">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan <span class="text-gray-400">(opsional)</span></label>
                    <textarea name="catatan" rows="2"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Catatan tambahan...">{{ old('catatan') }}</textarea>
                </div>

                <button type="submit"
                        class="w-full bg-blue-700 text-white py-2 rounded-lg hover:bg-blue-800 font-medium">
                    Simpan Transaksi
                </button>
            </form>
        </div>

        {{-- Riwayat hari ini --}}
        <div class="bg-white rounded-xl shadow p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="font-semibold text-gray-700">Transaksi Hari Ini</h3>
                <span class="text-blue-700 font-bold text-sm">
                    Total: Rp {{ number_format($totalHariIni, 0, ',', '.') }}
                </span>
            </div>

            @forelse($transaksiHariIni as $t)
                <div class="border-b border-gray-100 py-3">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $t->paket->nama_paket }}</p>
                            <p class="text-xs text-gray-500">
                                {{ $t->jumlah_orang }} orang
                                @if($t->nama_pemesan) — {{ $t->nama_pemesan }} @endif
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-bold text-blue-700">
                                Rp {{ number_format($t->total_bayar, 0, ',', '.') }}
                            </p>
                            <p class="text-xs text-gray-400">{{ $t->created_at->format('H:i') }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-gray-400 text-sm">Belum ada transaksi hari ini.</p>
            @endforelse
        </div>

    </div>

    <script>
        const paketData = @json($paket->keyBy('id'));

        function updateInfo() {
            const select = document.getElementById('paketSelect');
            const selectedOption = select.options[select.selectedIndex];
            const infoPaket = document.getElementById('infoPaket');

            if (select.value) {
                const harga = selectedOption.dataset.harga;
                const minimal = selectedOption.dataset.minimal;
                document.getElementById('infoMinimal').textContent = 'Minimal ' + minimal + ' orang';
                infoPaket.classList.remove('hidden');
                document.getElementById('jumlahOrang').min = minimal;
                hitungTotal();
            } else {
                infoPaket.classList.add('hidden');
                document.getElementById('totalBayar').textContent = 'Rp 0';
            }
        }

        function hitungTotal() {
            const select = document.getElementById('paketSelect');
            const jumlah = parseInt(document.getElementById('jumlahOrang').value) || 0;
            const selectedOption = select.options[select.selectedIndex];

            if (select.value && jumlah > 0) {
                const harga = parseInt(selectedOption.dataset.harga) || 0;
                const total = harga * jumlah;
                document.getElementById('totalBayar').textContent = 'Rp ' + total.toLocaleString('id-ID');
            }
        }
    </script>

@endsection