<table>
    <tr>
        <td colspan="2" style="font-size: 15px;"><strong>{{ $judulLaporan }} Periode {{ $labelPeriode }}</strong></td>
    </tr>
    <tr><td colspan="2" style="font-size: 15px;">{{ $alamatLaporan }}</td></tr>
    <tr><td colspan="2"></td></tr>

    @forelse ($data as $kategori)
        <tr>
            <td colspan="2"><strong>{{ $kategori['label_bersih'] }}</strong></td>
        </tr>

        @if ($kategori['baris']->isEmpty())
            <tr>
                <td colspan="2">Tidak ada transaksi pada periode ini.</td>
            </tr>
        @else
            <tr>
                @foreach ($kategori['kolom'] as $kolom)
                    <th>{{ $kolom }}</th>
                @endforeach
            </tr>
            @foreach ($kategori['baris'] as $baris)
                <tr>
                    @foreach ($baris as $sel)
                        <td>{{ $sel }}</td>
                    @endforeach
                </tr>
            @endforeach
            <tr>
                <td colspan="{{ count($kategori['kolom']) - 1 }}"><strong>Subtotal</strong></td>
                <td><strong>Rp {{ number_format($kategori['total'], 0, ',', '.') }}</strong></td>
            </tr>
        @endif

        <tr><td colspan="2"></td></tr>
    @empty
        <tr>
            <td colspan="2">Tidak ada data untuk periode ini.</td>
        </tr>
    @endforelse

    @if ($isLaporanPengeluaran)
        <tr>
            <td><strong>Grand Total Pengeluaran</strong></td>
            <td><strong>Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</strong></td>
        </tr>
    @else
        <tr>
            <td><strong>Grand Total Pendapatan</strong></td>
            <td><strong>Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong></td>
        </tr>

        @if ($tampilkanRingkasanBulanan)
            <tr>
                <td><strong>Grand Total Pengeluaran</strong></td>
                <td><strong>Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}</strong></td>
            </tr>
            <tr>
                <td><strong>Pendapatan Bersih</strong></td>
                <td><strong>Rp {{ number_format($totalPendapatanBersih, 0, ',', '.') }}</strong></td>
            </tr>
        @endif
    @endif
    <tr>
        <td colspan="2">Dicetak pada: {{ $tanggalCetak }}</td>
    </tr>
</table>