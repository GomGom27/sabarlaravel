<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Tabel Persediaan Barang - BPS Kabupaten Bandung</title>
    <link rel="stylesheet" href="{{ asset('css/persediaan_cetak.css') }}">
</head>
<body>
    <div class="no-print" style="margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center; background: #fff7ed; padding: 10px 16px; border: 1px solid #fed7aa; border-radius: 8px; font-family: Arial, sans-serif; font-size: 12px;">
        <span><b>Pratinjau Cetak Tabel Persediaan Barang</b> — BPS Kabupaten Bandung</span>
        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" style="background: #ea580c; color: #fff; border: none; padding: 6px 14px; border-radius: 6px; font-weight: bold; cursor: pointer;">Cetak / Print</button>
            <button onclick="window.close()" style="background: #e5e7eb; color: #374151; border: none; padding: 6px 14px; border-radius: 6px; font-weight: bold; cursor: pointer;">Tutup</button>
        </div>
    </div>

    <div class="doc-container">
        <div style="border-bottom: 2px solid #000000; padding-bottom: 12px; margin-bottom: 16px; text-align: center; position: relative;">
            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center;">
                <div style="display: flex; flex-direction: row; align-items: center; justify-content: center; gap: 12px;">
                    <img src="{{ asset('img/bps_logo.svg') }}" alt="Logo BPS" style="height: 38px; width: auto;">
                    <h2 style="margin: 0; font-size: 15px; font-weight: bold; text-transform: uppercase;">BADAN PUSAT STATISTIK KABUPATEN BANDUNG</h2>
                </div>
                <p style="margin: 3px 0 0 0; font-size: 10px; color: #222;">
                    Jl. Raya Soreang Km. 17 No. 23, Soreang, Kabupaten Bandung, Jawa Barat 40911 &bull; Telepon: (022) 5891726 &bull; Pos-el: bps3204@bps.go.id
                </p>
                <h3 style="margin: 6px 0 0 0; font-size: 13px; font-weight: bold; text-transform: uppercase;">TABEL PERSEDIAAN BARANG</h3>
            </div>
        </div>

        <table class="table-persediaan-bps">
            <thead>
                <tr>
                    <th rowspan="2">No.</th>
                    <th rowspan="2">Uraian Barang</th>
                    <th rowspan="2">Satuan</th>
                    <th rowspan="2">Harga Beli<br>(Rupiah)</th>
                    <th colspan="2">Total Persediaan</th>
                    <th colspan="2">Barang Rusak</th>
                    <th colspan="2">Barang Usang</th>
                </tr>
                <tr>
                    <th>Jumlah</th>
                    <th>Harga Total (Rp)</th>
                    <th>Jumlah</th>
                    <th>Harga Total (Rp)</th>
                    <th>Jumlah</th>
                    <th>Harga Total (Rp)</th>
                </tr>
                <tr>
                    <th>(1)</th><th>(2)</th><th>(3)</th><th>(4)</th><th>(5)</th>
                    <th>(6)</th><th>(7)</th><th>(8)</th><th>(9)</th><th>(10)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $item)
                <tr>
                    <td style="text-align: center;">{{ $item->nomor_urut }}</td>
                    <td>{{ $item->uraian_barang }}</td>
                    <td style="text-align: center;">{{ $item->satuan }}</td>
                    <td style="text-align: right;">Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ number_format($item->total_persediaan_jumlah, 0, ',', '.') }}</td>
                    <td style="text-align: right;">Rp {{ number_format($item->harga_total_persediaan, 0, ',', '.') }}</td>
                    <td style="text-align: center;">{{ $item->rusak_jumlah > 0 ? $item->rusak_jumlah : '-' }}</td>
                    <td style="text-align: right;">{{ $item->rusak_harga_total > 0 ? 'Rp ' . number_format($item->rusak_harga_total, 0, ',', '.') : '-' }}</td>
                    <td style="text-align: center;">{{ $item->usang_jumlah > 0 ? $item->usang_jumlah : '-' }}</td>
                    <td style="text-align: right;">{{ $item->usang_harga_total > 0 ? 'Rp ' . number_format($item->usang_harga_total, 0, ',', '.') : '-' }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" style="text-align: center;">JUMLAH TOTAL</th>
                    <th style="text-align: center;">{{ number_format($summary['total_persediaan_qty'], 0, ',', '.') }}</th>
                    <th style="text-align: right;">Rp {{ number_format($summary['total_persediaan_nilai'], 0, ',', '.') }}</th>
                    <th style="text-align: center;">{{ $summary['total_rusak_qty'] > 0 ? $summary['total_rusak_qty'] : '-' }}</th>
                    <th style="text-align: right;">{{ $summary['total_rusak_nilai'] > 0 ? 'Rp ' . number_format($summary['total_rusak_nilai'], 0, ',', '.') : '-' }}</th>
                    <th style="text-align: center;">{{ $summary['total_usang_qty'] > 0 ? $summary['total_usang_qty'] : '-' }}</th>
                    <th style="text-align: right;">{{ $summary['total_usang_nilai'] > 0 ? 'Rp ' . number_format($summary['total_usang_nilai'], 0, ',', '.') : '-' }}</th>
                </tr>
            </tfoot>
        </table>

        <div style="display: flex; justify-content: space-between; margin-top: 28px; font-size: 11px;">
            <div style="text-align: left;">
                <p style="margin: 0; font-weight: bold;">{{ $pejabat->jabatan_kiri_1 ?? 'Mengetahui,' }}</p>
                <p style="margin: 2px 0 0 0; font-weight: bold;">{{ $pejabat->jabatan_kiri_3 ?? 'Kepala BPS Kabupaten Bandung' }}</p>
                <div style="height: 55px;"></div>
                <p style="margin: 0; font-weight: bold; text-decoration: underline;">{{ $pejabat->nama_pejabat_kiri ?? 'Agus Nuwibowo S.Si., M.M.' }}</p>
                <p style="margin: 2px 0 0 0;">NIP. {{ $pejabat->nip_pejabat_kiri ?? '197208271993121001' }}</p>
            </div>
            <div style="text-align: right;">
                <p style="margin: 0;">{{ $pejabat->tempat_tanggal_kanan ?? 'Soreang, ..............................' }}</p>
                <p style="margin: 2px 0 0 0; font-weight: bold;">{{ $pejabat->jabatan_kanan ?? 'Petugas Pengelola Persediaan' }}</p>
                <div style="height: 55px;"></div>
                <p style="margin: 0; font-weight: bold; text-decoration: underline;">{{ $pejabat->nama_pejabat_kanan ?? 'Firmansyah Saragih, A.Md.' }}</p>
                <p style="margin: 2px 0 0 0;">NIP. {{ $pejabat->nip_pejabat_kanan ?? '198702092022031003' }}</p>
            </div>
        </div>
    </div>
</body>
</html>
