@extends('layouts.app')

@section('title', 'Ganti Kata Sandi')

@section('extra_head')
<link rel="stylesheet" href="{{ asset('css/ganti_password.css') }}">
@endsection

@section('content')
<div class="login-bg-overlay"></div>

<div class="relative z-10 max-w-md mx-auto my-6 sm:my-10">
    <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-2xl overflow-hidden border border-white/60">
        <div class="bg-gradient-to-r from-orange-600 via-orange-500 to-orange-700 p-6 text-white text-center">
            <div class="w-20 h-20 bg-transparent mx-auto mb-2 flex items-center justify-center">
                <img src="{{ asset('img/bps_logo.svg') }}" alt="Logo BPS" class="h-16 w-auto object-contain drop-shadow-md">
            </div>
            <h2 class="text-xl font-extrabold tracking-tight">BADAN PUSAT STATISTIK</h2>
            <p class="text-xs uppercase tracking-widest text-orange-200 font-semibold mt-0.5">Kabupaten Bandung</p>
            <p class="text-xs text-orange-100 mt-1">Pengaturan Keamanan: Ganti Kata Sandi</p>
        </div>

        <div class="p-6 sm:p-8">
            <h3 class="text-lg font-bold text-gray-800 mb-1 flex items-center gap-2">
                <i class="fa-solid fa-key text-orange-600"></i> Ganti Kata Sandi Akun
            </h3>
            <p class="text-xs text-gray-500 mb-6">Ubah kata sandi akun Admin atau Pengguna Anda untuk menjaga keamanan akses sistem inventori.</p>

            <form method="POST" action="{{ route('ganti_password.post') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" for="username">
                        <i class="fa-solid fa-user text-gray-400 mr-1"></i> Username Akun <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="username" name="username" required placeholder="Masukkan username Anda..."
                        value="{{ old('username') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm transition">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" for="old_password">
                        <i class="fa-solid fa-lock text-gray-400 mr-1"></i> Kata Sandi Saat Ini (Lama) <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="old_password" name="old_password" required placeholder="••••••••"
                            class="w-full pl-4 pr-11 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm transition">
                        <button type="button" onclick="togglePasswordVisibility('old_password', 'toggleOldIcon')"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-orange-600 focus:outline-none transition cursor-pointer">
                            <i id="toggleOldIcon" class="fa-solid fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" for="new_password">
                        <i class="fa-solid fa-shield-halved text-gray-400 mr-1"></i> Kata Sandi Baru <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="new_password" name="new_password" required minlength="4" placeholder="Minimal 4 karakter..."
                            class="w-full pl-4 pr-11 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm transition">
                        <button type="button" onclick="togglePasswordVisibility('new_password', 'toggleNewIcon')"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-orange-600 focus:outline-none transition cursor-pointer">
                            <i id="toggleNewIcon" class="fa-solid fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" for="confirm_password">
                        <i class="fa-solid fa-check-double text-gray-400 mr-1"></i> Konfirmasi Kata Sandi Baru <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" id="confirm_password" name="confirm_password" required minlength="4" placeholder="Ulangi kata sandi baru..."
                            class="w-full pl-4 pr-11 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm transition">
                        <button type="button" onclick="togglePasswordVisibility('confirm_password', 'toggleConfirmIcon')"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-orange-600 focus:outline-none transition cursor-pointer">
                            <i id="toggleConfirmIcon" class="fa-solid fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 px-4 rounded-xl shadow-md hover:shadow-lg transition duration-200 flex items-center justify-center gap-2 text-sm">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Kata Sandi Baru
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-5 border-t border-gray-100 text-center space-y-2">
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 bg-gray-50 hover:bg-gray-100 text-gray-700 border border-gray-200 rounded-xl text-xs font-bold transition">
                    <i class="fa-solid fa-arrow-left"></i> Batal / Kembali ke Halaman Masuk
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
