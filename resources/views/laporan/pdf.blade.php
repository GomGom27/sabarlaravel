<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Bulanan Persediaan BPS</title>
    <style>
        @page { margin: 20px 20px; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 8.5px; color: #000; margin: 0; }
        .kop-wrapper { text-align: center; border-bottom: 1.5px solid #000; padding-bottom: 6px; margin-bottom: 10px; }
        .kop-title-table { margin: 0 auto; border-collapse: collapse; }
        .kop-title-table td { border: none; vertical-align: middle; padding: 0; }
        .kop-logo { width: 34px; height: auto; margin-right: 8px; }
        .kop-instansi { font-size: 12px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.4px; }
        .kop-sub { font-size: 9.5px; font-weight: bold; margin-top: 3px; text-transform: uppercase; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.data th, table.data td { border: 0.6px solid #000; padding: 3.5px 4px; }
        table.data thead th { background-color: #f2f2f2; font-weight: bold; text-align: center; font-size: 8px; }
        table.data tfoot th { background-color: #f8f8f8; font-weight: bold; font-size: 8px; }
        .tc { text-align: center; }
        .tr { text-align: right; }
        table.ttd { width: 100%; margin-top: 20px; border-collapse: collapse; page-break-inside: avoid; }
        table.ttd td { border: none; vertical-align: top; font-size: 8.5px; padding: 0; }
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
        <div class="kop-sub">DAFTAR PERSEDIAAN BARANG MILIK NEGARA (BMN) / ATK - PERIODE {{ strtoupper($periodeText) }}</div>
    </div>

    <table class="data">
        <thead>
            <tr>
                <th style="width: 24px;">No</th>
                <th>Uraian Barang</th>
                <th style="width: 45px;">Satuan</th>
                <th style="width: 72px;">Harga Beli (Rp)</th>
                <th style="width: 55px;">Persediaan (Jml)</th>
                <th style="width: 85px;">Harga Total (Rp)</th>
                <th style="width: 65px;">Rusak - Total (Rp)</th>
                <th style="width: 45px;">Rusak (Jml)</th>
                <th style="width: 45px;">Usang (Jml)</th>
                <th style="width: 65px;">Usang - Total (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $itm)
            <tr>
                <td class="tc">{{ $itm->nomor_urut }}</td>
                <td>{{ $itm->uraian_barang }}</td>
                <td class="tc">{{ $itm->satuan }}</td>
                <td class="tr">Rp {{ number_format($itm->harga_beli, 0, ',', '.') }}</td>
                <td class="tc">{{ $itm->total_persediaan_jumlah }}</td>
                <td class="tr">Rp {{ number_format($itm->harga_total_persediaan, 0, ',', '.') }}</td>
                <td class="tc">{{ $itm->rusak_harga_total }}</td>
                <td class="tc">{{ $itm->rusak_jumlah }}</td>
                <td class="tc">{{ $itm->usang_jumlah }}</td>
                <td class="tc">{{ $itm->usang_harga_total }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" class="tc">TOTAL</th>
                <th class="tc">{{ $totalQtyPersediaan }}</th>
                <th class="tr">Rp {{ number_format($totalNilaiPersediaan, 0, ',', '.') }}</th>
                <th class="tc">-</th><th class="tc">-</th><th class="tc">-</th><th class="tc">-</th>
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
