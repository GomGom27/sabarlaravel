<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersediaanBarang extends Model
{
    protected $table = 'persediaan_barang';

    protected $fillable = [
        'nomor_urut', 'uraian_barang', 'satuan', 'harga_beli',
        'total_persediaan_jumlah', 'harga_total_persediaan',
        'rusak_jumlah', 'rusak_harga_total',
        'usang_jumlah', 'usang_harga_total', 'keterangan',
    ];
}
