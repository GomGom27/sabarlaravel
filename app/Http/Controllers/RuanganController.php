<?php

namespace App\Http\Controllers;

use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class RuanganController extends Controller
{
    public function index()
    {
        $ruanganList = Ruangan::orderBy('kode_ruangan')->get();
        return view('ruangan.index', compact('ruanganList'));
    }

    public function create()
    {
        return view('ruangan.form', ['action' => 'tambah', 'ruangan' => null]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_ruangan' => 'required|string|max:50',
            'nama_ruangan' => 'required|string|max:150',
        ], [
            'kode_ruangan.required' => 'Kode Ruangan wajib diisi.',
            'nama_ruangan.required' => 'Nama Ruangan wajib diisi.',
        ]);

        try {
            Ruangan::create([
                'kode_ruangan' => trim($request->kode_ruangan),
                'nama_ruangan' => trim($request->nama_ruangan),
                'lokasi_lantai' => trim($request->input('lokasi_lantai', '')),
                'penanggung_jawab' => trim($request->input('penanggung_jawab', '')),
                'keterangan' => trim($request->input('keterangan', '')),
            ]);

            return redirect()->route('ruangan.index')
                ->with('success', 'Ruangan "' . trim($request->nama_ruangan) . '" berhasil didaftarkan.');
        } catch (QueryException $e) {
            return back()->withInput()->with('danger', 'Kode Ruangan sudah terdaftar. Gunakan kode lain.');
        }
    }

    public function edit(int $id)
    {
        $ruangan = Ruangan::find($id);
        if (!$ruangan) {
            return redirect()->route('ruangan.index')->with('danger', 'Ruangan tidak ditemukan.');
        }

        return view('ruangan.form', ['action' => 'edit', 'ruangan' => $ruangan]);
    }

    public function update(Request $request, int $id)
    {
        $ruangan = Ruangan::find($id);
        if (!$ruangan) {
            return redirect()->route('ruangan.index')->with('danger', 'Ruangan tidak ditemukan.');
        }

        $request->validate([
            'kode_ruangan' => 'required|string|max:50',
            'nama_ruangan' => 'required|string|max:150',
        ], [
            'kode_ruangan.required' => 'Kode Ruangan wajib diisi.',
            'nama_ruangan.required' => 'Nama Ruangan wajib diisi.',
        ]);

        try {
            $ruangan->update([
                'kode_ruangan' => trim($request->kode_ruangan),
                'nama_ruangan' => trim($request->nama_ruangan),
                'lokasi_lantai' => trim($request->input('lokasi_lantai', '')),
                'penanggung_jawab' => trim($request->input('penanggung_jawab', '')),
                'keterangan' => trim($request->input('keterangan', '')),
            ]);

            return redirect()->route('ruangan.index')
                ->with('success', 'Data ruangan "' . $ruangan->nama_ruangan . '" berhasil diperbarui.');
        } catch (QueryException $e) {
            return back()->withInput()->with('danger', 'Kode Ruangan sudah terdaftar pada ruangan lain.');
        }
    }

    public function destroy(int $id)
    {
        $ruangan = Ruangan::find($id);
        if ($ruangan) {
            $nama = $ruangan->nama_ruangan;
            $ruangan->delete();
            return redirect()->route('ruangan.index')->with('success', "Ruangan \"{$nama}\" telah dihapus.");
        }

        return redirect()->route('ruangan.index');
    }
}
