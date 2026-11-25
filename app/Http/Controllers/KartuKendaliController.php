<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\KartuKendaliBulanan;
use App\Models\KartuKendaliTransaksi;
use App\Models\PengaturanPejabat;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class KartuKendaliController extends Controller
{
    public function index(Request $request)
    {
        $barangList = Barang::orderBy('id')->get();
        $barangId = $request->query('barang_id');

        if (!$barangId) {
            $hvs = Barang::where('nama_barang', 'HVS F4 75 Gram')->first();
            $barangId = $hvs ? $hvs->id : ($barangList->first()->id ?? 1);
        }

        $selectedBarang = Barang::find($barangId);
        $bulanan = KartuKendaliBulanan::where('barang_id', $barangId)->first();
        $transaksiKiri = KartuKendaliTransaksi::where('barang_id', $barangId)
            ->where('bagian', 'kiri')->orderBy('baris_no')->get();
        $transaksiKanan = KartuKendaliTransaksi::where('barang_id', $barangId)
            ->where('bagian', 'kanan')->orderBy('baris_no')->get();

        return view('kartu_kendali.index', compact(
            'barangList',
            'selectedBarang',
            'bulanan',
            'transaksiKiri',
            'transaksiKanan'
        ));
    }

    public function tambahTransaksi(Request $request)
    {
        $barangId = (int) $request->input('barang_id', 1);
        $noBon = trim($request->input('no_bon_faktur', ''));
        $tgl = trim($request->input('tgl_mk', ''));
        $uraian = trim($request->input('uraian_penerima', ''));
        $tipe = $request->input('tipe_transaksi');
        $bagian = trim($request->input('bagian', 'kiri'));
        $jumlah = (int) ($request->input('jumlah') ?: 0);
        $fromDashboard = $request->input('from_dashboard');

        $barisKosong = KartuKendaliTransaksi::where('barang_id', $barangId)
            ->where('bagian', $bagian)
            ->where(function ($q) {
                $q->whereNull('no_bon_faktur')->orWhere('no_bon_faktur', '');
            })
            ->orderBy('baris_no')
            ->first();

        $lastSaldoRow = KartuKendaliTransaksi::where('barang_id', $barangId)
            ->where('no_bon_faktur', '!=', '')
            ->orderByDesc('bagian')
            ->orderByDesc('baris_no')
            ->first();
        $lastSaldo = $lastSaldoRow ? (int) $lastSaldoRow->sisa_saldo : 0;

        $masukPembelian = $tipe === 'masuk_pembelian' ? $jumlah : 0;
        $keluarTransfer = $tipe === 'keluar_transfer' ? $jumlah : 0;
        $keluarPembelian = $tipe === 'keluar_pembelian' ? $jumlah : 0;
        $newSaldo = $lastSaldo + $masukPembelian - $keluarTransfer - $keluarPembelian;

        if ($barisKosong) {
            $barisKosong->update([
                'no_bon_faktur' => $noBon,
                'tgl_mk' => $tgl,
                'uraian_penerima' => $uraian,
                'masuk_pembelian' => $masukPembelian,
                'keluar_transfer' => $keluarTransfer,
                'keluar_pembelian' => $keluarPembelian,
                'sisa_saldo' => $newSaldo,
            ]);
        } else {
            $maxNo = KartuKendaliTransaksi::where('barang_id', $barangId)
                ->where('bagian', $bagian)->max('baris_no') ?? 10;
            KartuKendaliTransaksi::create([
                'barang_id' => $barangId,
                'baris_no' => $maxNo + 1,
                'bagian' => $bagian,
                'no_bon_faktur' => $noBon,
                'tgl_mk' => $tgl,
                'uraian_penerima' => $uraian,
                'masuk_pembelian' => $masukPembelian,
                'keluar_transfer' => $keluarTransfer,
                'keluar_pembelian' => $keluarPembelian,
                'sisa_saldo' => $newSaldo,
            ]);
        }

        $redirect = $fromDashboard
            ? redirect()->route('dashboard')
            : redirect()->route('kartu_kendali.index', ['barang_id' => $barangId]);

        return $redirect->with('success', 'Transaksi Kartu Kendali berhasil ditambahkan.');
    }

    public function editTransaksi(Request $request, int $id)
    {
        $row = KartuKendaliTransaksi::find($id);
        $barangId = $request->input('barang_id');
        $fromDashboard = $request->input('from_dashboard');

        if ($row) {
            $row->update([
                'no_bon_faktur' => trim($request->input('no_bon_faktur', '')),
                'tgl_mk' => trim($request->input('tgl_mk', '')),
                'uraian_penerima' => trim($request->input('uraian_penerima', '')),
                'masuk_pembelian' => (int) ($request->input('masuk_pembelian') ?: 0),
                'masuk_transfer' => (int) ($request->input('masuk_transfer') ?: 0),
                'masuk_lainnya' => (int) ($request->input('masuk_lainnya') ?: 0),
                'keluar_pembelian' => (int) ($request->input('keluar_pembelian') ?: 0),
                'keluar_transfer' => (int) ($request->input('keluar_transfer') ?: 0),
                'keluar_lainnya' => (int) ($request->input('keluar_lainnya') ?: 0),
                'sisa_saldo' => (int) ($request->input('sisa_saldo') ?: 0),
            ]);
        }

        $redirect = $fromDashboard
            ? redirect()->route('dashboard')
            : redirect()->route('kartu_kendali.index', ['barang_id' => $barangId]);

        return $redirect->with('success', 'Baris transaksi Kartu Kendali berhasil diperbarui.');
    }

    public function hapusTransaksi(Request $request, int $id)
    {
        $row = KartuKendaliTransaksi::find($id);
        $barangId = $request->input('barang_id');
        $fromDashboard = $request->input('from_dashboard');

        if ($row) {
            if ($row->baris_no <= 10) {
                $row->update([
                    'no_bon_faktur' => '',
                    'tgl_mk' => '',
                    'uraian_penerima' => '',
                    'masuk_pembelian' => 0,
                    'masuk_transfer' => 0,
                    'masuk_lainnya' => 0,
                    'keluar_pembelian' => 0,
                    'keluar_transfer' => 0,
                    'keluar_lainnya' => 0,
                    'sisa_saldo' => 0,
                ]);
            } else {
                $row->delete();
            }
        }

        $redirect = $fromDashboard
            ? redirect()->route('dashboard')
            : redirect()->route('kartu_kendali.index', ['barang_id' => $barangId]);

        return $redirect->with('success', 'Baris transaksi Kartu Kendali telah dihapus/dikosongkan.');
    }

    public function hapusSemua(Request $request)
    {
        $barangId = (int) ($request->input('barang_id') ?: 1);
        $scope = $request->input('scope', 'selected');

        if ($scope === 'all') {
            KartuKendaliTransaksi::where('baris_no', '<=', 10)->update([
                'no_bon_faktur' => '', 'tgl_mk' => '', 'uraian_penerima' => '',
                'masuk_pembelian' => 0, 'masuk_transfer' => 0, 'masuk_lainnya' => 0,
                'keluar_pembelian' => 0, 'keluar_transfer' => 0, 'keluar_lainnya' => 0,
                'sisa_saldo' => 0,
            ]);
            KartuKendaliTransaksi::where('baris_no', '>', 10)->delete();
            $msg = 'Seluruh transaksi Kartu Kendali untuk SEMUA barang telah berhasil dikosongkan/dihapus.';
        } else {
            KartuKendaliTransaksi::where('barang_id', $barangId)->where('baris_no', '<=', 10)->update([
                'no_bon_faktur' => '', 'tgl_mk' => '', 'uraian_penerima' => '',
                'masuk_pembelian' => 0, 'masuk_transfer' => 0, 'masuk_lainnya' => 0,
                'keluar_pembelian' => 0, 'keluar_transfer' => 0, 'keluar_lainnya' => 0,
                'sisa_saldo' => 0,
            ]);
            KartuKendaliTransaksi::where('barang_id', $barangId)->where('baris_no', '>', 10)->delete();
            $msg = 'Seluruh data transaksi Kartu Kendali untuk barang yang dipilih telah dikosongkan/dihapus.';
        }

        return redirect()->route('kartu_kendali.index', ['barang_id' => $barangId])->with('success', $msg);
    }

    public function print(Request $request)
    {
        $barangId = $request->query('barang_id');
        if (!$barangId) {
            $hvs = Barang::where('nama_barang', 'HVS F4 75 Gram')->first();
            $barangId = $hvs ? $hvs->id : (Barang::first()->id ?? 1);
        }

        $selectedBarang = Barang::find($barangId);
        $bulanan = KartuKendaliBulanan::where('barang_id', $barangId)->first();
        $transaksiKiri = KartuKendaliTransaksi::where('barang_id', $barangId)
            ->where('bagian', 'kiri')->orderBy('baris_no')->get();
        $transaksiKanan = KartuKendaliTransaksi::where('barang_id', $barangId)
            ->where('bagian', 'kanan')->orderBy('baris_no')->get();
        $pejabat = PengaturanPejabat::orderByDesc('id')->first();

        return view('kartu_kendali.print', compact('selectedBarang', 'bulanan', 'transaksiKiri', 'transaksiKanan', 'pejabat'));
    }

    public function exportExcel(Request $request)
    {
        $barangId = $request->query('barang_id');
        if (!$barangId) {
            $hvs = Barang::where('nama_barang', 'HVS F4 75 Gram')->first();
            $barangId = $hvs ? $hvs->id : (Barang::first()->id ?? 1);
        }

        $selectedBarang = Barang::find($barangId);
        $transaksiKiri = KartuKendaliTransaksi::where('barang_id', $barangId)
            ->where('bagian', 'kiri')->orderBy('baris_no')->get();
        $transaksiKanan = KartuKendaliTransaksi::where('barang_id', $barangId)
            ->where('bagian', 'kanan')->orderBy('baris_no')->get();

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Kartu Kendali');

        $sheet->mergeCells('A1:J1')->setCellValue('A1', 'BADAN PUSAT STATISTIK KABUPATEN BANDUNG');
        $sheet->mergeCells('A2:J2')->setCellValue('A2', 'KARTU KENDALI PERSEDIAAN BARANG');
        $sheet->getStyle('A1:A2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A1:A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $namaBarang = $selectedBarang->nama_barang ?? 'HVS F4 75 Gram';
        $kodeBarang = $selectedBarang->kode_barang ?? '-';
        $satuan = $selectedBarang->satuan ?? 'Rim';

        $sheet->setCellValue('A4', "Nama Barang: {$namaBarang}");
        $sheet->setCellValue('F4', "Kode: {$kodeBarang}");
        $sheet->setCellValue('I4', "Satuan: {$satuan}");

        $headers = ['No', 'Bagian', 'No. Bon / Faktur', 'Tanggal', 'Uraian / Penerima', 'Masuk (Beli)', 'Masuk (Lain)', 'Keluar (Transfer)', 'Keluar (Lain)', 'Sisa Saldo'];
        $colIdx = 1;
        foreach ($headers as $h) {
            $col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx++);
            $sheet->setCellValue("{$col}6", $h);
        }
        $sheet->getStyle('A6:J6')->applyFromArray([
            'font' => ['bold' => true, 'size' => 9],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F2F2F2']],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $row = 7;
        $all = $transaksiKiri->concat($transaksiKanan);
        foreach ($all as $t) {
            $sheet->setCellValue("A{$row}", $t->baris_no);
            $sheet->setCellValue("B{$row}", strtoupper($t->bagian));
            $sheet->setCellValue("C{$row}", $t->no_bon_faktur ?: '-');
            $sheet->setCellValue("D{$row}", $t->tgl_mk ?: '-');
            $sheet->setCellValue("E{$row}", $t->uraian_penerima ?: '-');
            $sheet->setCellValue("F{$row}", $t->masuk_pembelian);
            $sheet->setCellValue("G{$row}", $t->masuk_transfer + $t->masuk_lainnya);
            $sheet->setCellValue("H{$row}", $t->keluar_transfer);
            $sheet->setCellValue("I{$row}", $t->keluar_pembelian + $t->keluar_lainnya);
            $sheet->setCellValue("J{$row}", $t->sisa_saldo);
            $sheet->getStyle("A{$row}:J{$row}")->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $row++;
        }

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $filename = 'Kartu_Kendali_' . str_replace(' ', '_', $namaBarang) . '.xlsx';
        $writer = new Xlsx($spreadsheet);
        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function exportPdf(Request $request)
    {
        $barangId = $request->query('barang_id');
        if (!$barangId) {
            $hvs = Barang::where('nama_barang', 'HVS F4 75 Gram')->first();
            $barangId = $hvs ? $hvs->id : (Barang::first()->id ?? 1);
        }

        $selectedBarang = Barang::find($barangId);
        $bulanan = KartuKendaliBulanan::where('barang_id', $barangId)->first();
        $transaksiKiri = KartuKendaliTransaksi::where('barang_id', $barangId)
            ->where('bagian', 'kiri')->orderBy('baris_no')->get();
        $transaksiKanan = KartuKendaliTransaksi::where('barang_id', $barangId)
            ->where('bagian', 'kanan')->orderBy('baris_no')->get();
        $pejabat = PengaturanPejabat::orderByDesc('id')->first();

        $logoBase64 = '';
        $logoPath = public_path('img/bps_logo.jpg');
        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = Pdf::loadView('kartu_kendali.pdf', compact(
            'selectedBarang',
            'bulanan',
            'transaksiKiri',
            'transaksiKanan',
            'pejabat',
            'logoBase64'
        ))->setPaper('a4', 'landscape');

        return $pdf->download('Kartu_Kendali_' . str_replace(' ', '_', $selectedBarang->nama_barang ?? 'Barang') . '.pdf');
    }
}
