<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KartuKendaliBulanan extends Model
{
    protected $table = 'kartu_kendali_bulanan';
    public $timestamps = false;

    protected $fillable = [
        'barang_id', 'tahun',
        'jan', 'feb', 'mar', 'apr', 'mei', 'jun',
        'jul', 'agt', 'sept', 'okt', 'nov', 'des',
        'jumlah_pengeluaran', 'stock_awal', 'stock_akhir',
    ];

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }
}
