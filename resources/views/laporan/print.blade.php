<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Bulanan - BPS Kabupaten Bandung</title>
    <link rel="stylesheet" href="{{ asset('css/laporan_print.css') }}">
</head>
<body>
    <div class="no-print-bar">
        <div style="display: flex; align-items: center; gap: 8px;">
            <span><b>Pratinjau Cetak Landscape — Laporan Bulanan</b></span>
            <span style="background: rgba(255,255,255,0.25); padding: 2px 8px; border-radius: 4px; font-size: 8.5pt;">Periode: {{ $periodeText }}</span>
        </div>

        <form method="GET" action="{{ route('laporan.print') }}" style="display: flex; align-items: center; gap: 6px; font-size: 8.5pt;">
            <label style="font-weight: bold;">Bulan:</label>
            <select name="bulan" style="padding: 3px 6px; border-radius: 4px; border: 1px solid #ccc; font-size: 8pt;">
                <option value="">Semua Bulan</option>
                @foreach($daftarBulan as $bln)
                <option value="{{ $bln }}" {{ $selectedBulan === $bln ? 'selected' : '' }}>{{ $bln }}</option>
                @endforeach
            </select>

            <label style="font-weight: bold;">Tahun:</label>
            <select name="tahun" style="padding: 3px 6px; border-radius: 4px; border: 1px solid #ccc; font-size: 8pt;">
                <option value="">Semua Tahun</option>
                @foreach($daftarTahun as $th)
                <option value="{{ $th }}" {{ $selectedTahun === (string)$th ? 'selected' : '' }}>{{ $th }}</option>
                @endforeach
            </select>

            <label style="font-weight: bold;">Tanggal:</label>
            <input type="date" name="tanggal" value="{{ $selectedTanggal }}" style="padding: 2px 4px; border-radius: 4px; border: 1px solid #ccc; font-size: 8pt;">

            <button type="submit" class="btn" style="padding: 3px 10px; font-size: 8pt;">Terapkan Filter</button>
        </form>

        <div style="display: flex; align-items: center; gap: 8px;">
            <button class="btn" onclick="window.print()">Cetak / Simpan PDF</button>
            <button class="btn" onclick="window.close()" style="background:#fee2e2; color:#991b1b;">Tutup</button>
        </div>
    </div>

    <div style="position: relative; border-bottom: 2px solid #000; padding-bottom: 8px; margin-bottom: 10px;">
        <div style="position: absolute; right: 0; top: 0; font-size: 7.5pt; font-family: monospace;">
            <span style="border: 1px solid #ccc; padding: 2px 6px; border-radius: 3px;">BMN.BPS-3204</span>
        </div>
        <div style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
            <div style="display: flex; flex-direction: row; align-items: center; justify-content: center; gap: 12px;">
                <img src="{{ asset('img/bps_logo.svg') }}" alt="Logo BPS" style="width: 44px; height: auto;">
                <div style="text-align: center;">
                    <h2 style="margin: 0; font-size: 11pt; font-weight: bold; text-transform: uppercase;">BADAN PUSAT STATISTIK KABUPATEN BANDUNG</h2>
                    <h3 style="margin: 2px 0 0 0; font-size: 9pt; font-weight: bold; text-transform: uppercase;">DAFTAR PENGAMBILAN &amp; PERSEDIAAN BARANG / ATK</h3>
                </div>
            </div>
            <p style="margin: 3px 0 0 0; font-size: 8pt; color: #444; text-align: center;">Jl. Raya Soreang Km. 17 No. 23, Soreang, Kabupaten Bandung &bull; Periode: {{ $periodeText }}</p>
        </div>
    </div>

    <table class="table-laporan">
        <thead>
            <tr>
                <th style="width: 25px;">No</th>
                <th>Uraian Barang</th>
                <th style="width: 45px;">Satuan</th>
                <th style="width: 75px;">Harga Beli (Rp)</th>
                <th style="width: 55px;">Persediaan (Jml)</th>
                <th style="width: 90px;">Harga Total (Rp)</th>
                <th style="width: 65px;">Rusak Total</th>
                <th style="width: 45px;">Rusak Jml</th>
                <th style="width: 45px;">Usang Jml</th>
                <th style="width: 65px;">Usang Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $itm)
            <tr>
                <td style="text-align: center;">{{ $itm->nomor_urut }}</td>
                <td>{{ $itm->uraian_barang }}</td>
                <td style="text-align: center;">{{ $itm->satuan }}</td>
                <td style="text-align: right;">Rp {{ number_format($itm->harga_beli, 0, ',', '.') }}</td>
                <td style="text-align: center;">{{ $itm->total_persediaan_jumlah }}</td>
                <td style="text-align: right;">Rp {{ number_format($itm->harga_total_persediaan, 0, ',', '.') }}</td>
                <td style="text-align: center;">{{ $itm->rusak_harga_total }}</td>
                <td style="text-align: center;">{{ $itm->rusak_jumlah }}</td>
                <td style="text-align: center;">{{ $itm->usang_jumlah }}</td>
                <td style="text-align: center;">{{ $itm->usang_harga_total }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" style="text-align: center;">TOTAL</th>
                <th style="text-align: center;">{{ $totalQtyPersediaan }}</th>
                <th style="text-align: right;">Rp {{ number_format($totalNilaiPersediaan, 0, ',', '.') }}</th>
                <th>-</th><th>-</th><th>-</th><th>-</th>
            </tr>
        </tfoot>
    </table>

    <div style="margin-top: 25px; display: flex; justify-content: space-between; page-break-inside: avoid; font-size: 8pt;">
        <div style="text-align: left;">
            <p style="margin: 1px 0;">{{ $pejabat->jabatan_kiri_1 ?? 'Mengetahui' }},</p>
            <p style="margin: 1px 0;">{{ $pejabat->jabatan_kiri_2 ?? 'Penanggung Jawab KPB' }}</p>
            <p style="margin: 1px 0; font-weight: bold;">{{ $pejabat->jabatan_kiri_3 ?? 'Kepala BPS Kabupaten Bandung' }}</p>
            <div style="height: 50px;"></div>
            <p style="margin: 1px 0; font-weight: bold; text-decoration: underline;">{{ $pejabat->nama_pejabat_kiri ?? 'Agus Nuwibowo S.Si., M.M.' }}</p>
            <p style="margin: 1px 0;">NIP. {{ $pejabat->nip_pejabat_kiri ?? '197208271993121001' }}</p>
        </div>

        <div style="text-align: right;">
            <p style="margin: 1px 0;">{{ $pejabat->tempat_tanggal_kanan ?? 'Soreang, ..............................' }}</p>
            <p style="margin: 1px 0; font-weight: bold;">{{ $pejabat->jabatan_kanan ?? 'Petugas Pengelola Persediaan' }}</p>
            <div style="height: 50px;"></div>
            <p style="margin: 1px 0; font-weight: bold; text-decoration: underline;">{{ $pejabat->nama_pejabat_kanan ?? 'Firmansyah Saragih, A.Md.' }}</p>
            <p style="margin: 1px 0;">NIP. {{ $pejabat->nip_pejabat_kanan ?? '198702092022031003' }}</p>
        </div>
    </div>
</body>
</html>
