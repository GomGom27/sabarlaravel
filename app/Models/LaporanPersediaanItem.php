<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanPersediaanItem extends Model
{
    protected $table = 'laporan_persediaan_item';
    public $timestamps = false;

    protected $fillable = [
        'nomor_urut', 'uraian_barang', 'satuan', 'harga_beli',
        'total_persediaan_jumlah', 'harga_total_persediaan',
        'rusak_harga_total', 'rusak_jumlah', 'usang_jumlah', 'usang_harga_total',
        'periode_bulan', 'periode_tahun', 'tanggal_laporan',
    ];
}
