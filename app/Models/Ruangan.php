<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ruangan extends Model
{
    protected $table = 'ruangan';
    public $timestamps = false;

    protected $fillable = [
        'kode_ruangan',
        'nama_ruangan',
        'lokasi_lantai',
        'penanggung_jawab',
        'keterangan',
        'created_at',
    ];

    public function peminjaman()
    {
        return $this->hasMany(Peminjaman::class, 'ruangan_tujuan_id');
    }
}
