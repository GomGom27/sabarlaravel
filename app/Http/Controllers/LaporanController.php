<?php

namespace App\Http\Controllers;

use App\Models\LaporanPersediaanItem;
use App\Models\PengaturanPejabat;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class LaporanController extends Controller
{
    private array $daftarBulan = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
    ];

    private array $bulanMap = [
        'januari' => '01', 'februari' => '02', 'maret' => '03', 'april' => '04',
        'mei' => '05', 'juni' => '06', 'juli' => '07', 'agustus' => '08',
        'september' => '09', 'oktober' => '10', 'november' => '11', 'desember' => '12',
    ];

    private function getFilteredItems(string $bulan, string $tahun, string $tanggal, string $q): array
    {
        $query = LaporanPersediaanItem::query();

        if ($tanggal !== '') {
            $query->where('tanggal_laporan', $tanggal);
        } else {
            if ($bulan !== '') {
                $blnNum = $this->bulanMap[strtolower($bulan)] ?? null;
                if ($blnNum) {
                    $query->where(function ($sub) use ($bulan, $blnNum) {
                        $sub->whereRaw('LOWER(periode_bulan) = LOWER(?)', [$bulan])
                            ->orWhereRaw("strftime('%m', tanggal_laporan) = ?", [$blnNum]);
                    });
                } else {
                    $query->whereRaw('LOWER(periode_bulan) = LOWER(?)', [$bulan]);
                }
            }
            if ($tahun !== '') {
                $query->where(function ($sub) use ($tahun) {
                    $sub->whereRaw('CAST(periode_tahun AS TEXT) = ?', [$tahun])
                        ->orWhereRaw("strftime('%Y', tanggal_laporan) = ?", [$tahun]);
                });
            }
        }

        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('uraian_barang', 'LIKE', "%{$q}%")
                    ->orWhere('satuan', 'LIKE', "%{$q}%");
            });
        }

        $items = $query->orderBy('nomor_urut')->orderBy('id')->get();

        if ($tanggal !== '') {
            $periodeText = "Tanggal {$tanggal}";
        } elseif ($bulan !== '' && $tahun !== '') {
            $periodeText = "{$bulan} {$tahun}";
        } elseif ($bulan !== '') {
            $periodeText = "Bulan {$bulan}";
        } elseif ($tahun !== '') {
            $periodeText = "Tahun {$tahun}";
        } else {
            $periodeText = 'Semua Periode';
        }

        return [$items, $periodeText];
    }

    private function getFilterOptions(): array
    {
        $thRows = DB::table('laporan_persediaan_item')
            ->whereNotNull('periode_tahun')->where('periode_tahun', '!=', '')
            ->distinct()->pluck('periode_tahun')->toArray();

        $thFromTgl = DB::table('laporan_persediaan_item')
            ->whereNotNull('tanggal_laporan')->where('tanggal_laporan', '!=', '')
            ->selectRaw("DISTINCT strftime('%Y', tanggal_laporan) as th")
            ->pluck('th')->toArray();

        $years = array_unique(array_merge([2024, 2025, 2026, (int) date('Y')], array_map('intval', $thRows), array_map('intval', array_filter($thFromTgl))));
        rsort($years);

        return [$this->daftarBulan, $years];
    }

    public function index(Request $request)
    {
        $bulan = trim($request->query('bulan', ''));
        $tahun = trim($request->query('tahun', ''));
        $tanggal = trim($request->query('tanggal', ''));
        $q = trim($request->query('q', ''));

        [$daftarBulan, $daftarTahun] = $this->getFilterOptions();
        [$items, $periodeText] = $this->getFilteredItems($bulan, $tahun, $tanggal, $q);

        $totalNilaiPersediaan = $items->sum('harga_total_persediaan');
        $totalQtyPersediaan = $items->sum('total_persediaan_jumlah');
        $pejabat = PengaturanPejabat::orderByDesc('id')->first();

        return view('laporan.index', [
            'items' => $items,
            'totalNilaiPersediaan' => $totalNilaiPersediaan,
            'totalQtyPersediaan' => $totalQtyPersediaan,
            'pejabat' => $pejabat,
            'selectedBulan' => $bulan,
            'selectedTahun' => $tahun,
            'selectedTanggal' => $tanggal,
            'searchQ' => $q,
            'periodeText' => $periodeText,
            'daftarBulan' => $daftarBulan,
            'daftarTahun' => $daftarTahun,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'uraian_barang' => 'required|string|max:255',
            'satuan' => 'required|string|max:50',
        ]);

        $hargaBeli = (float) ($request->input('harga_beli') ?: 0);
        $totalQty = (int) ($request->input('total_persediaan_jumlah') ?: 0);
        $periodeBulan = trim($request->input('periode_bulan', 'Juni')) ?: 'Juni';
        $periodeTahun = (int) ($request->input('periode_tahun') ?: 2026);
        $tanggalLaporan = trim($request->input('tanggal_laporan', '')) ?: date('Y-m-d');

        $maxNo = LaporanPersediaanItem::max('nomor_urut') ?? 0;

        LaporanPersediaanItem::create([
            'nomor_urut' => $maxNo + 1,
            'uraian_barang' => trim($request->uraian_barang),
            'satuan' => trim($request->satuan),
            'harga_beli' => $hargaBeli,
            'total_persediaan_jumlah' => $totalQty,
            'harga_total_persediaan' => $hargaBeli * $totalQty,
            'rusak_harga_total' => trim($request->input('rusak_harga_total', '-')) ?: '-',
            'rusak_jumlah' => trim($request->input('rusak_jumlah', '-')) ?: '-',
            'usang_jumlah' => trim($request->input('usang_jumlah', '-')) ?: '-',
            'usang_harga_total' => trim($request->input('usang_harga_total', '-')) ?: '-',
            'periode_bulan' => $periodeBulan,
            'periode_tahun' => $periodeTahun,
            'tanggal_laporan' => $tanggalLaporan,
        ]);

        return redirect()->route('laporan.index', ['bulan' => $periodeBulan, 'tahun' => $periodeTahun])
            ->with('success', "Item \"{$request->uraian_barang}\" berhasil ditambahkan ke Laporan Bulanan ({$periodeBulan} {$periodeTahun}).");
    }

    public function update(Request $request, int $id)
    {
        $item = LaporanPersediaanItem::find($id);
        if (!$item) {
            return redirect()->route('laporan.index')->with('danger', 'Item tidak ditemukan.');
        }

        $hargaBeli = (float) ($request->input('harga_beli') ?: 0);
        $totalQty = (int) ($request->input('total_persediaan_jumlah') ?: 0);
        $periodeBulan = trim($request->input('periode_bulan', 'Juni')) ?: 'Juni';
        $periodeTahun = (int) ($request->input('periode_tahun') ?: 2026);

        $item->update([
            'uraian_barang' => trim($request->uraian_barang),
            'satuan' => trim($request->input('satuan', 'Pcs')),
            'harga_beli' => $hargaBeli,
            'total_persediaan_jumlah' => $totalQty,
            'harga_total_persediaan' => $hargaBeli * $totalQty,
            'rusak_harga_total' => trim($request->input('rusak_harga_total', '-')) ?: '-',
            'rusak_jumlah' => trim($request->input('rusak_jumlah', '-')) ?: '-',
            'usang_jumlah' => trim($request->input('usang_jumlah', '-')) ?: '-',
            'usang_harga_total' => trim($request->input('usang_harga_total', '-')) ?: '-',
            'periode_bulan' => $periodeBulan,
            'periode_tahun' => $periodeTahun,
            'tanggal_laporan' => trim($request->input('tanggal_laporan', '')) ?: '2026-06-30',
        ]);

        return redirect()->route('laporan.index', ['bulan' => $periodeBulan, 'tahun' => $periodeTahun])
            ->with('success', "Data persediaan \"{$item->uraian_barang}\" berhasil diperbarui.");
    }

    public function destroy(int $id)
    {
        $item = LaporanPersediaanItem::find($id);
        if ($item) {
            $nama = $item->uraian_barang;
            $item->delete();
            return redirect()->route('laporan.index')
                ->with('success', "Item persediaan \"{$nama}\" telah dihapus dari laporan.");
        }

        return redirect()->route('laporan.index');
    }

    public function destroyAll()
    {
        LaporanPersediaanItem::query()->delete();
        return redirect()->route('laporan.index')
            ->with('success', 'Seluruh data persediaan pada Laporan Bulanan berhasil dihapus sekaligus.');
    }

    public function print(Request $request)
    {
        $bulan = trim($request->query('bulan', ''));
        $tahun = trim($request->query('tahun', ''));
        $tanggal = trim($request->query('tanggal', ''));
        $q = trim($request->query('q', ''));

        [$daftarBulan, $daftarTahun] = $this->getFilterOptions();
        [$items, $periodeText] = $this->getFilteredItems($bulan, $tahun, $tanggal, $q);

        $totalNilaiPersediaan = $items->sum('harga_total_persediaan');
        $totalQtyPersediaan = $items->sum('total_persediaan_jumlah');
        $pejabat = PengaturanPejabat::orderByDesc('id')->first();

        return view('laporan.print', [
            'items' => $items,
            'totalNilaiPersediaan' => $totalNilaiPersediaan,
            'totalQtyPersediaan' => $totalQtyPersediaan,
            'pejabat' => $pejabat,
            'selectedBulan' => $bulan,
            'selectedTahun' => $tahun,
            'selectedTanggal' => $tanggal,
            'searchQ' => $q,
            'periodeText' => $periodeText,
            'daftarBulan' => $daftarBulan,
            'daftarTahun' => $daftarTahun,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $bulan = trim($request->query('bulan', ''));
        $tahun = trim($request->query('tahun', ''));
        $tanggal = trim($request->query('tanggal', ''));
        $q = trim($request->query('q', ''));

        [$items, $periodeText] = $this->getFilteredItems($bulan, $tahun, $tanggal, $q);
        $pejabat = PengaturanPejabat::orderByDesc('id')->first();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Persediaan');

        $sheet->mergeCells('A1:J1')->setCellValue('A1', 'BADAN PUSAT STATISTIK KABUPATEN BANDUNG');
        $sheet->mergeCells('A2:J2')->setCellValue('A2', 'DAFTAR PERSEDIAAN BARANG MILIK NEGARA (BMN) / ATK');
        $sheet->mergeCells('A3:J3')->setCellValue('A3', 'PERIODE: ' . strtoupper($periodeText));

        $sheet->getStyle('A1')->getFont()->setName('Arial')->setSize(12)->setBold(true);
        $sheet->getStyle('A2:A3')->getFont()->setName('Arial')->setSize(10)->setBold(true);
        $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $headers = ['No', 'Uraian Barang', 'Satuan', 'Harga Beli (Rp)', 'Persediaan (Jml)', 'Harga Total (Rp)', 'Rusak - Total (Rp)', 'Rusak (Jml)', 'Usang (Jml)', 'Usang - Total (Rp)'];
        $colIdx = 1;
        foreach ($headers as $h) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx++);
            $sheet->setCellValue("{$col}5", $h);
        }
        $sheet->getStyle('A5:J5')->applyFromArray([
            'font' => ['name' => 'Arial', 'size' => 9, 'bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F2F2F2']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $row = 6;
        $totalQty = 0;
        $totalNilai = 0;

        foreach ($items as $item) {
            $totalQty += $item->total_persediaan_jumlah;
            $totalNilai += $item->harga_total_persediaan;

            $sheet->setCellValue("A{$row}", $item->nomor_urut);
            $sheet->setCellValue("B{$row}", $item->uraian_barang);
            $sheet->setCellValue("C{$row}", $item->satuan);
            $sheet->setCellValue("D{$row}", $item->harga_beli);
            $sheet->setCellValue("E{$row}", $item->total_persediaan_jumlah);
            $sheet->setCellValue("F{$row}", $item->harga_total_persediaan);
            $sheet->setCellValue("G{$row}", $item->rusak_harga_total);
            $sheet->setCellValue("H{$row}", $item->rusak_jumlah);
            $sheet->setCellValue("I{$row}", $item->usang_jumlah);
            $sheet->setCellValue("J{$row}", $item->usang_harga_total);

            $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
                'font' => ['name' => 'Arial', 'size' => 9],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
            $sheet->getStyle("D{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $sheet->getStyle("F{$row}")->getNumberFormat()->setFormatCode('#,##0');
            $row++;
        }

        $sheet->mergeCells("A{$row}:D{$row}")->setCellValue("A{$row}", 'TOTAL');
        $sheet->setCellValue("E{$row}", $totalQty);
        $sheet->setCellValue("F{$row}", $totalNilai);
        $sheet->setCellValue("G{$row}", '-')->setCellValue("H{$row}", '-')->setCellValue("I{$row}", '-')->setCellValue("J{$row}", '-');
        $sheet->getStyle("A{$row}:J{$row}")->applyFromArray([
            'font' => ['name' => 'Arial', 'size' => 9, 'bold' => true],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8F8F8']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);
        $sheet->getStyle("F{$row}")->getNumberFormat()->setFormatCode('#,##0');

        // Tanda Tangan
        $row += 2;
        $sheet->setCellValue("B{$row}", 'Mengetahui,');
        $sheet->setCellValue("H{$row}", $pejabat->tempat_tanggal_kanan ?? 'Soreang, ..............................');
        $row++;
        $sheet->setCellValue("B{$row}", 'Penanggung Jawab KPB');
        $sheet->setCellValue("H{$row}", 'Petugas Pengelola Persediaan');
        $row++;
        $sheet->setCellValue("B{$row}", 'Kepala BPS Kabupaten Bandung');
        $row += 4;
        $sheet->setCellValue("B{$row}", $pejabat->nama_pejabat_kiri ?? 'Agus Nuwibowo S.Si., M.M.');
        $sheet->setCellValue("H{$row}", $pejabat->nama_pejabat_kanan ?? 'Firmansyah Saragih, A.Md.');
        $sheet->getStyle("B{$row}")->getFont()->setBold(true)->setUnderline(true);
        $sheet->getStyle("H{$row}")->getFont()->setBold(true)->setUnderline(true);
        $row++;
        $sheet->setCellValue("B{$row}", 'NIP. ' . ($pejabat->nip_pejabat_kiri ?? '197208271993121001'));
        $sheet->setCellValue("H{$row}", 'NIP. ' . ($pejabat->nip_pejabat_kanan ?? '198702092022031003'));

        $widths = ['A' => 6, 'B' => 38, 'C' => 10, 'D' => 16, 'E' => 14, 'F' => 22, 'G' => 18, 'H' => 14, 'I' => 14, 'J' => 18];
        foreach ($widths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        $periodeClean = str_replace(' ', '_', ($bulan && $tahun) ? "{$bulan}_{$tahun}" : ($bulan ?: ($tahun ?: 'Semua_Periode')));
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, "Laporan_Persediaan_BPS_{$periodeClean}.xlsx", [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function exportPdf(Request $request)
    {
        $bulan = trim($request->query('bulan', ''));
        $tahun = trim($request->query('tahun', ''));
        $tanggal = trim($request->query('tanggal', ''));
        $q = trim($request->query('q', ''));

        [$items, $periodeText] = $this->getFilteredItems($bulan, $tahun, $tanggal, $q);
        $totalQtyPersediaan = $items->sum('total_persediaan_jumlah');
        $totalNilaiPersediaan = $items->sum('harga_total_persediaan');
        $pejabat = PengaturanPejabat::orderByDesc('id')->first();

        $logoBase64 = '';
        $logoPath = public_path('img/bps_logo.jpg');
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('laporan.pdf', compact(
            'items',
            'periodeText',
            'totalQtyPersediaan',
            'totalNilaiPersediaan',
            'pejabat',
            'logoBase64'
        ))->setPaper('a4', 'landscape');

        $periodeClean = str_replace(' ', '_', ($bulan && $tahun) ? "{$bulan}_{$tahun}" : ($bulan ?: ($tahun ?: 'Semua_Periode')));
        return $pdf->download("Laporan_Persediaan_BPS_{$periodeClean}.pdf");
    }
}
