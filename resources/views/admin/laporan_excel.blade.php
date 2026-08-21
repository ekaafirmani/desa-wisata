<table>
    <tr>
        <td colspan="2"><strong>Laporan Pendapatan Periode {{ $labelPeriode }} Desa Wisata Minapadi</strong></td>
    </tr>
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

    <tr>
        <td><strong>Grand Total</strong></td>
        <td><strong>Rp {{ number_format($grandTotal, 0, ',', '.') }}</strong></td>
    </tr>
</table>