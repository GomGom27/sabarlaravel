<?php

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\LaporanPersediaanItem;
use App\Models\Peminjaman;
use App\Models\PersediaanBarang;
use App\Models\Ruangan;
use App\Models\User;
use Tests\TestCase;

class SabarMigrationTest extends TestCase
{
    public function test_login_page_accessible(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('SABAR');
        $response->assertSee('BADAN PUSAT STATISTIK');
    }

    public function test_login_gagal(): void
    {
        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'salah123',
        ]);
        $response->assertRedirect();
        $this->assertGuest();
    }

    public function test_login_admin_dan_akses_seluruh_halaman_admin(): void
    {
        $response = $this->post('/login', [
            'username' => 'admin',
            'password' => 'admin123',
        ]);
        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();

        // Dashboard
        $this->get('/dashboard')->assertStatus(200)->assertSee('Grafik Lingkaran Pengambilan Barang');

        // Persediaan Barang & Export
        $this->get('/persediaan')->assertStatus(200)->assertSee('TABEL PERSEDIAAN BARANG');
        $this->get('/persediaan/cetak')->assertStatus(200)->assertSee('BADAN PUSAT STATISTIK KABUPATEN BANDUNG');
        $this->get('/persediaan/export/excel')->assertStatus(200);
        $this->get('/persediaan/export/pdf')->assertStatus(200);

        // Ruangan
        $this->get('/ruangan')->assertStatus(200)->assertSee('Data Ruangan');

        // Peminjaman Barang (HANYA DIPINJAM & DIKEMBALIKAN, tanpa MENUNGGU)
        $pinjamResp = $this->get('/peminjaman');
        $pinjamResp->assertStatus(200);
        $pinjamResp->assertSee('DIPINJAM');
        $pinjamResp->assertSee('DIKEMBALIKAN');
        $pinjamResp->assertDontSee('Menunggu Persetujuan');

        // Kartu Kendali & Export
        $this->get('/kartu-kendali')->assertStatus(200)->assertSee('KARTU KENDALI PERSEDIAAN BARANG');
        $this->get('/kartu-kendali/print')->assertStatus(200);
        $this->get('/kartu-kendali/export-excel')->assertStatus(200);
        $this->get('/kartu-kendali/export-pdf')->assertStatus(200);

        // Laporan Bulanan + Filter + Export
        $this->get('/laporan')->assertStatus(200)->assertSee('Laporan Bulanan');
        $this->get('/laporan?bulan=Juni&tahun=2026')->assertStatus(200);
        $this->get('/laporan/print?bulan=Juni&tahun=2026')->assertStatus(200);
        $this->get('/laporan/export-excel?bulan=Juni&tahun=2026')->assertStatus(200);
        $this->get('/laporan/export-pdf?bulan=Juni&tahun=2026')->assertStatus(200);

        // Pengguna & Pejabat
        $this->get('/pengguna')->assertStatus(200)->assertSee('Manajemen Pengguna');
        $this->get('/pengaturan/pejabat')->assertStatus(200)->assertSee('Pengaturan Pejabat Penandatangan');

        // Logout
        $this->post('/logout')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_login_pengguna_dan_pembatasan_hak_akses(): void
    {
        $response = $this->post('/login', [
            'username' => 'pengguna',
            'password' => 'staf123',
        ]);
        $response->assertRedirect('/peminjaman');
        $this->assertAuthenticated();

        // Pengguna bisa mengakses Peminjaman Barang
        $this->get('/peminjaman')->assertStatus(200);
        $this->get('/peminjaman/tambah')->assertStatus(200);

        // Pengguna TIDAK boleh mengakses halaman Admin -> redirect + "ANDA TIDAK MEMILIKI HAK AKSES"
        foreach (['/persediaan', '/ruangan', '/kartu-kendali', '/laporan', '/pengguna', '/pengaturan/pejabat'] as $adminUrl) {
            $res = $this->get($adminUrl);
            $res->assertRedirect('/peminjaman');
            $res->assertSessionHas('danger', 'ANDA TIDAK MEMILIKI HAK AKSES');
        }
    }

    public function test_crud_persediaan_dan_persisted_sqlite(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $nextNo = (PersediaanBarang::max('nomor_urut') ?? 0) + 1;
        $resp = $this->post('/persediaan/tambah', [
            'nomor_urut' => $nextNo,
            'uraian_barang' => 'Barang Uji Migrasi Laravel',
            'satuan' => 'Rim',
            'harga_beli' => 50000,
            'total_persediaan_jumlah' => 10,
            'rusak_jumlah' => 0,
            'usang_jumlah' => 0,
        ]);
        $resp->assertRedirect('/persediaan');
        $resp->assertSessionHas('success');

        $created = PersediaanBarang::where('nomor_urut', $nextNo)->first();
        $this->assertNotNull($created);
        $this->assertEquals(500000, $created->harga_total_persediaan);

        // Cleanup data uji agar DB tetap bersih
        $this->post("/persediaan/hapus/{$created->id}")->assertRedirect('/persediaan');
        $this->assertNull(PersediaanBarang::find($created->id));
        Barang::where('nama_barang', 'Barang Uji Migrasi Laravel')->delete();
        LaporanPersediaanItem::where('uraian_barang', 'Barang Uji Migrasi Laravel')->delete();
    }
}
