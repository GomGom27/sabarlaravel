<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tabel Persediaan Barang BPS</title>
    <style>
        @page { margin: 18px 20px; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 8.5px; color: #000; margin: 0; }
        .kop-wrapper { text-align: center; border-bottom: 1.5px solid #000; padding-bottom: 6px; margin-bottom: 10px; }
        .kop-title-table { margin: 0 auto; border-collapse: collapse; }
        .kop-title-table td { vertical-align: middle; border: none; padding: 0; }
        .kop-logo { width: 34px; height: auto; margin-right: 8px; }
        .kop-instansi { font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.4px; }
        .kop-alamat { font-size: 8px; margin-top: 2px; color: #222; }
        .kop-judul { font-size: 10px; font-weight: bold; margin-top: 4px; text-transform: uppercase; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.data th, table.data td { border: 0.6px solid #000; padding: 3px 4px; }
        table.data thead th { background-color: #f2f2f2; font-weight: bold; text-align: center; font-size: 7.5px; }
        table.data tfoot th { background-color: #ebebeb; font-weight: bold; font-size: 8px; }
        .tc { text-align: center; }
        .tr { text-align: right; }
        table.ttd { width: 100%; margin-top: 18px; border-collapse: collapse; page-break-inside: avoid; }
        table.ttd td { border: none; vertical-align: top; font-size: 8.5px; }
    </style>
</head>
<body>
    <div class="kop-wrapper">
        <table class="kop-title-table">
            <tr>
                @if(!empty($logoBase64))
                <td><img src="{{ $logoBase64 }}" class="kop-logo" alt="Logo BPS"></td>
                @endif
                <td><span class="kop-instansi">BADAN PUSAT STATISTIK KABUPATEN BANDUNG</span></td>
            </tr>
        </table>
        <div class="kop-alamat">Jl. Raya Soreang Km. 17 No. 23, Soreang, Kabupaten Bandung, Jawa Barat 40911 &bull; Telepon: (022) 5891726 &bull; Pos-el: bps3204@bps.go.id</div>
        <div class="kop-judul">TABEL PERSEDIAAN BARANG</div>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th rowspan="2" style="width: 24px;">No.</th>
                <th rowspan="2">Uraian Barang</th>
                <th rowspan="2" style="width: 45px;">Satuan</th>
                <th rowspan="2" style="width: 68px;">Harga Beli<br>(Rupiah)</th>
                <th colspan="2">Total Persediaan</th>
                <th colspan="2">Barang Rusak</th>
                <th colspan="2">Barang Usang</th>
            </tr>
            <tr>
                <th style="width: 48px;">Jumlah</th>
                <th style="width: 80px;">Harga Total (Rp)</th>
                <th style="width: 40px;">Jumlah</th>
                <th style="width: 68px;">Harga Total (Rp)</th>
                <th style="width: 40px;">Jumlah</th>
                <th style="width: 68px;">Harga Total (Rp)</th>
            </tr>
            <tr>
                <th>(1)</th><th>(2)</th><th>(3)</th><th>(4)</th><th>(5)</th>
                <th>(6)</th><th>(7)</th><th>(8)</th><th>(9)</th><th>(10)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td class="tc">{{ $item->nomor_urut }}</td>
                <td>{{ $item->uraian_barang }}</td>
                <td class="tc">{{ $item->satuan }}</td>
                <td class="tr">Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                <td class="tc">{{ number_format($item->total_persediaan_jumlah, 0, ',', '.') }}</td>
                <td class="tr">Rp {{ number_format($item->harga_total_persediaan, 0, ',', '.') }}</td>
                <td class="tc">{{ $item->rusak_jumlah > 0 ? $item->rusak_jumlah : '-' }}</td>
                <td class="tr">{{ $item->rusak_harga_total > 0 ? 'Rp ' . number_format($item->rusak_harga_total, 0, ',', '.') : '-' }}</td>
                <td class="tc">{{ $item->usang_jumlah > 0 ? $item->usang_jumlah : '-' }}</td>
                <td class="tr">{{ $item->usang_harga_total > 0 ? 'Rp ' . number_format($item->usang_harga_total, 0, ',', '.') : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="tc">JUMLAH TOTAL</th>
                <th class="tc">{{ number_format($summary['total_persediaan_qty'], 0, ',', '.') }}</th>
                <th class="tr">Rp {{ number_format($summary['total_persediaan_nilai'], 0, ',', '.') }}</th>
                <th class="tc">{{ $summary['total_rusak_qty'] > 0 ? $summary['total_rusak_qty'] : '-' }}</th>
                <th class="tr">{{ $summary['total_rusak_nilai'] > 0 ? 'Rp ' . number_format($summary['total_rusak_nilai'], 0, ',', '.') : '-' }}</th>
                <th class="tc">{{ $summary['total_usang_qty'] > 0 ? $summary['total_usang_qty'] : '-' }}</th>
                <th class="tr">{{ $summary['total_usang_nilai'] > 0 ? 'Rp ' . number_format($summary['total_usang_nilai'], 0, ',', '.') : '-' }}</th>
            </tr>
        </tfoot>
    </table>

    <table class="ttd">
        <tr>
            <td style="width: 50%; text-align: left;">
                {{ $pejabat->jabatan_kiri_1 ?? 'Mengetahui' }},<br>
                {{ $pejabat->jabatan_kiri_2 ?? 'Penanggung Jawab KPB' }}<br>
                <b>{{ $pejabat->jabatan_kiri_3 ?? 'Kepala BPS Kabupaten Bandung' }}</b><br><br><br><br>
                <b><u>{{ $pejabat->nama_pejabat_kiri ?? 'Agus Nuwibowo S.Si., M.M.' }}</u></b><br>
                NIP. {{ $pejabat->nip_pejabat_kiri ?? '197208271993121001' }}
            </td>
            <td style="width: 50%; text-align: right;">
                {{ $pejabat->tempat_tanggal_kanan ?? 'Soreang, ..............................' }}<br>
                <b>{{ $pejabat->jabatan_kanan ?? 'Petugas Pengelola Persediaan' }}</b><br><br><br><br><br>
                <b><u>{{ $pejabat->nama_pejabat_kanan ?? 'Firmansyah Saragih, A.Md.' }}</u></b><br>
                NIP. {{ $pejabat->nip_pejabat_kanan ?? '198702092022031003' }}
            </td>
        </tr>
    </table>
</body>
</html>
