@extends('layouts.app')

@section('title', 'Daftar Akun Pengguna Baru')

@section('extra_head')
<link rel="stylesheet" href="{{ asset('css/register.css') }}">
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
            <p class="text-xs text-orange-100 mt-1">Registrasi Akun Pengguna / Staf Pegawai</p>
        </div>

        <div class="p-6 sm:p-8">
            <h3 class="text-lg font-bold text-gray-800 mb-1 flex items-center gap-2">
                <i class="fa-solid fa-user-plus text-orange-600"></i> Buat Akun Pengguna
            </h3>
            <p class="text-xs text-gray-500 mb-6">Daftarkan akun staf Anda untuk mengajukan pengambilan &amp; pengeluaran barang persediaan.</p>

            <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" for="nama_lengkap">
                        <i class="fa-solid fa-id-card text-gray-400 mr-1"></i> Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="nama_lengkap" name="nama_lengkap" required value="{{ old('nama_lengkap') }}" placeholder="Contoh: Arya Putra Dewa, S. Kom."
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm transition">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" for="nip">
                            <i class="fa-solid fa-address-card text-gray-400 mr-1"></i> NIP
                        </label>
                        <input type="text" id="nip" name="nip" value="{{ old('nip') }}" placeholder="Contoh: 19950314..."
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm transition">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" for="unit_kerja">
                            <i class="fa-solid fa-building text-gray-400 mr-1"></i> Unit Kerja / Fungsi
                        </label>
                        <input type="text" id="unit_kerja" name="unit_kerja" value="{{ old('unit_kerja') }}" list="unit_kerja_list" placeholder="Contoh: Fungsi IPDS..."
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm transition">
                        <datalist id="unit_kerja_list">
                            <option value="Subbagian Umum / Tata Usaha">
                            <option value="Fungsi IPDS">
                            <option value="Fungsi Statistik Sosial">
                            <option value="Fungsi Statistik Produksi">
                            <option value="Fungsi Statistik Distribusi">
                            <option value="Fungsi Nerwilis">
                        </datalist>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" for="username">
                        <i class="fa-solid fa-user text-gray-400 mr-1"></i> Username <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="username" name="username" required value="{{ old('username') }}" placeholder="Contoh: arya"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm transition">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" for="password">
                            <i class="fa-solid fa-key text-gray-400 mr-1"></i> Kata Sandi <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" id="password" name="password" required placeholder="••••••••"
                                class="w-full pl-3.5 pr-10 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm transition">
                            <button type="button" onclick="togglePasswordVisibility('password', 'toggleRegPassIcon')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-orange-600 focus:outline-none transition cursor-pointer">
                                <i id="toggleRegPassIcon" class="fa-solid fa-eye text-xs"></i>
                            </button>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5" for="confirm_password">
                            <i class="fa-solid fa-check-double text-gray-400 mr-1"></i> Konfirmasi <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" id="confirm_password" name="confirm_password" required placeholder="••••••••"
                                class="w-full pl-3.5 pr-10 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm transition">
                            <button type="button" onclick="togglePasswordVisibility('confirm_password', 'toggleRegConfIcon')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-orange-600 focus:outline-none transition cursor-pointer">
                                <i id="toggleRegConfIcon" class="fa-solid fa-eye text-xs"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-3 px-4 rounded-xl shadow-md hover:shadow-lg transition duration-200 flex items-center justify-center gap-2 text-sm">
                        <i class="fa-solid fa-user-plus"></i> Daftarkan Akun Pengguna
                    </button>
                </div>
            </form>

            <div class="mt-6 pt-4 border-t border-gray-100 text-center">
                <p class="text-xs text-gray-500">
                    Sudah memiliki akun?
                    <a href="{{ route('login') }}" class="font-bold text-orange-600 hover:text-orange-800 ml-1">Masuk di Sini</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
