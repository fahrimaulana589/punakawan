<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laporan Penjualan Produk</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
        }
        tfoot td {
            font-weight: bold;
        }
    </style>
</head>
<body>

<h2 style="text-align: center;">Laporan Penjualan Produk</h2>
<p>Periode: {{ request('start_date') }} - {{ request('end_date') }}</p>

<table>
    <thead>
        <tr>
            <th>Kode</th>
            <th>Nama</th>
            <th>Harga</th>
            <th>Terjual</th>
            <th>Total Penjualan</th>
        </tr>
    </thead>
    <tbody>
        @php $grandTotal = 0; @endphp
        @foreach ($produks as $produk)
            @php
                $subTotal = $produk->terjual * $produk->harga;
                $grandTotal += $subTotal;
            @endphp
            <tr>
                <td>{{ $produk->kode }}</td>
                <td>{{ $produk->nama }}</td>
                <td>{{ format_uang($produk->harga) }}</td>
                <td>{{ $produk->terjual }}</td>
                <td>{{ format_uang($subTotal) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="4">Total</td>
            <td>{{ format_uang($grandTotal) }}</td>
        </tr>
    </tfoot>
</table>

</body>
</html>
