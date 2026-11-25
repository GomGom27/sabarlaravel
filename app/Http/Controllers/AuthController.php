<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('dashboard')
                : redirect()->route('peminjaman.index');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ], [
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ]);

        $username = trim($request->input('username'));
        $password = trim($request->input('password'));

        $user = User::whereRaw('LOWER(TRIM(username)) = LOWER(?)', [$username])->first();

        if ($user && Hash::check($password, $user->password_hash)) {
            Auth::login($user);
            $request->session()->regenerate();

            $roleLabel = $user->isAdmin() ? 'Super Admin' : 'Pengguna';
            session()->flash('success', "LOGIN BERHASIL! Selamat datang kembali, {$user->nama_lengkap} ({$roleLabel}).");

            return $user->isAdmin()
                ? redirect()->route('dashboard')
                : redirect()->route('peminjaman.index');
        }

        return back()
            ->withInput($request->only('username'))
            ->with('danger', 'Username atau kata sandi tidak valid. Silakan coba lagi.');
    }

    public function showRegister()
    {
        if (Auth::check()) {
            return Auth::user()->isAdmin()
                ? redirect()->route('dashboard')
                : redirect()->route('peminjaman.index');
        }

        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nama_lengkap' => 'required|string|max:150',
            'username' => 'required|string|max:80',
            'password' => 'required|string|min:4',
            'confirm_password' => 'required|same:password',
        ], [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
            'password.min' => 'Kata sandi minimal 4 karakter.',
            'confirm_password.same' => 'Konfirmasi kata sandi tidak cocok. Silakan coba lagi.',
        ]);

        $username = trim($request->input('username'));
        $exists = User::whereRaw('LOWER(TRIM(username)) = LOWER(?)', [$username])->exists();

        if ($exists) {
            return back()
                ->withInput()
                ->with('danger', 'Username sudah digunakan oleh pegawai lain. Silakan pilih username lain.');
        }

        User::create([
            'username' => $username,
            'password_hash' => Hash::make(trim($request->input('password'))),
            'nama_lengkap' => trim($request->input('nama_lengkap')),
            'nip' => trim($request->input('nip', '')) ?: null,
            'role' => 'pengguna',
            'unit_kerja' => trim($request->input('unit_kerja', '')) ?: 'Staf BPS Kabupaten Bandung',
        ]);

        return redirect()->route('login')
            ->with('success', 'Pendaftaran akun Pengguna berhasil! Silakan masuk dengan kredensial baru Anda.');
    }

    public function showGantiPassword()
    {
        return view('auth.ganti_password');
    }

    public function gantiPassword(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:4',
            'confirm_password' => 'required|same:new_password',
        ], [
            'username.required' => 'Username wajib diisi.',
            'old_password.required' => 'Kata sandi saat ini wajib diisi.',
            'new_password.required' => 'Kata sandi baru wajib diisi.',
            'new_password.min' => 'Kata sandi baru minimal 4 karakter.',
            'confirm_password.same' => 'Konfirmasi kata sandi baru tidak cocok. Silakan ulangi.',
        ]);

        $username = trim($request->input('username'));
        $user = User::whereRaw('LOWER(TRIM(username)) = LOWER(?)', [$username])->first();

        if (!$user) {
            return back()->with('danger', 'Username akun tidak ditemukan dalam sistem.');
        }

        if (!Hash::check(trim($request->input('old_password')), $user->password_hash)) {
            return back()->with('danger', 'Kata sandi saat ini (lama) tidak sesuai.');
        }

        $user->password_hash = Hash::make(trim($request->input('new_password')));
        $user->save();

        return redirect()->route('login')
            ->with('success', "Kata sandi untuk pengguna \"{$user->username}\" berhasil diperbarui! Silakan masuk dengan kata sandi baru.");
    }

    public function showLupaPassword()
    {
        return view('auth.lupa_password');
    }

    public function lupaPassword(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'nip_atau_nama' => 'required|string',
            'new_password' => 'required|string|min:4',
            'confirm_password' => 'required|same:new_password',
        ], [
            'username.required' => 'Username wajib diisi.',
            'nip_atau_nama.required' => 'NIP atau Nama Lengkap wajib diisi untuk verifikasi.',
            'new_password.required' => 'Kata sandi baru wajib diisi.',
            'new_password.min' => 'Kata sandi baru minimal 4 karakter.',
            'confirm_password.same' => 'Konfirmasi kata sandi baru tidak cocok. Silakan ulangi.',
        ]);

        $username = trim($request->input('username'));
        $nipAtauNama = trim($request->input('nip_atau_nama'));
        $user = User::whereRaw('LOWER(TRIM(username)) = LOWER(?)', [$username])->first();

        if (!$user) {
            return back()->with('danger', 'Username akun tidak ditemukan dalam sistem BPS.');
        }

        $userNip = str_replace([' ', '.', '-'], '', trim($user->nip ?? ''));
        $inputClean = str_replace([' ', '.', '-'], '', $nipAtauNama);
        $userNama = strtolower(trim($user->nama_lengkap ?? ''));
        $inputNama = strtolower($nipAtauNama);

        $matchNip = !empty($userNip) && $inputClean === $userNip;
        $matchNama = !empty($userNama) && ($inputNama === $userNama || str_contains($userNama, $inputNama) || str_contains($inputNama, $userNama));

        if (!($matchNip || $matchNama)) {
            return back()->with('danger', 'Verifikasi keamanan gagal! NIP atau Nama Lengkap tidak cocok dengan data akun terdaftar.');
        }

        $user->password_hash = Hash::make(trim($request->input('new_password')));
        $user->save();

        return redirect()->route('login')
            ->with('success', "Pemulihan kata sandi berhasil! Akun \"{$user->username}\" telah diperbarui. Silakan masuk dengan kata sandi baru Anda.");
    }

    public function quickLogin(string $role)
    {
        $user = $role === 'admin'
            ? User::where('role', 'admin')->first()
            : User::where('role', 'pengguna')->first();

        if ($user) {
            Auth::login($user);
            request()->session()->regenerate();
            $roleLabel = $user->isAdmin() ? 'Super Admin' : 'Pengguna';
            session()->flash('success', "LOGIN BERHASIL! Selamat datang kembali, {$user->nama_lengkap} ({$roleLabel}).");

            return $user->isAdmin()
                ? redirect()->route('dashboard')
                : redirect()->route('peminjaman.index');
        }

        return redirect()->route('login');
    }

    public function logout(Request $request)
    {
        $nama = Auth::user()->nama_lengkap ?? 'Pengguna';
        $roleLabel = (Auth::user() && Auth::user()->isAdmin()) ? 'Super Admin' : 'Pengguna';

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('info', "LOGOUT BERHASIL! Anda telah keluar dari sistem ({$roleLabel} - {$nama}).");
    }
}
