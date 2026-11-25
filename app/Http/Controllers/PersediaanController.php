<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\LaporanPersediaanItem;
use App\Models\PengaturanPejabat;
use App\Models\PersediaanBarang;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PersediaanController extends Controller
{
    public function index(Request $request)
    {
        $searchQuery = trim($request->query('q', ''));
        $filterKondisi = trim($request->query('filter_kondisi', ''));
        $sortBy = trim($request->query('sort', 'no_asc'));

        $query = PersediaanBarang::query();

        if ($searchQuery !== '') {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('uraian_barang', 'LIKE', "%{$searchQuery}%")
                  ->orWhereRaw('CAST(nomor_urut AS TEXT) LIKE ?', ["%{$searchQuery}%"]);
            });
        }

        if ($filterKondisi === 'rusak') {
            $query->where('rusak_jumlah', '>', 0);
        } elseif ($filterKondisi === 'usang') {
            $query->where('usang_jumlah', '>', 0);
        } elseif ($filterKondisi === 'normal') {
            $query->where('rusak_jumlah', 0)->where('usang_jumlah', 0);
        }

        switch ($sortBy) {
            case 'no_desc':
                $query->orderByDesc('nomor_urut');
                break;
            case 'nama_asc':
                $query->orderBy('uraian_barang');
                break;
            case 'harga_desc':
                $query->orderByDesc('harga_beli');
                break;
            case 'total_desc':
                $query->orderByDesc('harga_total_persediaan');
                break;
            default:
                $query->orderBy('nomor_urut');
                break;
        }

        $items = $query->get();

        $summary = [
            'total_items' => $items->count(),
            'total_persediaan_qty' => $items->sum('total_persediaan_jumlah'),
            'total_persediaan_nilai' => $items->sum('harga_total_persediaan'),
            'total_rusak_qty' => $items->sum('rusak_jumlah'),
            'total_rusak_nilai' => $items->sum('rusak_harga_total'),
            'total_usang_qty' => $items->sum('usang_jumlah'),
            'total_usang_nilai' => $items->sum('usang_harga_total'),
        ];

        $nextNoUrut = (PersediaanBarang::max('nomor_urut') ?? 0) + 1;
        $pejabat = PengaturanPejabat::orderByDesc('id')->first();

        return view('persediaan.index', compact(
            'items',
            'summary',
            'pejabat',
            'searchQuery',
            'filterKondisi',
            'sortBy',
            'nextNoUrut'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nomor_urut' => 'required|integer|min:1',
            'uraian_barang' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'harga_beli' => 'required|numeric|min:0',
            'total_persediaan_jumlah' => 'required|integer|min:0',
            'rusak_jumlah' => 'nullable|integer|min:0',
            'usang_jumlah' => 'nullable|integer|min:0',
        ], [
            'nomor_urut.required' => 'Nomor urut wajib diisi.',
            'uraian_barang.required' => 'Uraian barang wajib diisi.',
            'satuan.required' => 'Satuan wajib diisi.',
        ]);

        $nomorUrut = (int) $request->nomor_urut;
        $uraianBarang = trim($request->uraian_barang);
        $satuan = trim($request->satuan);
        $hargaBeli = (float) $request->harga_beli;
        $totalJumlah = (int) $request->total_persediaan_jumlah;
        $rusakJumlah = (int) ($request->input('rusak_jumlah') ?: 0);
        $usangJumlah = (int) ($request->input('usang_jumlah') ?: 0);
        $keterangan = trim($request->input('keterangan', ''));

        if (PersediaanBarang::where('nomor_urut', $nomorUrut)->exists()) {
            return redirect()->route('persediaan.index')
                ->with('danger', "Gagal menyimpan: Nomor urut {$nomorUrut} sudah ada di tabel persediaan.");
        }

        $hargaTotalPersediaan = $hargaBeli * $totalJumlah;
        $rusakHargaTotal = $hargaBeli * $rusakJumlah;
        $usangHargaTotal = $hargaBeli * $usangJumlah;

        PersediaanBarang::create([
            'nomor_urut' => $nomorUrut,
            'uraian_barang' => $uraianBarang,
            'satuan' => $satuan,
            'harga_beli' => $hargaBeli,
            'total_persediaan_jumlah' => $totalJumlah,
            'harga_total_persediaan' => $hargaTotalPersediaan,
            'rusak_jumlah' => $rusakJumlah,
            'rusak_harga_total' => $rusakHargaTotal,
            'usang_jumlah' => $usangJumlah,
            'usang_harga_total' => $usangHargaTotal,
            'keterangan' => $keterangan,
        ]);

        // Sinkron ke tabel barang
        if (!Barang::whereRaw('LOWER(TRIM(nama_barang)) = LOWER(TRIM(?))', [$uraianBarang])->exists()) {
            $kat = KategoriBarang::first();
            $katId = $kat ? $kat->id : 1;
            $kodeB = sprintf('1.01.03.01.%04d', Barang::count() + 1);
            Barang::create([
                'nama_barang' => $uraianBarang,
                'kode_barang' => $kodeB,
                'kategori_id' => $katId,
                'satuan' => $satuan,
            ]);
        }

        // Sinkron ke laporan_persediaan_item
        if (!LaporanPersediaanItem::whereRaw('LOWER(TRIM(uraian_barang)) = LOWER(TRIM(?))', [$uraianBarang])->exists()) {
            $maxLap = LaporanPersediaanItem::max('nomor_urut') ?? 0;
            LaporanPersediaanItem::create([
                'nomor_urut' => $maxLap + 1,
                'uraian_barang' => $uraianBarang,
                'satuan' => $satuan,
                'harga_beli' => $hargaBeli,
                'total_persediaan_jumlah' => $totalJumlah,
                'harga_total_persediaan' => $hargaTotalPersediaan,
                'rusak_harga_total' => $rusakJumlah > 0 ? (string) $rusakJumlah : '-',
                'rusak_jumlah' => $rusakHargaTotal > 0 ? (string) $rusakHargaTotal : '-',
                'usang_jumlah' => $usangJumlah > 0 ? (string) $usangJumlah : '-',
                'usang_harga_total' => $usangHargaTotal > 0 ? (string) $usangHargaTotal : '-',
                'periode_bulan' => 'Juni',
                'periode_tahun' => 2026,
                'tanggal_laporan' => date('Y-m-d'),
            ]);
        }

        return redirect()->route('persediaan.index')
            ->with('success', "DATA BARANG BERHASIL DITAMBAHKAN! (\"{$uraianBarang}\")");
    }

    public function update(Request $request, int $id)
    {
        $item = PersediaanBarang::find($id);
        if (!$item) {
            return redirect()->route('persediaan.index')->with('danger', 'Data barang tidak ditemukan.');
        }

        $request->validate([
            'nomor_urut' => 'required|integer|min:1',
            'uraian_barang' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
            'harga_beli' => 'required|numeric|min:0',
            'total_persediaan_jumlah' => 'required|integer|min:0',
        ]);

        $nomorUrut = (int) $request->nomor_urut;
        if (PersediaanBarang::where('nomor_urut', $nomorUrut)->where('id', '!=', $id)->exists()) {
            return redirect()->route('persediaan.index')
                ->with('danger', "Gagal menyimpan: Nomor urut {$nomorUrut} telah digunakan oleh barang lain.");
        }

        $hargaBeli = (float) $request->harga_beli;
        $totalJumlah = (int) $request->total_persediaan_jumlah;
        $rusakJumlah = (int) ($request->input('rusak_jumlah') ?: 0);
        $usangJumlah = (int) ($request->input('usang_jumlah') ?: 0);

        $item->update([
            'nomor_urut' => $nomorUrut,
            'uraian_barang' => trim($request->uraian_barang),
            'satuan' => trim($request->satuan),
            'harga_beli' => $hargaBeli,
            'total_persediaan_jumlah' => $totalJumlah,
            'harga_total_persediaan' => $hargaBeli * $totalJumlah,
            'rusak_jumlah' => $rusakJumlah,
            'rusak_harga_total' => $hargaBeli * $rusakJumlah,
            'usang_jumlah' => $usangJumlah,
            'usang_harga_total' => $hargaBeli * $usangJumlah,
            'keterangan' => trim($request->input('keterangan', '')),
        ]);

        return redirect()->route('persediaan.index')
            ->with('success', "Perubahan data barang \"{$item->uraian_barang}\" berhasil disimpan!");
    }

    public function destroy(int $id)
    {
        $item = PersediaanBarang::find($id);
        $nama = $item ? $item->uraian_barang : 'Barang';
        if ($item) {
            $item->delete();
        }

        return redirect()->route('persediaan.index')
            ->with('success', "DATA BARANG BERHASIL DIHAPUS! (\"{$nama}\")");
    }

    public function destroyAll(Request $request)
    {
        PersediaanBarang::query()->delete();
        return redirect()->route('persediaan.index')
            ->with('success', 'Seluruh data pada tabel Persediaan Barang berhasil dihapus.');
    }

    public function updatePejabat(Request $request)
    {
        $request->validate([
            'nama_pejabat_kiri' => 'required|string|max:150',
            'nama_pejabat_kanan' => 'required|string|max:150',
        ]);

        $pejabat = PengaturanPejabat::orderByDesc('id')->first();
        $data = [
            'nama_pejabat_kiri' => trim($request->nama_pejabat_kiri),
            'nip_pejabat_kiri' => trim($request->input('nip_pejabat_kiri', '')),
            'tempat_tanggal_kanan' => trim($request->input('tempat_tanggal_kanan', '')),
            'nama_pejabat_kanan' => trim($request->nama_pejabat_kanan),
            'nip_pejabat_kanan' => trim($request->input('nip_pejabat_kanan', '')),
        ];

        if ($pejabat) {
            $pejabat->update($data);
        } else {
            PengaturanPejabat::create($data);
        }

        $redirectTo = $request->input('redirect_to');
        return redirect($redirectTo ?: route('persediaan.index'))
            ->with('success', 'Data Kepala BPS dan Petugas Pengelola Persediaan berhasil diperbarui!');
    }

    public function cetak()
    {
        $items = PersediaanBarang::orderBy('nomor_urut')->get();
        $summary = [
            'total_items' => $items->count(),
            'total_persediaan_qty' => $items->sum('total_persediaan_jumlah'),
            'total_persediaan_nilai' => $items->sum('harga_total_persediaan'),
            'total_rusak_qty' => $items->sum('rusak_jumlah'),
            'total_rusak_nilai' => $items->sum('rusak_harga_total'),
            'total_usang_qty' => $items->sum('usang_jumlah'),
            'total_usang_nilai' => $items->sum('usang_harga_total'),
        ];
        $pejabat = PengaturanPejabat::orderByDesc('id')->first();

        return view('persediaan.cetak', compact('items', 'summary', 'pejabat'));
    }

    public function exportExcel()
    {
        $items = PersediaanBarang::orderBy('nomor_urut')->get();
        $pejabat = PengaturanPejabat::orderByDesc('id')->first();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Persediaan Barang');

        $sheet->mergeCells('A1:J1')->setCellValue('A1', 'BADAN PUSAT STATISTIK KABUPATEN BANDUNG');
        $sheet->mergeCells('A2:J2')->setCellValue('A2', 'Jl. Raya Soreang Km. 17 No. 23, Soreang, Kabupaten Bandung, Jawa Barat 40911');
        $sheet->mergeCells('A3:J3')->setCellValue('A3', 'TABEL PERSEDIAAN BARANG');

        $sheet->getStyle('A1')->getFont()->setName('Arial')->setSize(13)->setBold(true);
        $sheet->getStyle('A2')->getFont()->setName('Arial')->setSize(9);
        $sheet->getStyle('A3')->getFont()->setName('Arial')->setSize(11)->setBold(true);
        $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Header bertingkat (row 5-7)
        $sheet->mergeCells('A5:A6')->setCellValue('A5', 'No.');
        $sheet->mergeCells('B5:B6')->setCellValue('B5', 'Uraian Barang');
        $sheet->mergeCells('C5:C6')->setCellValue('C5', 'Satuan');
        $sheet->mergeCells('D5:D6')->setCellValue('D5', "Harga Beli\n(Rupiah)");
        $sheet->mergeCells('E5:F5')->setCellValue('E5', 'Total Persediaan');
        $sheet->mergeCells('G5:H5')->setCellValue('G5', 'Barang Rusak');
        $sheet->mergeCells('I5:J5')->setCellValue('I5', 'Barang Usang');

        $sheet->setCellValue('E6', 'Jumlah')->setCellValue('F6', 'Harga Total (Rp)');
        $sheet->setCellValue('G6', 'Jumlah')->setCellValue('H6', 'Harga Total (Rp)');
        $sheet->setCellValue('I6', 'Jumlah')->setCellValue('J6', 'Harga Total (Rp)');

        for ($i = 1; $i <= 10; $i++) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i);
            $sheet->setCellValue("{$col}7", "({$i})");
        }

        $headerStyle = [
            'font' => ['name' => 'Arial', 'size' => 9, 'bold' => true],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F2F2F2']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ];
        $sheet->getStyle('A5:J7')->applyFromArray($headerStyle);

        $row = 8;
        $totQty = 0;
        $totNilai = 0;
        $totRusakQty = 0;
        $totRusakNilai = 0;
        $totUsangQty = 0;
        $totUsangNilai = 0;

        foreach ($items as $item) {
            $totQty += $item->total_persediaan_jumlah;
            $totNilai += $item->harga_total_persediaan;
            $totRusakQty += $item->rusak_jumlah;
            $totRusakNilai += $item->rusak_harga_total;
            $totUsangQty += $item->usang_jumlah;
            $totUsangNilai += $item->usang_harga_total;

            $sheet->setCellValue("A{$row}", $item->nomor_urut);
            $sheet->setCellValue("B{$row}", $item->uraian_barang);
            $sheet->setCellValue("C{$row}", $item->satuan);
            $sheet->setCellValue("D{$row}", $item->harga_beli);
            $sheet->setCellValue("E{$row}", $item->total_persediaan_jumlah);
            $sheet->setCellValue("F{$row}", $item->harga_total_persediaan);
            $sheet->setCellValue("G{$row}", $item->rusak_jumlah > 0 ? $item->rusak_jumlah : '-');
            $sheet->setCellValue("H{$row}", $item->rusak_harga_total > 0 ? $item->rusak_harga_total : '-');
            $sheet->setCellValue("I{$row}", $item->usang_jumlah > 0 ? $item->usang_jumlah : '-');
            $sheet->setCellValue("J{$row}", $item->usang_harga_total > 0 ? $item->usang_harga_total : '-');

            $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                'font' => ['name' => 'Arial', 'size' => 9],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("E{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("D{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("F{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $row++;
        }

        // Baris total
        $sheet->mergeCells("A{$row}:D{$row}")->setCellValue("A{$row}", 'JUMLAH TOTAL');
        $sheet->setCellValue("E{$row}", $totQty);
        $sheet->setCellValue("F{$row}", $totNilai);
        $sheet->setCellValue("G{$row}", $totRusakQty > 0 ? $totRusakQty : '-');
        $sheet->setCellValue("H{$row}", $totRusakNilai > 0 ? $totRusakNilai : '-');
        $sheet->setCellValue("I{$row}", $totUsangQty > 0 ? $totUsangQty : '-');
        $sheet->setCellValue("J{$row}", $totUsangNilai > 0 ? $totUsangNilai : '-');
        $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
            'font' => ['name' => 'Arial', 'size' => 9, 'bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EBEBEB']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $sheet->getStyle("F{$row}")->getNumberFormat()->setFormatCode('#,##0');

        // Tanda Tangan
        $row += 3;
        $tempatTanggal = $pejabat->tempat_tanggal_kanan ?? 'Soreang, 30 Juni 2026';
        $sheet->setCellValue("B{$row}", 'Mengetahui,');
        $sheet->setCellValue("H{$row}", $tempatTanggal);
        $row++;
        $sheet->setCellValue("B{$row}", 'Kepala BPS Kabupaten Bandung');
        $sheet->setCellValue("H{$row}", 'Petugas Pengelola Persediaan');
        $row += 4;
        $sheet->setCellValue("B{$row}", $pejabat->nama_pejabat_kiri ?? 'Agus Nuwibowo S.Si., M.M.');
        $sheet->setCellValue("H{$row}", $pejabat->nama_pejabat_kanan ?? 'Firmansyah Saragih, A.Md.');
        $sheet->getStyle("B{$row}")->getFont()->setBold(true)->setUnderline(true);
        $sheet->getStyle("H{$row}")->getFont()->setBold(true)->setUnderline(true);
        $row++;
        $sheet->setCellValue("B{$row}", 'NIP. ' . ($pejabat->nip_pejabat_kiri ?? '197208271993121001'));
        $sheet->setCellValue("H{$row}", 'NIP. ' . ($pejabat->nip_pejabat_kanan ?? '198702092022031003'));

        $widths = ['A' => 6, 'B' => 36, 'C' => 10, 'D' => 16, 'E' => 12, 'F' => 18, 'G' => 10, 'H' => 16, 'I' => 10, 'J' => 16];
        foreach ($widths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        $writer = new Xlsx($spreadsheet);
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, 'tabel_persediaan_barang_bps.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function exportPdf()
    {
        $items = PersediaanBarang::orderBy('nomor_urut')->get();
        $summary = [
            'total_persediaan_qty' => $items->sum('total_persediaan_jumlah'),
            'total_persediaan_nilai' => $items->sum('harga_total_persediaan'),
            'total_rusak_qty' => $items->sum('rusak_jumlah'),
            'total_rusak_nilai' => $items->sum('rusak_harga_total'),
            'total_usang_qty' => $items->sum('usang_jumlah'),
            'total_usang_nilai' => $items->sum('usang_harga_total'),
        ];
        $pejabat = PengaturanPejabat::orderByDesc('id')->first();

        $logoBase64 = '';
        $logoPath = public_path('img/bps_logo.jpg');
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('persediaan.pdf', compact('items', 'summary', 'pejabat', 'logoBase64'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('tabel_persediaan_barang_bps.pdf');
    }
}
