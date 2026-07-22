<?php

namespace App\Http\Controllers;

use App\Models\PaketTubing;
use App\Models\TransaksiTubing;
use Illuminate\Http\Request;

class TubingController extends Controller
{
    // Menentukan jenis paket berdasarkan role petugas yang login
    private function jenisPaketSaatIni(): string
    {
        return auth()->user()->role === 'tubing_mini' ? 'mini' : 'dewasa';
    }

    // Halaman utama tubing: form input + riwayat hari ini
    public function dashboard()
    {
        $jenis = $this->jenisPaketSaatIni();

        // Hanya paket yang sesuai jenis & masih tersedia
        $paketList = PaketTubing::where('jenis', $jenis)
            ->where('aktif', true)
            ->get();

        $riwayatHariIni = TransaksiTubing::with('paket')
            ->where('user_id', auth()->id())
            ->whereDate('created_at', today())
            ->latest()
            ->get();

        $totalHariIni = $riwayatHariIni->sum('total_bayar');

        return view('tubing.dashboard', [
            'paketList'      => $paketList,
            'riwayatHariIni' => $riwayatHariIni,
            'totalHariIni'   => $totalHariIni,
            'jenis'          => $jenis,
        ]);
    }

    // Simpan transaksi tubing baru
    public function simpan(Request $request)
    {
        $jenis = $this->jenisPaketSaatIni();

        $request->validate([
            'paket_id'       => 'required|exists:paket_tubing,id',
            'jumlah_peserta' => 'required|integer|min:1',
        ]);

        // Pastikan paket yang dipilih benar-benar sesuai jenis role petugas
        $paket = PaketTubing::where('id', $request->paket_id)
            ->where('jenis', $jenis)
            ->first();

        if (!$paket) {
            return redirect()->route('tubing.dashboard')
                             ->with('error', 'Paket tidak valid untuk wahana ini.');
        }

        $totalBayar = $paket->harga * $request->jumlah_peserta;

        TransaksiTubing::create([
            'user_id'        => auth()->id(),
            'paket_id'       => $paket->id,
            'jumlah_peserta' => $request->jumlah_peserta,
            'total_bayar'    => $totalBayar,
        ]);

        return redirect()->route('tubing.dashboard')
                         ->with('success', 'Transaksi tubing berhasil disimpan!');
    }
}