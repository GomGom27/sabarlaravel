<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kartu Kendali Persediaan Barang - BPS Kabupaten Bandung</title>
    <link rel="stylesheet" href="{{ asset('css/kartu_kendali_print.css') }}">
</head>
<body>
    <div class="no-print-bar">
        <span><b>Pratinjau Cetak Lembar Kartu Kendali Persediaan Barang (Hanya Lembar Dokumen)</b></span>
        <div>
            <button class="btn" onclick="window.print()">Cetak Kartu Kendali</button>
            <button class="btn" onclick="window.close()" style="background:#fee2e2; color:#991b1b; margin-left:8px;">Tutup</button>
        </div>
    </div>

    <div class="header-container">
        <div class="meta-right"><div><b>Halaman</b> : 1</div></div>
        <div style="display: flex; flex-direction: row; align-items: center; justify-content: center; gap: 12px; margin: 0 auto; width: 100%;">
            <img src="{{ asset('img/bps_logo.svg') }}" alt="Logo BPS" style="width: 48px; height: auto;">
            <div style="text-align: center;">
                <div style="font-size: 10pt; font-weight: bold; text-transform: uppercase;">BADAN PUSAT STATISTIK KABUPATEN BANDUNG</div>
                <div style="font-size: 11.5pt; font-weight: bold; text-transform: uppercase;">KARTU KENDALI PERSEDIAAN BARANG</div>
            </div>
        </div>
    </div>

    <div style="font-size: 8.5pt; margin-bottom: 6px; display: flex; justify-content: space-between;">
        <span><b>Nama Barang:</b> {{ $selectedBarang->nama_barang ?? '-' }}</span>
        <span><b>Kode:</b> {{ $selectedBarang->kode_barang ?? '-' }} | <b>Satuan:</b> {{ $selectedBarang->satuan ?? 'Rim' }}</span>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
        @foreach(['kiri' => $transaksiKiri, 'kanan' => $transaksiKanan] as $bagian => $rows)
        <table class="table-kk">
            <thead>
                <tr>
                    <th rowspan="2">No</th><th rowspan="2">No. Bon</th><th rowspan="2">Tgl</th><th rowspan="2">Uraian</th>
                    <th colspan="3">Masuk</th><th colspan="3">Keluar</th><th rowspan="2">Sisa</th>
                </tr>
                <tr><th>Beli</th><th>Trf</th><th>Lain</th><th>Beli</th><th>Trf</th><th>Lain</th></tr>
            </thead>
            <tbody>
                @foreach($rows as $r)
                <tr>
                    <td style="text-align:center;">{{ $r->baris_no }}</td>
                    <td>{{ $r->no_bon_faktur ?? '' }}</td>
                    <td style="text-align:center;">{{ $r->tgl_mk ?? '' }}</td>
                    <td>{{ $r->uraian_penerima ?? '' }}</td>
                    <td style="text-align:center;">{{ $r->masuk_pembelian ?: '' }}</td>
                    <td style="text-align:center;">{{ $r->masuk_transfer ?: '' }}</td>
                    <td style="text-align:center;">{{ $r->masuk_lainnya ?: '' }}</td>
                    <td style="text-align:center;">{{ $r->keluar_pembelian ?: '' }}</td>
                    <td style="text-align:center;">{{ $r->keluar_transfer ?: '' }}</td>
                    <td style="text-align:center;">{{ $r->keluar_lainnya ?: '' }}</td>
                    <td style="text-align:center; font-weight:bold;">{{ $r->sisa_saldo ?: '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endforeach
    </div>

    <div style="margin-top: 20px; text-align: right; font-size: 8.5pt;">
        <p>{{ $pejabat->tempat_tanggal_kanan ?? 'Soreang, 30 Juni 2026' }}</p>
        <p><b>{{ $pejabat->jabatan_kanan ?? 'Petugas Pengelola Persediaan' }}</b></p>
        <div style="height: 45px;"></div>
        <p style="font-weight: bold; text-decoration: underline;">{{ $pejabat->nama_pejabat_kanan ?? 'Firmansyah Saragih, A.Md.' }}</p>
        <p>NIP. {{ $pejabat->nip_pejabat_kanan ?? '198702092022031003' }}</p>
    </div>
</body>
</html>
