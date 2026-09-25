@extends('admin.layouts.app')

@section('title', 'Kelola Paket Wisata')

@section('content')

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Kelola Paket Wisata</h2>
        <button onclick="document.getElementById('modalTambah').classList.remove('hidden')"
                class="bg-green-700 text-white px-4 py-2 rounded-lg hover:bg-green-800 text-sm">
            + Tambah Paket
        </button>
    </div>

    <div class="bg-white rounded-xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-6 py-3 text-left">No</th>
                        <th class="px-6 py-3 text-left">Nama Paket</th>
                        <th class="px-6 py-3 text-left">Fasilitas</th>
                        <th class="px-6 py-3 text-left">Harga/Orang</th>
                        <th class="px-6 py-3 text-left">Min. Orang</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($paket as $index => $p)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">{{ ($paket->firstItem() ?? 0) + $index }}</td>
                        <td class="px-6 py-4 font-medium">{{ $p->nama_paket }}</td>
                        <td class="px-6 py-4 text-gray-500">{{ $p->fasilitas ?? '-' }}</td>
                        <td class="px-6 py-4">Rp {{ number_format($p->harga_per_orang, 0, ',', '.') }}</td>
                        <td class="px-6 py-4">{{ $p->minimal_orang }} orang</td>
                        <td class="px-6 py-4">
                            @if($p->aktif)
                                <span class="px-2 py-1 rounded-full text-xs bg-green-100 text-green-700">Aktif</span>
                            @else
                                <span class="px-2 py-1 rounded-full text-xs bg-red-100 text-red-700">Nonaktif</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.paket_wisata_tour.edit', $p->id) }}"
                                   class="text-yellow-600 hover:underline text-xs">Edit</a>
                                @if($p->aktif)
                                <form method="POST" action="{{ route('admin.paket_wisata_tour.nonaktifkan', $p->id) }}"
                                      onsubmit="return confirm('Nonaktifkan paket {{ $p->nama_paket }}?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-red-600 hover:underline text-xs">Nonaktifkan</button>
                                </form>
                                @else
                                <form method="POST" action="{{ route('admin.paket_wisata_tour.aktifkan', $p->id) }}"
                                      onsubmit="return confirm('Aktifkan paket {{ $p->nama_paket }}?')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-green-600 hover:underline text-xs">Aktifkan</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-400">
                            Belum ada paket wisata. Tambah paket dulu!
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @include('admin.partials.pagination', ['items' => $paket])

    {{-- Modal Tambah Paket --}}
    <div id="modalTambah" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-xl shadow-lg p-6 w-full max-w-lg mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-800">Tambah Paket Wisata</h3>
                <button onclick="document.getElementById('modalTambah').classList.add('hidden')"
                        class="text-gray-400 hover:text-gray-600">✕</button>
            </div>

            @if($errors->any())
                <div class="mb-4 px-4 py-3 bg-red-100 border border-red-400 text-red-800 rounded-lg text-sm">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.paket_wisata_tour.simpan') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Paket</label>
                    <input type="text" name="nama_paket" value="{{ old('nama_paket') }}"
                           class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                           placeholder="Contoh: Paket Keluarga">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi <span class="text-gray-400">(opsional)</span></label>
                    <textarea name="deskripsi" rows="2"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                              placeholder="Deskripsi singkat paket...">{{ old('deskripsi') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fasilitas <span class="text-gray-400">(opsional)</span></label>
                    <textarea name="fasilitas" rows="2"
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                              placeholder="Contoh: Tubing + Kolam + Makan Siang">{{ old('fasilitas') }}</textarea>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Harga/Orang (Rp)</label>
                        <input type="number" name="harga_per_orang" value="{{ old('harga_per_orang') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                               placeholder="50000">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Minimal Orang</label>
                        <input type="number" name="minimal_orang" value="{{ old('minimal_orang') }}"
                               class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                               placeholder="10">
                    </div>
                </div>
                <div class="pt-2 flex gap-3">
                    <button type="submit"
                            class="flex-1 bg-green-700 text-white py-2 rounded-lg hover:bg-green-800 font-medium">
                        Simpan Paket
                    </button>
                    <button type="button"
                            onclick="document.getElementById('modalTambah').classList.add('hidden')"
                            class="flex-1 bg-gray-100 text-gray-700 py-2 rounded-lg hover:bg-gray-200 font-medium">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Auto buka modal kalau ada error validasi --}}
    @if($errors->any())
    <script>
        document.getElementById('modalTambah').classList.remove('hidden');
    </script>
    @endif

@endsection