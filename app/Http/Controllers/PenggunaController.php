<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id')->get();
        return view('pengguna.index', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:150',
            'username' => 'required|string|max:80',
            'password' => 'required|string|min:4',
            'role' => 'required|in:admin,pengguna',
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 4 karakter.',
            'role.required' => 'Role wajib dipilih.',
        ]);

        $username = trim($request->username);
        if (User::whereRaw('LOWER(TRIM(username)) = LOWER(?)', [$username])->exists()) {
            return back()->withInput()->with('danger', "Username \"{$username}\" sudah terdaftar.");
        }

        User::create([
            'nama_lengkap' => trim($request->nama_lengkap),
            'username' => $username,
            'password_hash' => Hash::make(trim($request->password)),
            'role' => strtolower($request->role),
            'nip' => trim($request->input('nip', '')) ?: null,
            'unit_kerja' => trim($request->input('unit_kerja', '')) ?: 'BPS Kabupaten Bandung',
        ]);

        return redirect()->route('pengguna.index')->with('success', "Pengguna \"{$username}\" berhasil ditambahkan.");
    }

    public function update(Request $request, int $id)
    {
        $user = User::find($id);
        if (!$user) {
            return redirect()->route('pengguna.index')->with('danger', 'Pengguna tidak ditemukan.');
        }

        $request->validate([
            'nama_lengkap' => 'required|string|max:150',
            'username' => 'required|string|max:80',
            'role' => 'required|in:admin,pengguna',
        ]);

        $username = trim($request->username);
        if (User::whereRaw('LOWER(TRIM(username)) = LOWER(?)', [$username])->where('id', '!=', $id)->exists()) {
            return back()->with('danger', "Username \"{$username}\" sudah digunakan akun lain.");
        }

        $data = [
            'nama_lengkap' => trim($request->nama_lengkap),
            'username' => $username,
            'role' => strtolower($request->role),
            'nip' => trim($request->input('nip', '')) ?: null,
            'unit_kerja' => trim($request->input('unit_kerja', '')) ?: 'BPS Kabupaten Bandung',
        ];

        if ($request->filled('password')) {
            $data['password_hash'] = Hash::make(trim($request->password));
        }

        $user->update($data);

        return redirect()->route('pengguna.index')->with('success', "Data pengguna \"{$username}\" berhasil diperbarui.");
    }

    public function destroy(int $id)
    {
        if (Auth::id() === $id) {
            return redirect()->route('pengguna.index')->with('danger', 'Anda tidak dapat menghapus akun Anda sendiri yang sedang aktif.');
        }

        $user = User::find($id);
        if ($user) {
            $username = $user->username;
            $user->delete();
            return redirect()->route('pengguna.index')->with('success', "Pengguna \"{$username}\" berhasil dihapus.");
        }

        return redirect()->route('pengguna.index');
    }
}
