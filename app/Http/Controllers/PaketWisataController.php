<?php

namespace App\Http\Controllers;

use App\Models\PaketWisata;
use App\Models\TransaksiPaketWisata;
use Illuminate\Http\Request;

class PaketWisataController extends Controller
{
    // Halaman dashboard petugas paket wisata
    public function dashboard()
    {
        $paket = PaketWisata::where('aktif', true)->get();

        $transaksiHariIni = TransaksiPaketWisata::whereDate('created_at', today())
                                                 ->where('user_id', auth()->id())
                                                 ->with('paket')
                                                 ->get();

        $totalHariIni = $transaksiHariIni->sum('total_bayar');

        return view('paket_wisata.dashboard', compact(
            'paket', 'transaksiHariIni', 'totalHariIni'
        ));
    }

    // Simpan transaksi paket wisata
    public function simpan(Request $request)
    {
        $paket = PaketWisata::findOrFail($request->paket_id);

        $request->validate([
            'paket_id'     => 'required|exists:paket_wisata,id',
            'jumlah_orang' => 'required|integer|min:' . $paket->minimal_orang,
            'nama_pemesan' => 'nullable|string|max:100',
            'no_telepon'   => 'nullable|string|max:15',
            'catatan'      => 'nullable|string',
        ], [
            'jumlah_orang.min' => 'Jumlah orang minimal ' . $paket->minimal_orang . ' orang untuk paket ini.',
        ]);

        TransaksiPaketWisata::create([
            'user_id'       => auth()->id(),
            'paket_id'      => $request->paket_id,
            'jumlah_orang'  => $request->jumlah_orang,
            'harga_per_orang' => $paket->harga_per_orang,
            'total_bayar'   => $paket->harga_per_orang * $request->jumlah_orang,
            'nama_pemesan'  => $request->nama_pemesan,
            'no_telepon'    => $request->no_telepon,
            'catatan'       => $request->catatan,
        ]);

        return redirect()->route('paket_wisata.dashboard')
                         ->with('success', 'Transaksi paket wisata berhasil dicatat!');
    }

    // Rekap harian
    public function rekap(Request $request)
    {
        $tanggal = $request->tanggal ?? today()->toDateString();

        $transaksi = TransaksiPaketWisata::whereDate('created_at', $tanggal)
                                          ->where('user_id', auth()->id())
                                          ->with('paket')
                                          ->get();

        $totalPendapatan = $transaksi->sum('total_bayar');

        return view('paket_wisata.rekap', compact(
            'transaksi', 'tanggal', 'totalPendapatan'
        ));
    }
}