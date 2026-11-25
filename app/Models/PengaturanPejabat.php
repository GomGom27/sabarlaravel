<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaturanPejabat extends Model
{
    protected $table = 'pengaturan_pejabat';
    public $timestamps = false;

    protected $fillable = [
        'jabatan_kiri_1', 'jabatan_kiri_2', 'jabatan_kiri_3',
        'nama_pejabat_kiri', 'nip_pejabat_kiri',
        'tempat_tanggal_kanan', 'jabatan_kanan',
        'nama_pejabat_kanan', 'nip_pejabat_kanan',
    ];
}
