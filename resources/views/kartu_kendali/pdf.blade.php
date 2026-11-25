<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Kendali Persediaan Barang</title>
    <style>
        @page { margin: 18px 20px; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 8px; color: #000; }
        .kop { text-align: center; border-bottom: 1.5px solid #000; padding-bottom: 6px; margin-bottom: 10px; }
        .kop-table { margin: 0 auto; border-collapse: collapse; }
        .kop-table td { border: none; vertical-align: middle; padding: 0; }
        table.data { width: 100%; border-collapse: collapse; margin-top: 6px; }
        table.data th, table.data td { border: 0.6px solid #000; padding: 3px 4px; }
        table.data th { background: #f2f2f2; text-align: center; font-weight: bold; }
        .tc { text-align: center; }
    </style>
</head>
<body>
    <div class="kop">
        <table class="kop-table">
            <tr>
                @if(!empty($logoBase64))
                <td><img src="{{ $logoBase64 }}" style="width: 34px; margin-right: 8px;" alt="Logo BPS"></td>
                @endif
                <td style="text-align: left;">
                    <div style="font-size: 11px; font-weight: bold;">BADAN PUSAT STATISTIK KABUPATEN BANDUNG</div>
                    <div style="font-size: 9.5px; font-weight: bold;">KARTU KENDALI PERSEDIAAN BARANG</div>
                </td>
            </tr>
        </table>
    </div>

    <div style="margin-bottom: 6px; font-size: 8.5px;">
        <b>Nama Barang:</b> {{ $selectedBarang->nama_barang ?? '-' }} &nbsp;|&nbsp;
        <b>Kode:</b> {{ $selectedBarang->kode_barang ?? '-' }} &nbsp;|&nbsp;
        <b>Satuan:</b> {{ $selectedBarang->satuan ?? 'Rim' }}
    </div>

    <table class="data">
        <thead>
            <tr>
                <th>No</th><th>Bagian</th><th>No. Bon / Faktur</th><th>Tgl M/K</th><th>Uraian / Penerima</th>
                <th>Masuk Beli</th><th>Masuk Lain</th><th>Keluar Trf</th><th>Keluar Lain</th><th>Sisa Saldo</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaksiKiri->concat($transaksiKanan) as $r)
            <tr>
                <td class="tc">{{ $r->baris_no }}</td>
                <td class="tc">{{ strtoupper($r->bagian) }}</td>
                <td>{{ $r->no_bon_faktur ?: '-' }}</td>
                <td class="tc">{{ $r->tgl_mk ?: '-' }}</td>
                <td>{{ $r->uraian_penerima ?: '-' }}</td>
                <td class="tc">{{ $r->masuk_pembelian }}</td>
                <td class="tc">{{ $r->masuk_transfer + $r->masuk_lainnya }}</td>
                <td class="tc">{{ $r->keluar_transfer }}</td>
                <td class="tc">{{ $r->keluar_pembelian + $r->keluar_lainnya }}</td>
                <td class="tc"><b>{{ $r->sisa_saldo }}</b></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 18px; text-align: right; font-size: 8.5px;">
        {{ $pejabat->tempat_tanggal_kanan ?? 'Soreang, 30 Juni 2026' }}<br>
        <b>{{ $pejabat->jabatan_kanan ?? 'Petugas Pengelola Persediaan' }}</b><br><br><br><br>
        <b><u>{{ $pejabat->nama_pejabat_kanan ?? 'Firmansyah Saragih, A.Md.' }}</u></b><br>
        NIP. {{ $pejabat->nip_pejabat_kanan ?? '198702092022031003' }}
    </div>
</body>
</html>
