<?php

namespace App\Http\Controllers;

use App\Models\PengaturanPejabat;
use Illuminate\Http\Request;

class PejabatController extends Controller
{
    public function index()
    {
        $pejabat = PengaturanPejabat::orderByDesc('id')->first();
        return view('pengaturan.pejabat', compact('pejabat'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_pejabat_kiri' => 'required|string|max:150',
            'nama_pejabat_kanan' => 'required|string|max:150',
        ], [
            'nama_pejabat_kiri.required' => 'Nama Kepala BPS wajib diisi.',
            'nama_pejabat_kanan.required' => 'Nama Petugas Pengelola Persediaan wajib diisi.',
        ]);

        $pejabat = PengaturanPejabat::orderByDesc('id')->first();
        $data = [
            'jabatan_kiri_1' => trim($request->input('jabatan_kiri_1', 'Mengetahui')),
            'jabatan_kiri_2' => trim($request->input('jabatan_kiri_2', 'Penanggung Jawab KPB')),
            'jabatan_kiri_3' => trim($request->input('jabatan_kiri_3', 'Kepala BPS Kabupaten Bandung')),
            'nama_pejabat_kiri' => trim($request->input('nama_pejabat_kiri')),
            'nip_pejabat_kiri' => trim($request->input('nip_pejabat_kiri', '')),
            'tempat_tanggal_kanan' => trim($request->input('tempat_tanggal_kanan', '')),
            'jabatan_kanan' => trim($request->input('jabatan_kanan', 'Petugas Pengelola Persediaan')),
            'nama_pejabat_kanan' => trim($request->input('nama_pejabat_kanan')),
            'nip_pejabat_kanan' => trim($request->input('nip_pejabat_kanan', '')),
        ];

        if ($pejabat) {
            $pejabat->update($data);
        } else {
            PengaturanPejabat::create($data);
        }

        $redirectTo = $request->input('redirect_to');
        if ($redirectTo) {
            return redirect($redirectTo)->with('success', 'Data Pejabat Penandatangan Dokumen berhasil diperbarui!');
        }

        return redirect()->route('pengaturan.pejabat')->with('success', 'Data Pejabat Penandatangan Dokumen berhasil diperbarui!');
    }
}
