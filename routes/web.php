<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KartuKendaliController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PejabatController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\PersediaanController;
use App\Http\Controllers\RuanganController;
use Illuminate\Support\Facades\Route;

// ================= AUTHENTICATION =================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::get('/ganti-password', [AuthController::class, 'showGantiPassword'])->name('ganti_password');
Route::post('/ganti-password', [AuthController::class, 'gantiPassword'])->name('ganti_password.post');
Route::get('/lupa-password', [AuthController::class, 'showLupaPassword'])->name('lupa_password');
Route::post('/lupa-password', [AuthController::class, 'lupaPassword'])->name('lupa_password.post');
Route::get('/quick-login/{role}', [AuthController::class, 'quickLogin'])->name('quick_login');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout.post');

// ================= AUTHENTICATED ROUTES (ADMIN & PENGGUNA) =================
Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Peminjaman / Pengambilan Barang (Akses Admin & Pengguna)
    Route::get('/peminjaman', [PeminjamanController::class, 'index'])->name('peminjaman.index');
    Route::get('/peminjaman/tambah', [PeminjamanController::class, 'create'])->name('peminjaman.create');
    Route::post('/peminjaman/tambah', [PeminjamanController::class, 'store'])->name('peminjaman.store');
    Route::get('/peminjaman/detail/{id}', [PeminjamanController::class, 'detail'])->name('peminjaman.detail');
});

// ================= ADMIN ONLY ROUTES =================
Route::middleware(['auth', 'admin'])->group(function () {
    // Peminjaman Admin Actions
    Route::post('/peminjaman/kembalikan/{id}', [PeminjamanController::class, 'kembalikan'])->name('peminjaman.kembalikan');
    Route::post('/peminjaman/update/{id}', [PeminjamanController::class, 'update'])->name('peminjaman.update');
    Route::post('/peminjaman/hapus/{id}', [PeminjamanController::class, 'destroy'])->name('peminjaman.destroy');
    Route::post('/peminjaman/hapus-semua', [PeminjamanController::class, 'destroyAll'])->name('peminjaman.destroy_all');

    // Persediaan Barang
    Route::get('/persediaan', [PersediaanController::class, 'index'])->name('persediaan.index');
    Route::post('/persediaan/tambah', [PersediaanController::class, 'store'])->name('persediaan.store');
    Route::post('/persediaan/edit/{id}', [PersediaanController::class, 'update'])->name('persediaan.update');
    Route::post('/persediaan/hapus/{id}', [PersediaanController::class, 'destroy'])->name('persediaan.destroy');
    Route::post('/persediaan/hapus-semua', [PersediaanController::class, 'destroyAll'])->name('persediaan.destroy_all');
    Route::post('/persediaan/pejabat', [PersediaanController::class, 'updatePejabat'])->name('persediaan.pejabat');
    Route::get('/persediaan/cetak', [PersediaanController::class, 'cetak'])->name('persediaan.cetak');
    Route::get('/persediaan/export/excel', [PersediaanController::class, 'exportExcel'])->name('persediaan.export.excel');
    Route::get('/persediaan/export/pdf', [PersediaanController::class, 'exportPdf'])->name('persediaan.export.pdf');

    // Ruangan
    Route::get('/ruangan', [RuanganController::class, 'index'])->name('ruangan.index');
    Route::get('/ruangan/tambah', [RuanganController::class, 'create'])->name('ruangan.create');
    Route::post('/ruangan/tambah', [RuanganController::class, 'store'])->name('ruangan.store');
    Route::get('/ruangan/edit/{id}', [RuanganController::class, 'edit'])->name('ruangan.edit');
    Route::post('/ruangan/edit/{id}', [RuanganController::class, 'update'])->name('ruangan.update');
    Route::post('/ruangan/hapus/{id}', [RuanganController::class, 'destroy'])->name('ruangan.destroy');

    // Kartu Kendali Persediaan Barang
    Route::get('/kartu-kendali', [KartuKendaliController::class, 'index'])->name('kartu_kendali.index');
    Route::post('/kartu-kendali/tambah-transaksi', [KartuKendaliController::class, 'tambahTransaksi'])->name('kartu_kendali.tambah');
    Route::post('/kartu-kendali/edit-transaksi/{id}', [KartuKendaliController::class, 'editTransaksi'])->name('kartu_kendali.edit');
    Route::post('/kartu-kendali/hapus-transaksi/{id}', [KartuKendaliController::class, 'hapusTransaksi'])->name('kartu_kendali.hapus');
    Route::post('/kartu-kendali/hapus-semua', [KartuKendaliController::class, 'hapusSemua'])->name('kartu_kendali.hapus_semua');
    Route::get('/kartu-kendali/print', [KartuKendaliController::class, 'print'])->name('kartu_kendali.print');
    Route::get('/kartu-kendali/export-excel', [KartuKendaliController::class, 'exportExcel'])->name('kartu_kendali.export.excel');
    Route::get('/kartu-kendali/export-pdf', [KartuKendaliController::class, 'exportPdf'])->name('kartu_kendali.export.pdf');

    // Laporan Bulanan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::post('/laporan/tambah', [LaporanController::class, 'store'])->name('laporan.store');
    Route::post('/laporan/edit/{id}', [LaporanController::class, 'update'])->name('laporan.update');
    Route::post('/laporan/hapus/{id}', [LaporanController::class, 'destroy'])->name('laporan.destroy');
    Route::post('/laporan/hapus-semua', [LaporanController::class, 'destroyAll'])->name('laporan.destroy_all');
    Route::get('/laporan/print', [LaporanController::class, 'print'])->name('laporan.print');
    Route::get('/laporan/export-excel', [LaporanController::class, 'exportExcel'])->name('laporan.export.excel');
    Route::get('/laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('laporan.export.pdf');

    // Pengguna
    Route::get('/pengguna', [PenggunaController::class, 'index'])->name('pengguna.index');
    Route::post('/pengguna/tambah', [PenggunaController::class, 'store'])->name('pengguna.store');
    Route::post('/pengguna/edit/{id}', [PenggunaController::class, 'update'])->name('pengguna.update');
    Route::post('/pengguna/hapus/{id}', [PenggunaController::class, 'destroy'])->name('pengguna.destroy');

    // Pengaturan Pejabat
    Route::get('/pengaturan/pejabat', [PejabatController::class, 'index'])->name('pengaturan.pejabat');
    Route::post('/pengaturan/pejabat', [PejabatController::class, 'update'])->name('pengaturan.pejabat.update');
});
