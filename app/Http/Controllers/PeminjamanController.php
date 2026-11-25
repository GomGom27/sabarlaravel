<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\KategoriBarang;
use App\Models\LaporanPersediaanItem;
use App\Models\Peminjaman;
use App\Models\PersediaanBarang;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PeminjamanController extends Controller
{
    public function index(Request $request)
    {
        $statusFilter = trim($request->query('status', ''));

        $query = DB::table('peminjaman as p')
            ->join('barang as b', 'p.barang_id', '=', 'b.id')
            ->leftJoin('ruangan as r', 'p.ruangan_tujuan_id', '=', 'r.id')
            ->join('users as u', 'p.user_id', '=', 'u.id')
            ->select('p.*', 'b.nama_barang', 'b.kode_barang', 'r.nama_ruangan', 'u.nama_lengkap as pemohon');

        if (!Auth::user()->isAdmin()) {
            $query->where('p.user_id', Auth::id());
        }

        if ($statusFilter === 'dipinjam' || $statusFilter === 'disetujui') {
            $query->whereIn('p.status', ['disetujui', 'dipinjam']);
        } elseif ($statusFilter === 'dikembalikan' || $statusFilter === 'selesai') {
            $query->whereIn('p.status', ['selesai', 'dikembalikan']);
        }

        $peminjamanList = $query->orderByDesc('p.id')->get();

        return view('peminjaman.index', compact('peminjamanList', 'statusFilter'));
    }

    public function create()
    {
        $barangList = DB::select("
            SELECT id, nama_barang, kode_barang FROM barang
            UNION
            SELECT id, uraian_barang AS nama_barang, ('PERS-' || printf('%03d', nomor_urut)) AS kode_barang FROM persediaan_barang
            ORDER BY nama_barang ASC
        ");
        $ruanganList = Ruangan::orderBy('nama_ruangan')->get();

        return view('peminjaman.form', compact('barangList', 'ruanganList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1',
            'tanggal_pinjam' => 'required|date',
        ], [
            'jumlah.required' => 'Jumlah barang wajib diisi.',
            'jumlah.min' => 'Jumlah barang minimal 1.',
            'tanggal_pinjam.required' => 'Tanggal pengambilan wajib diisi.',
        ]);

        $barangId = $request->input('barang_id');
        $namaBarangInput = trim($request->input('nama_barang_input', ''));
        $jumlah = (int) $request->input('jumlah', 1);
        $tanggalPinjam = $request->input('tanggal_pinjam');
        $tanggalKembali = $request->input('tanggal_kembali') ?: null;
        $ruanganTujuanId = $request->input('ruangan_tujuan_id') ?: null;
        $peminjamNama = trim($request->input('peminjam_nama', Auth::user()->nama_lengkap ?? 'Pegawai BPS'));
        $unitKerja = trim($request->input('unit_kerja', Auth::user()->unit_kerja ?? 'BPS Kabupaten Bandung'));
        $keperluan = trim($request->input('keperluan', ''));

        $namaTarget = $namaBarangInput;
        if (!$namaTarget && $barangId) {
            $bRow = Barang::find($barangId);
            if ($bRow) {
                $namaTarget = $bRow->nama_barang;
            }
        }

        if (!$namaTarget) {
            $bDefault = Barang::first();
            if ($bDefault) {
                $barangId = $bDefault->id;
                $namaTarget = $bDefault->nama_barang;
            } else {
                $namaTarget = 'Barang Persediaan';
            }
        }

        $bMatch = Barang::whereRaw('LOWER(TRIM(nama_barang)) = LOWER(TRIM(?))', [$namaTarget])->first();
        if ($bMatch) {
            $barangId = $bMatch->id;
            $satuanBrg = $bMatch->satuan ?: 'Pcs';
        } else {
            $katRow = KategoriBarang::first();
            $katId = $katRow ? $katRow->id : 1;
            $bCount = Barang::count();
            $newKode = sprintf('1.01.03.99.%06d', $bCount + 1);
            $newBarang = Barang::create([
                'nama_barang' => $namaTarget,
                'kode_barang' => $newKode,
                'kategori_id' => $katId,
                'satuan' => 'Pcs',
            ]);
            $barangId = $newBarang->id;
            $satuanBrg = 'Pcs';
        }

        if (!$ruanganTujuanId) {
            $rDefault = Ruangan::first();
            $ruanganTujuanId = $rDefault ? $rDefault->id : 1;
        }

        $lastId = Peminjaman::max('id') ?? 0;
        $noTransaksi = sprintf('TRX-202606-%03d', $lastId + 1);
        $noBon = sprintf('%02d/PK-3204/2026', $lastId + 35);

        // Status peminjaman HANYA DIPINJAM ('disetujui' di DB) & DIKEMBALIKAN ('selesai' di DB)
        $statusAwal = 'disetujui';

        Peminjaman::create([
            'no_transaksi' => $noTransaksi,
            'no_bon' => $noBon,
            'tanggal_pinjam' => $tanggalPinjam,
            'tanggal_kembali' => $tanggalKembali,
            'user_id' => Auth::id(),
            'peminjam_nama' => $peminjamNama,
            'unit_kerja' => $unitKerja,
            'ruangan_tujuan_id' => $ruanganTujuanId,
            'barang_id' => $barangId,
            'jumlah' => $jumlah,
            'keperluan' => $keperluan,
            'status' => $statusAwal,
        ]);

        // Otomatis kurangi persediaan di PersediaanBarang
        $persediaanItem = PersediaanBarang::whereRaw('LOWER(TRIM(uraian_barang)) = LOWER(TRIM(?))', [$namaTarget])->first();
        if (!$persediaanItem) {
            $persediaanItem = PersediaanBarang::whereRaw('LOWER(uraian_barang) LIKE LOWER(?)', ["%{$namaTarget}%"])->first();
        }

        $stokBaru = 0;
        $hargaBeli = 0.0;
        $satuanFinal = $satuanBrg;

        if ($persediaanItem) {
            $stokBaru = max(0, $persediaanItem->total_persediaan_jumlah - $jumlah);
            $hargaBeli = (float) ($persediaanItem->harga_beli ?? 0);
            $hargaTotalPersediaan = (float) $stokBaru * $hargaBeli;
            $satuanFinal = $persediaanItem->satuan ?: $satuanBrg;

            $persediaanItem->update([
                'total_persediaan_jumlah' => $stokBaru,
                'harga_total_persediaan' => $hargaTotalPersediaan,
            ]);
        } else {
            $maxUrut = PersediaanBarang::max('nomor_urut') ?? 0;
            PersediaanBarang::create([
                'nomor_urut' => $maxUrut + 1,
                'uraian_barang' => $namaTarget,
                'satuan' => $satuanBrg,
                'harga_beli' => 0,
                'total_persediaan_jumlah' => 0,
                'harga_total_persediaan' => 0,
                'rusak_jumlah' => 0,
                'rusak_harga_total' => 0,
                'usang_jumlah' => 0,
                'usang_harga_total' => 0,
            ]);
        }

        // Sinkronkan ke laporan_persediaan_item
        $lapItem = LaporanPersediaanItem::whereRaw('LOWER(TRIM(uraian_barang)) = LOWER(TRIM(?))', [$namaTarget])->first();
        if (!$lapItem && $persediaanItem) {
            $lapItem = LaporanPersediaanItem::whereRaw('LOWER(TRIM(uraian_barang)) = LOWER(TRIM(?))', [$persediaanItem->uraian_barang])->first();
        }

        if ($lapItem) {
            $hb = (float) ($lapItem->harga_beli ?: $hargaBeli);
            $lapItem->update([
                'total_persediaan_jumlah' => $stokBaru,
                'harga_total_persediaan' => (float) $stokBaru * $hb,
            ]);
        } else {
            $maxNoLap = LaporanPersediaanItem::max('nomor_urut') ?? 0;
            LaporanPersediaanItem::create([
                'nomor_urut' => $maxNoLap + 1,
                'uraian_barang' => $persediaanItem ? $persediaanItem->uraian_barang : $namaTarget,
                'satuan' => $satuanFinal,
                'harga_beli' => $hargaBeli,
                'total_persediaan_jumlah' => $stokBaru,
                'harga_total_persediaan' => (float) $stokBaru * $hargaBeli,
                'rusak_harga_total' => '-',
                'rusak_jumlah' => '-',
                'usang_jumlah' => '-',
                'usang_harga_total' => '-',
                'periode_bulan' => 'Juni',
                'periode_tahun' => 2026,
                'tanggal_laporan' => date('Y-m-d'),
            ]);
        }

        return redirect()->route('peminjaman.index')
            ->with('success', "Pengajuan pengambilan ({$noTransaksi}) berhasil didaftarkan dengan status DIPINJAM.");
    }

    public function kembalikan(int $id)
    {
        $peminjaman = Peminjaman::find($id);
        if ($peminjaman) {
            $peminjaman->update([
                'status' => 'selesai',
                'tanggal_kembali' => $peminjaman->tanggal_kembali ?: date('Y-m-d'),
                'catatan_admin' => 'Barang telah dikembalikan / pengambilan selesai tercatat',
            ]);
        }

        return redirect()->route('peminjaman.index')
            ->with('success', 'Status peminjaman berhasil diubah menjadi DIKEMBALIKAN.');
    }

    public function update(Request $request, int $id)
    {
        $peminjaman = Peminjaman::find($id);
        if (!$peminjaman) {
            return redirect()->route('peminjaman.index')->with('danger', 'Data peminjaman tidak ditemukan.');
        }

        $statusInput = $request->input('status', 'disetujui');
        $dbStatus = in_array($statusInput, ['selesai', 'dikembalikan', 'DIKEMBALIKAN']) ? 'selesai' : 'disetujui';

        $peminjaman->update([
            'peminjam_nama' => trim($request->input('peminjam_nama', $peminjaman->peminjam_nama)),
            'unit_kerja' => trim($request->input('unit_kerja', $peminjaman->unit_kerja)),
            'jumlah' => (int) $request->input('jumlah', $peminjaman->jumlah),
            'keperluan' => trim($request->input('keperluan', $peminjaman->keperluan)),
            'status' => $dbStatus,
        ]);

        return redirect()->route('peminjaman.index')->with('success', 'Data peminjaman berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $row = Peminjaman::find($id);
        if ($row) {
            $no = $row->no_transaksi;
            $row->delete();
            return redirect()->route('peminjaman.index')->with('success', "Data transaksi {$no} berhasil dihapus.");
        }

        return redirect()->route('peminjaman.index');
    }

    public function destroyAll()
    {
        Peminjaman::query()->delete();
        return redirect()->route('peminjaman.index')
            ->with('success', 'Seluruh data transaksi pengambilan barang berhasil dihapus.');
    }

    public function detail(int $id)
    {
        $p = DB::table('peminjaman as p')
            ->join('barang as b', 'p.barang_id', '=', 'b.id')
            ->leftJoin('ruangan as r', 'p.ruangan_tujuan_id', '=', 'r.id')
            ->join('users as u', 'p.user_id', '=', 'u.id')
            ->select('p.*', 'b.nama_barang', 'b.kode_barang', 'b.satuan', 'r.nama_ruangan', 'u.nama_lengkap as pemohon', 'u.nip as nip_pemohon')
            ->where('p.id', $id)
            ->first();

        if (!$p) {
            return redirect()->route('peminjaman.index')->with('danger', 'Data tidak ditemukan.');
        }

        if (!Auth::user()->isAdmin() && $p->user_id != Auth::id()) {
            return redirect()->route('peminjaman.index')->with('danger', 'ANDA TIDAK MEMILIKI HAK AKSES');
        }

        return view('peminjaman.detail', compact('p'));
    }
}
