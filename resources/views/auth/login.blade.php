@extends('layouts.app')

@section('title', 'Masuk ke Sistem')

@section('extra_head')
<link rel="stylesheet" href="{{ asset('css/login.css') }}">
@endsection

@section('content')
<div class="login-bg-overlay"></div>

<div class="relative z-10 max-w-md mx-auto my-6 sm:my-10">
    <div class="bg-white/95 backdrop-blur-sm rounded-2xl shadow-2xl overflow-hidden border border-white/60">
        <div class="bg-gradient-to-r from-orange-600 via-orange-500 to-orange-700 p-6 text-white text-center">
            <div class="w-20 h-20 bg-transparent mx-auto mb-2 flex items-center justify-center">
                <img src="{{ asset('img/bps_logo.svg') }}" alt="Logo BPS" class="h-16 w-auto object-contain drop-shadow-md">
            </div>
            <div class="flex items-center justify-center gap-2 sm:gap-2.5 my-2.5">
                @foreach(['S','A','B','A','R'] as $ch)
                <span class="w-9 h-9 sm:w-10 sm:h-10 bg-white/20 backdrop-blur-md border border-white/50 shadow-md rounded-lg flex items-center justify-center text-lg sm:text-xl font-black text-white transform hover:scale-110 hover:bg-white/30 transition-all duration-200 select-none">{{ $ch }}</span>
                @endforeach
            </div>
            <p class="text-xs uppercase tracking-widest text-orange-200 font-semibold mt-0.5">Sistem Ambil Barang</p>
            <p class="text-xs text-orange-100 mt-1">BADAN PUSAT STATISTIK KAB. BANDUNG</p>
        </div>

        <div class="p-6 sm:p-8">
            <h3 class="text-lg font-bold text-gray-800 mb-1 flex items-center gap-2">
                <i class="fa-solid fa-lock text-orange-600"></i> Autentikasi Pengguna
            </h3>
            <p class="text-xs text-gray-500 mb-6">Masukkan username dan kata sandi Anda untuk mengakses sistem.</p>

            <form method="POST" action="{{ route('login.post') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" for="username">
                        <i class="fa-solid fa-user text-gray-400 mr-1"></i> Username
                    </label>
                    <input type="text" id="username" name="username" required value="{{ old('username') }}" placeholder="Masukkan username Anda..."
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm transition">
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider" for="password">
                            <i class="fa-solid fa-key text-gray-400 mr-1"></i> Kata Sandi
                        </label>
                    </div>
                    <div class="relative">
                        <input type="password" id="password" name="password" required placeholder="••••••••"
                            class="w-full pl-4 pr-11 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-sm transition">
                        <button type="button" onclick="togglePasswordVisibility('password', 'togglePasswordIcon')"
                            class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-orange-600 focus:outline-none transition cursor-pointer"
                            title="Tampilkan / Sembunyikan Kata Sandi">
                            <i id="togglePasswordIcon" class="fa-solid fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 px-4 rounded-xl shadow-md hover:shadow-lg transition duration-200 flex items-center justify-center gap-2 text-sm">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i> Masuk Sekarang
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-5 border-t border-gray-100">
                <div class="grid grid-cols-2 gap-2.5">
                    <a href="{{ route('ganti_password') }}" class="py-2.5 px-3 text-center rounded-xl border border-orange-200 hover:border-orange-400 bg-orange-50/70 hover:bg-orange-100 text-orange-800 text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-sm">
                        <i class="fa-solid fa-key text-orange-600"></i> Ganti Password
                    </a>
                    <a href="{{ route('lupa_password') }}" class="py-2.5 px-3 text-center rounded-xl border border-blue-200 hover:border-blue-400 bg-blue-50/70 hover:bg-blue-100 text-blue-800 text-xs font-bold transition flex items-center justify-center gap-1.5 shadow-sm">
                        <i class="fa-solid fa-shield-halved text-blue-600"></i> Lupa Password?
                    </a>
                </div>
                <p class="text-[11px] text-gray-400 text-center mt-2 flex items-center justify-center gap-1">
                    <i class="fa-solid fa-lock text-[10px]"></i> Pengamanan Akun Admin &amp; Pengguna BPS
                </p>
            </div>

            <div class="mt-5 pt-5 border-t border-gray-100 text-center space-y-3">
                <p class="text-xs text-gray-500">Belum memiliki akun pengguna?</p>
                <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 w-full py-2.5 px-4 bg-orange-50 hover:bg-orange-100 text-orange-700 border border-orange-200 rounded-xl text-xs font-bold transition">
                    <i class="fa-solid fa-user-plus text-orange-600"></i> Buat Akun Pengguna Baru (Registrasi)
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
