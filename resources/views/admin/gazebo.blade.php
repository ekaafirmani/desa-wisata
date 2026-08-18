@extends('admin.layouts.app')

@section('title', 'Gazebo')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Sewa Gazebo</h2>
        <div class="text-right">
            <p class="text-xs text-gray-500">Total Hari Ini</p>
            <p class="text-lg font-bold text-green-700">Rp {{ number_format($totalHariIni, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Form --}}
        <div class="bg-white rounded-xl shadow p-6 lg:col-span-1">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Sewa Baru</h3>
            <form method="POST" action="{{ route('admin.gazebo.simpan') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Gazebo</label>
                    <select name="jenis_gazebo" id="jenis_gazebo" onchange="toggleNomorGazebo(); hitungTotalGazebo();"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                        <option value="">-- Pilih Jenis --</option>
                        <option value="besar" data-harga="{{ $stokGazeboBesar->harga_satuan ?? 0 }}" {{ old('jenis_gazebo') == 'besar' ? 'selected' : '' }}>
                            Gazebo Besar (Rp {{ number_format($stokGazeboBesar->harga_satuan ?? 0, 0, ',', '.') }}) — Tersedia: {{ $stokGazeboBesar->tersedia ?? 0 }}
                        </option>
                        <option value="kecil" data-harga="{{ $stokGazeboKecil->harga_satuan ?? 0 }}" {{ old('jenis_gazebo') == 'kecil' ? 'selected' : '' }}>
                            Gazebo Kecil (Rp {{ number_format($stokGazeboKecil->harga_satuan ?? 0, 0, ',', '.') }}) — Tersedia: {{ $stokGazeboKecil->tersedia ?? 0 }}
                        </option>
                    </select>
                    @error('jenis_gazebo')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah Gazebo</label>
                    <input type="number" name="jumlah" id="jumlah_gazebo" min="1" value="{{ old('jumlah', 1) }}"
                           oninput="hitungTotalGazebo()"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                    @error('jumlah')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Dropdown nomor gazebo besar --}}
                <div id="wrapper_nomor_besar" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Nomor Gazebo Besar</label>
                    <select name="nomor_gazebo[]" id="nomor_besar" multiple size="5"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                        @forelse ($nomorBesarTersedia as $n)
                            <option value="{{ $n }}">No. {{ $n }}</option>
                        @empty
                            <option value="" disabled>Tidak ada nomor tersedia</option>
                        @endforelse
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Tahan Ctrl (Windows) / Cmd (Mac) untuk pilih lebih dari satu, sesuai jumlah di atas.</p>
                </div>

                {{-- Dropdown nomor gazebo kecil --}}
                <div id="wrapper_nomor_kecil" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Nomor Gazebo Kecil</label>
                    <select name="nomor_gazebo[]" id="nomor_kecil" multiple size="5"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-600">
                        @forelse ($nomorKecilTersedia as $n)
                            <option value="{{ $n }}">No. {{ $n }}</option>
                        @empty
                            <option value="" disabled>Tidak ada nomor tersedia</option>
                        @endforelse
                    </select>
                    <p class="text-xs text-gray-400 mt-1">Tahan Ctrl (Windows) / Cmd (Mac) untuk pilih lebih dari satu, sesuai jumlah di atas.</p>
                </div>
                @error('nomor_gazebo')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror


                {{-- Total Bayar --}}
                <div class="bg-green-50 rounded-lg px-4 py-3">
                    <p class="text-xs text-gray-500">Total Bayar</p>
                    <p class="text-xl font-bold text-green-700" id="total_bayar_display">Rp 0</p>
                </div>

                <button type="submit"
                        class="w-full bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-medium">
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
                            <th class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Jenis</th>
                            <th class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Jumlah</th>
                            <th class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Harga Satuan</th>
                            <th class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Total</th>
                            <th class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Catatan</th>
                            <th class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse ($riwayatGazebo as $r)
                            <tr>
                                <td class="px-3 py-2 text-gray-600">{{ $r->created_at->format('H:i') }}</td>
                                <td class="px-3 py-2 text-gray-600 capitalize">Gazebo {{ $r->jenis_gazebo }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ $r->jumlah }}</td>
                                <td class="px-3 py-2 text-gray-600">Rp {{ number_format($r->harga_satuan, 0, ',', '.') }}</td>
                                <td class="px-3 py-2 font-medium text-gray-800">Rp {{ number_format($r->total_bayar, 0, ',', '.') }}</td>
                                <td class="px-3 py-2 text-gray-600">{{ $r->catatan ?? '-' }}</td>
                                <td class="px-3 py-2">
                                    @if ($r->status === 'disewa')
                                        <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700 rounded-full">Disewa</span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-medium bg-green-100 text-green-700 rounded-full">Kembali</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="px-3 py-6 text-center text-gray-400">Belum ada transaksi hari ini.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- Gazebo yang masih disewa --}}
    <div class="bg-white rounded-xl shadow p-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Gazebo yang Masih Disewa (Semua Petugas)</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Waktu Sewa</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Jenis</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Jumlah</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Catatan</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase">Petugas</th>
                        <th class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse ($gazeboDisewa as $g)
                        <tr>
                            <td class="px-3 py-2 text-gray-600">{{ $g->created_at->format('d M, H:i') }}</td>
                            <td class="px-3 py-2 text-gray-600 capitalize">Gazebo {{ $g->jenis_gazebo }}</td>
                            <td class="px-3 py-2 text-gray-600">{{ $g->jumlah }} buah</td>
                            <td class="px-3 py-2 text-gray-600">{{ $g->catatan ?? '-' }}</td>
                            <td class="px-3 py-2 text-gray-600">{{ $g->petugas->name ?? '-' }}</td>
                            <td class="px-3 py-2 text-right">
                                <form method="POST" action="{{ route('admin.gazebo.kembali', $g->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-green-700 hover:underline text-xs font-medium">
                                        Tandai Kembali
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-3 py-6 text-center text-gray-400">Tidak ada gazebo yang sedang disewa.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function toggleNomorGazebo() {
            const jenis = document.getElementById('jenis_gazebo').value;
            const besarWrap = document.getElementById('wrapper_nomor_besar');
            const kecilWrap = document.getElementById('wrapper_nomor_kecil');
            const besarSelect = document.getElementById('nomor_besar');
            const kecilSelect = document.getElementById('nomor_kecil');

            if (jenis === 'besar') {
                besarWrap.classList.remove('hidden');
                kecilWrap.classList.add('hidden');
                besarSelect.disabled = false;
                kecilSelect.disabled = true;
                kecilSelect.selectedIndex = -1;
            } else if (jenis === 'kecil') {
                kecilWrap.classList.remove('hidden');
                besarWrap.classList.add('hidden');
                kecilSelect.disabled = false;
                besarSelect.disabled = true;
                besarSelect.selectedIndex = -1;
            } else {
                besarWrap.classList.add('hidden');
                kecilWrap.classList.add('hidden');
                besarSelect.disabled = true;
                kecilSelect.disabled = true;
            }
        }

        function hitungTotalGazebo() {
            const jenisSelect = document.getElementById('jenis_gazebo');
            const jumlahInput = document.getElementById('jumlah_gazebo');
            const selectedOption = jenisSelect.options[jenisSelect.selectedIndex];

            const harga = selectedOption ? (parseInt(selectedOption.getAttribute('data-harga')) || 0) : 0;
            const jumlah = parseInt(jumlahInput.value) || 0;
            const total = harga * jumlah;

            document.getElementById('total_bayar_display').textContent =
                'Rp ' + total.toLocaleString('id-ID');
        }

        document.addEventListener('DOMContentLoaded', function () {
            toggleNomorGazebo();
            hitungTotalGazebo();
        });
    </script>

@endsection