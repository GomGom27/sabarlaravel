<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KartuKendaliTransaksi extends Model
{
    protected $table = 'kartu_kendali_transaksi';
    public $timestamps = false;

    protected $fillable = [
        'barang_id', 'baris_no', 'bagian', 'no_bon_faktur', 'tgl_mk',
        'uraian_penerima', 'masuk_pembelian', 'masuk_transfer', 'masuk_lainnya',
        'keluar_pembelian', 'keluar_transfer', 'keluar_lainnya',
        'sisa_saldo', 'bulan', 'tahun',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
