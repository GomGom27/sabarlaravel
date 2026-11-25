<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    protected $table = 'peminjaman';
    public $timestamps = false;

    protected $fillable = [
        'no_transaksi',
        'no_bon',
        'tanggal_pinjam',
        'tanggal_kembali',
        'user_id',
        'peminjam_nama',
        'unit_kerja',
        'ruangan_tujuan_id',
        'barang_id',
        'jumlah',
        'keperluan',
        'status',
        'catatan_admin',
        'created_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'barang_id');
    }

    public function ruangan()
    {
        return $this->belongsTo(Ruangan::class, 'ruangan_tujuan_id');
    }
}
