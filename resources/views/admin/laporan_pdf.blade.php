<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Pendapatan</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #222;
        }
        h1 {
            font-size: 15px;
            text-align: center;
            margin-bottom: 4px;
        }
        h2 {
            font-size: 12px;
            margin-top: 22px;
            margin-bottom: 6px;
            border-bottom: 1px solid #333;
            padding-bottom: 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        th, td {
            border: 1px solid #444;
            padding: 4px 6px;
            text-align: left;
        }
        th {
            background: #eee;
        }
        .total-row td {
            font-weight: bold;
            background: #f7f7f7;
        }
        .grand-total {
            text-align: right;
            font-size: 13px;
            font-weight: bold;
            margin-top: 18px;
            border-top: 2px solid #222;
            padding-top: 8px;
        }
        .kosong {
            color: #888;
            font-style: italic;
        }
    </style>
</head>
<body>

    <h1>Laporan Pendapatan Periode {{ $labelPeriode }} Desa Wisata Minapadi</h1>

    @forelse ($data as $kategori)
        <h2>{{ $kategori['label_bersih'] }}</h2>

        @if ($kategori['baris']->isEmpty())
            <p class="kosong">Tidak ada transaksi pada periode ini.</p>
        @else
            <table>
                <thead>
                    <tr>
                        @foreach ($kategori['kolom'] as $kolom)
                            <th>{{ $kolom }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kategori['baris'] as $baris)
                        <tr>
                            @foreach ($baris as $sel)
                                <td>{{ $sel }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="{{ count($kategori['kolom']) - 1 }}">Subtotal</td>
                        <td>Rp {{ number_format($kategori['total'], 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        @endif
    @empty
        <p class="kosong">Tidak ada data untuk periode ini.</p>
    @endforelse

    <p class="grand-total">Grand Total: Rp {{ number_format($grandTotal, 0, ',', '.') }}</p>

</body>
</html>