<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\KartuKendaliBulanan;
use App\Models\KartuKendaliTransaksi;
use App\Models\Peminjaman;
use App\Models\PersediaanBarang;
use App\Models\Ruangan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->route('peminjaman.index');
        }

        $totalBarang = Barang::count();
        $totalPersediaan = PersediaanBarang::sum('total_persediaan_jumlah');
        $totalRuangan = Ruangan::count();
        $totalPinjamAktif = Peminjaman::whereIn('status', ['disetujui', 'dipinjam'])->count();
        $totalPinjamSelesai = Peminjaman::whereIn('status', ['selesai', 'dikembalikan'])->count();
        $totalPeminjaman = Peminjaman::count();

        $barangHvs = Barang::where('nama_barang', 'HVS F4 75 Gram')->first() ?? Barang::first();

        $bulanan = null;
        $transaksiKiri = collect();
        $transaksiKanan = collect();

        if ($barangHvs) {
            $bulanan = KartuKendaliBulanan::where('barang_id', $barangHvs->id)->first();
            $transaksiKiri = KartuKendaliTransaksi::where('barang_id', $barangHvs->id)
                ->where('bagian', 'kiri')->orderBy('baris_no')->get();
            $transaksiKanan = KartuKendaliTransaksi::where('barang_id', $barangHvs->id)
                ->where('bagian', 'kanan')->orderBy('baris_no')->get();
        }

        $recentPeminjaman = Peminjaman::with(['barang', 'ruangan'])
            ->orderByDesc('id')->limit(5)->get();

        $topBarangPinjam = DB::table('peminjaman as p')
            ->join('barang as b', 'p.barang_id', '=', 'b.id')
            ->select('b.nama_barang', DB::raw('SUM(p.jumlah) as total_qty'))
            ->groupBy('b.id', 'b.nama_barang')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        return view('dashboard.index', compact(
            'totalBarang',
            'totalPersediaan',
            'totalRuangan',
            'totalPinjamAktif',
            'totalPinjamSelesai',
            'totalPeminjaman',
            'barangHvs',
            'bulanan',
            'transaksiKiri',
            'transaksiKanan',
            'recentPeminjaman',
            'topBarangPinjam'
        ));
    }
}
