@extends('kuliner.layouts.app')

@section('title', 'Transaksi Kuliner')

@section('content')

    <h2 class="text-2xl font-bold text-gray-800 mb-6">Transaksi Kuliner</h2>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Form keranjang --}}
        <div class="bg-white rounded-xl shadow p-6 lg:col-span-1">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Pesanan Baru</h3>

            @if ($menuList->isEmpty())
                <p class="text-sm text-gray-500">
                    Belum ada menu yang tersedia. Tambahkan menu lewat menu "Kelola Menu" di samping.
                </p>
            @else
                <form method="POST" action="{{ route('kuliner.simpan') }}" id="formTransaksi" class="space-y-4">
                    @csrf

                    <div id="keranjang" class="space-y-3">
                        {{-- Baris item akan ditambahkan di sini lewat JavaScript --}}
                    </div>

                    <button type="button" onclick="tambahBaris()"
                            class="w-full border-2 border-dashed border-purple-300 text-purple-600 hover:bg-purple-50 px-4 py-2 rounded-lg text-sm font-medium">
                        + Tambah Item
                    </button>

                    <div class="bg-purple-50 rounded-lg p-4">
                        <p class="text-xs text-gray-500">Total Bayar</p>
                        <p class="text-2xl font-bold text-purple-700" id="totalBayar">Rp 0</p>
                    </div>

                    <button type="submit" id="btnSimpan" disabled
                            class="w-full bg-purple-700 hover:bg-purple-800 disabled:bg-gray-300 disabled:cursor-not-allowed text-white px-4 py-2 rounded-lg text-sm font-medium">
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

            <div class="space-y-3">
                @forelse ($riwayatHariIni as $r)
                    <div class="border rounded-lg p-4">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs text-gray-500">{{ $r->created_at->format('H:i') }}</span>
                            <span class="font-semibold text-gray-800">Rp {{ number_format($r->total_bayar, 0, ',', '.') }}</span>
                        </div>
                        <ul class="text-sm text-gray-600 space-y-1">
                            @foreach ($r->detail as $d)
                                <li>{{ $d->menu->nama_menu ?? 'Menu dihapus' }} x{{ $d->jumlah }} — Rp {{ number_format($d->subtotal, 0, ',', '.') }}</li>
                            @endforeach
                        </ul>
                    </div>
                @empty
                    <p class="text-center text-sm text-gray-400 py-8">Belum ada transaksi hari ini.</p>
                @endforelse
            </div>
        </div>

    </div>

    {{-- Data menu untuk dipakai JavaScript --}}
    <script>
        const daftarMenu = [
            @foreach ($menuList as $m)
                { id: {{ $m->id }}, nama: @json($m->nama_menu), harga: {{ $m->harga }} },
            @endforeach
        ];

        let jumlahBaris = 0;

        function tambahBaris() {
            jumlahBaris++;
            const idBaris = jumlahBaris;

            let opsiMenu = '<option value="">-- Pilih Menu --</option>';
            daftarMenu.forEach(m => {
                opsiMenu += `<option value="${m.id}" data-harga="${m.harga}">${m.nama} (Rp ${m.harga.toLocaleString('id-ID')})</option>`;
            });

            const baris = document.createElement('div');
            baris.id = 'baris-' + idBaris;
            baris.className = 'flex gap-2 items-start border-b pb-3';
            baris.innerHTML = `
                <div class="flex-1">
                    <select name="menu_id[]" onchange="hitungTotal()"
                            class="w-full border border-gray-300 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-600">
                        ${opsiMenu}
                    </select>
                </div>
                <div class="w-20">
                    <input type="number" name="jumlah[]" min="1" value="1" onchange="hitungTotal()" oninput="hitungTotal()"
                           class="w-full border border-gray-300 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-600">
                </div>
                <button type="button" onclick="hapusBaris(${idBaris})"
                        class="text-red-500 hover:text-red-700 px-2 py-2 text-sm">
                    ✕
                </button>
            `;

            document.getElementById('keranjang').appendChild(baris);
            hitungTotal();
        }

        function hapusBaris(id) {
            const baris = document.getElementById('baris-' + id);
            if (baris) baris.remove();
            hitungTotal();
        }

        function hitungTotal() {
            let total = 0;
            let adaItemValid = false;

            document.querySelectorAll('#keranjang > div').forEach(baris => {
                const select = baris.querySelector('select');
                const input = baris.querySelector('input');
                const selected = select.options[select.selectedIndex];
                const harga = selected ? parseInt(selected.dataset.harga) || 0 : 0;
                const jumlah = parseInt(input.value) || 0;

                if (select.value && jumlah > 0) {
                    adaItemValid = true;
                }

                total += harga * jumlah;
            });

            document.getElementById('totalBayar').textContent = 'Rp ' + total.toLocaleString('id-ID');
            document.getElementById('btnSimpan').disabled = !adaItemValid;
        }

        // Mulai dengan 1 baris kosong
        tambahBaris();
    </script>

@endsection