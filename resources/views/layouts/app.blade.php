<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SABAR - Sistem Ambil Barang') - BPS Kabupaten Bandung</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        bps: {
                            orange: '#ea580c',
                            'orange-dark': '#c2410c',
                            'orange-light': '#fff7ed',
                            blue: '#1d4ed8',
                            'blue-dark': '#1e3a8a',
                            'blue-light': '#eff6ff',
                            green: '#15803d',
                            'green-dark': '#166534',
                            'green-light': '#f0fdf4'
                        }
                    }
                }
            }
        }
    </script>
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Chart.js Library (Local + CDN Fallback) -->
    <script src="{{ asset('js/chart.min.js') }}"></script>
    <script>
        if (typeof Chart === 'undefined') {
            document.write('<script src="https://cdn.jsdelivr.net/npm/chart.js"><\/script>');
        }
    </script>
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}">
    @yield('extra_head')
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">

    <!-- TOP BAR BRANDING (BPS KABUPATEN BANDUNG) -->
    <header class="no-print bg-gradient-to-r from-orange-600 via-orange-500 to-orange-700 text-white shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-3">
                    <div class="h-11 w-auto flex items-center justify-center bg-transparent">
                        <img src="{{ asset('img/bps_logo.svg') }}" alt="Logo BPS" class="h-10 w-auto object-contain drop-shadow-sm">
                    </div>
                    <div>
                        <div class="font-bold text-lg tracking-tight flex items-center gap-2">
                            <span>BADAN PUSAT STATISTIK</span>
                            <span class="text-xs bg-white text-orange-700 font-semibold px-2 py-0.5 rounded-full uppercase tracking-wider">KAB. BANDUNG</span>
                        </div>
                        <p class="text-xs text-orange-100 hidden sm:block">SABAR — Sistem Ambil Barang &amp; Inventori Persediaan</p>
                    </div>
                </div>

                <!-- User Profile -->
                <div class="flex items-center space-x-3">
                    @auth
                        <div class="flex items-center space-x-2 text-sm bg-orange-800/40 px-3 py-1.5 rounded-lg border border-orange-400/30">
                            <div class="text-right hidden sm:block">
                                <p class="font-semibold leading-none">{{ Auth::user()->nama_lengkap }}</p>
                                <span class="text-[10px] font-bold px-1.5 py-0.5 rounded uppercase tracking-wide mt-1 inline-block {{ Auth::user()->isAdmin() ? 'bg-green-600 text-white' : 'bg-blue-600 text-white' }}">
                                    <i class="fa-solid {{ Auth::user()->isAdmin() ? 'fa-shield-halved' : 'fa-user-check' }} mr-1"></i>{{ Auth::user()->isAdmin() ? 'Super Admin' : 'Pengguna' }}
                                </span>
                            </div>
                        </div>

                        <button type="button" onclick="bukaModalLogout()" title="Keluar Sistem" class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-red-600 hover:bg-red-700 text-white font-bold text-xs rounded-xl shadow transition cursor-pointer">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            <span>Keluar Sistem</span>
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="bg-white text-orange-600 hover:bg-orange-50 font-semibold px-4 py-2 rounded-lg text-sm shadow transition flex items-center gap-2">
                            <i class="fa-solid fa-arrow-right-to-bracket"></i> BerAkhlak
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- NAVIGATION MENU BAR -->
    @auth
    <nav class="no-print bg-white border-b border-gray-200 shadow-sm sticky top-16 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex space-x-1 sm:space-x-3 overflow-x-auto py-2 scrollbar-none text-sm font-medium items-center justify-between">
                <div class="flex space-x-1 sm:space-x-3 overflow-x-auto scrollbar-none">
                    @if(Auth::user()->isAdmin())
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg transition whitespace-nowrap {{ request()->routeIs('dashboard', 'home') ? 'bg-orange-50 text-orange-700 font-bold border-b-2 border-orange-600' : 'text-gray-600 hover:text-orange-600 hover:bg-gray-50' }}">
                        <i class="fa-solid fa-gauge-high text-orange-600"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('persediaan.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg transition whitespace-nowrap {{ request()->routeIs('persediaan.*') ? 'bg-orange-50 text-orange-700 font-bold border-b-2 border-orange-600' : 'text-gray-600 hover:text-orange-600 hover:bg-gray-50' }}">
                        <i class="fa-solid fa-boxes-stacked text-orange-600"></i>
                        <span>Persediaan Barang</span>
                    </a>

                    <a href="{{ route('ruangan.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg transition whitespace-nowrap {{ request()->routeIs('ruangan.*') ? 'bg-orange-50 text-orange-700 font-bold border-b-2 border-orange-600' : 'text-gray-600 hover:text-orange-600 hover:bg-gray-50' }}">
                        <i class="fa-solid fa-door-open text-green-600"></i>
                        <span>Ruangan</span>
                    </a>
                    @endif

                    <a href="{{ route('peminjaman.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg transition whitespace-nowrap {{ request()->routeIs('peminjaman.*') ? 'bg-orange-50 text-orange-700 font-bold border-b-2 border-orange-600' : 'text-gray-600 hover:text-orange-600 hover:bg-gray-50' }}">
                        <i class="fa-solid fa-hand-holding-hand text-orange-600"></i>
                        <span>Peminjaman Barang</span>
                    </a>

                    @if(Auth::user()->isAdmin())
                    <a href="{{ route('kartu_kendali.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg transition whitespace-nowrap {{ request()->routeIs('kartu_kendali.*') ? 'bg-orange-50 text-orange-700 font-bold border-b-2 border-orange-600' : 'text-gray-600 hover:text-orange-600 hover:bg-gray-50' }}">
                        <i class="fa-solid fa-table-list text-blue-700"></i>
                        <span>Kartu Kendali Persediaan Barang</span>
                    </a>

                    <a href="{{ route('laporan.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg transition whitespace-nowrap {{ request()->routeIs('laporan.*') ? 'bg-orange-50 text-orange-700 font-bold border-b-2 border-orange-600' : 'text-gray-600 hover:text-orange-600 hover:bg-gray-50' }}">
                        <i class="fa-solid fa-file-contract text-green-700"></i>
                        <span>Laporan Bulanan</span>
                    </a>

                    <a href="{{ route('pengguna.index') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg transition whitespace-nowrap {{ request()->routeIs('pengguna.*') ? 'bg-orange-50 text-orange-700 font-bold border-b-2 border-orange-600' : 'text-gray-600 hover:text-orange-600 hover:bg-gray-50' }}">
                        <i class="fa-solid fa-users text-blue-600"></i>
                        <span>Pengguna</span>
                    </a>

                    <a href="{{ route('pengaturan.pejabat') }}" class="flex items-center gap-2 px-3 py-2 rounded-lg transition whitespace-nowrap {{ request()->routeIs('pengaturan.*') ? 'bg-orange-50 text-orange-700 font-bold border-b-2 border-orange-600' : 'text-gray-600 hover:text-orange-600 hover:bg-gray-50' }}">
                        <i class="fa-solid fa-signature text-orange-600"></i>
                        <span>Pejabat BPS</span>
                    </a>
                    @endif
                </div>

                @if(!Auth::user()->isAdmin())
                <div class="hidden md:flex items-center gap-2 text-xs font-semibold text-blue-700 bg-blue-50 px-3 py-1.5 rounded-lg border border-blue-200">
                    <i class="fa-solid fa-circle-info"></i>
                    <span>Mode Pengguna: Layanan Peminjaman / Ambil Barang BPS</span>
                </div>
                @endif
            </div>
        </div>
    </nav>
    @endauth

    <!-- FLASH & TOAST NOTIFICATIONS -->
    <div id="toastNotificationContainer" class="no-print fixed bottom-5 right-5 z-[9999] max-w-md w-full px-4 sm:px-0 space-y-3 pointer-events-none">
        @foreach(['success', 'danger', 'warning', 'info'] as $category)
            @if(session($category))
                @php
                    $message = session($category);
                    $isLogin = str_contains($message, 'LOGIN BERHASIL') || str_contains($message, 'Selamat datang');
                    $isLogout = str_contains($message, 'LOGOUT BERHASIL') || str_contains($message, 'keluar dari sistem');
                    $isAdmin = str_contains($message, 'Super Admin');
                @endphp
                <div class="toast-item pointer-events-auto transform transition-all duration-500 ease-out translate-y-0 opacity-100 scale-100 rounded-2xl shadow-2xl border backdrop-blur-md overflow-hidden relative
                    @if($isLogin && $isAdmin)
                        bg-gradient-to-r from-orange-950/95 via-orange-900/95 to-amber-900/95 text-white border-amber-500/40 shadow-orange-500/25 ring-1 ring-amber-400/30
                    @elseif($isLogin)
                        bg-gradient-to-r from-emerald-950/95 via-teal-900/95 to-cyan-900/95 text-white border-emerald-500/40 shadow-emerald-500/25 ring-1 ring-emerald-400/30
                    @elseif($isLogout)
                        bg-gradient-to-r from-slate-900/95 via-blue-950/95 to-indigo-950/95 text-white border-blue-500/40 shadow-blue-500/25 ring-1 ring-blue-400/30
                    @elseif($category === 'success')
                        bg-white text-gray-800 border-green-300 shadow-green-500/10
                    @elseif($category === 'danger')
                        bg-white text-gray-800 border-red-300 shadow-red-500/10
                    @else
                        bg-white text-gray-800 border-orange-300 shadow-orange-500/10
                    @endif">

                    <div class="p-4 flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center text-lg
                            @if($isLogin && $isAdmin) bg-gradient-to-br from-amber-400 to-orange-500 text-white shadow-lg animate-pulse
                            @elseif($isLogin) bg-gradient-to-br from-emerald-400 to-teal-500 text-white shadow-lg animate-pulse
                            @elseif($isLogout) bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg
                            @elseif($category === 'success') bg-green-100 text-green-600
                            @elseif($category === 'danger') bg-red-100 text-red-600
                            @else bg-orange-100 text-orange-600 @endif">
                            @if($isLogin && $isAdmin) <i class="fa-solid fa-shield-halved"></i>
                            @elseif($isLogin) <i class="fa-solid fa-circle-check"></i>
                            @elseif($isLogout) <i class="fa-solid fa-right-from-bracket"></i>
                            @elseif($category === 'success') <i class="fa-solid fa-check"></i>
                            @elseif($category === 'danger') <i class="fa-solid fa-triangle-exclamation"></i>
                            @else <i class="fa-solid fa-bell"></i> @endif
                        </div>

                        <div class="flex-1 min-w-0 pt-0.5">
                            @if($isLogin)
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[10px] font-black uppercase tracking-widest px-2 py-0.5 rounded-full {{ $isAdmin ? 'bg-amber-400 text-gray-950' : 'bg-emerald-400 text-gray-950' }}">
                                        {{ $isAdmin ? '👑 SUPER ADMIN' : '👤 PENGGUNA BPS' }}
                                    </span>
                                    <span class="text-[11px] opacity-75 font-semibold">Autentikasi Berhasil</span>
                                </div>
                                <p class="text-xs font-medium leading-relaxed text-gray-100">{{ $message }}</p>
                            @elseif($isLogout)
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-[10px] font-black uppercase tracking-widest px-2 py-0.5 rounded-full bg-blue-400 text-gray-950">👋 LOGOUT BERHASIL</span>
                                    <span class="text-[11px] opacity-75 font-semibold">Sesi Berakhir</span>
                                </div>
                                <p class="text-xs font-medium leading-relaxed text-gray-100">{{ $message }}</p>
                            @else
                                <div class="text-xs font-semibold mb-0.5 {{ $category === 'success' ? 'text-green-700' : ($category === 'danger' ? 'text-red-700' : 'text-gray-700') }}">
                                    Pemberitahuan Sistem
                                </div>
                                <p class="text-xs text-gray-600 leading-relaxed">{{ $message }}</p>
                            @endif
                        </div>

                        <button onclick="dismissToast(this.closest('.toast-item'))" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg transition-colors flex-shrink-0">
                            <i class="fa-solid fa-xmark text-sm"></i>
                        </button>
                    </div>

                    <div class="h-1 w-full bg-black/30 overflow-hidden">
                        <div class="h-full toast-progress {{ ($isLogin && $isAdmin) ? 'bg-amber-400' : ($isLogin ? 'bg-emerald-400' : ($isLogout ? 'bg-blue-400' : 'bg-orange-500')) }}" style="width: 100%; transition: width 5s linear;"></div>
                    </div>
                </div>
            @endif
        @endforeach

        @if($errors->any())
            <div class="toast-item pointer-events-auto rounded-2xl shadow-2xl border bg-white text-gray-800 border-red-300 overflow-hidden relative">
                <div class="p-4 flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded-xl flex-shrink-0 flex items-center justify-center text-lg bg-red-100 text-red-600">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div class="flex-1 min-w-0 pt-0.5">
                        <div class="text-xs font-semibold mb-0.5 text-red-700">Validasi Formulir</div>
                        <ul class="text-xs text-gray-600 list-disc pl-4 space-y-0.5">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button onclick="dismissToast(this.closest('.toast-item'))" class="text-gray-400 hover:text-gray-600 p-1 rounded-lg">
                        <i class="fa-solid fa-xmark text-sm"></i>
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- MAIN BODY CONTENT -->
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6">
        @yield('content')
    </main>

    <!-- FOOTER -->
    <footer class="no-print bg-white border-t border-gray-200 py-4 mt-auto">
        <div class="max-w-7xl mx-auto px-4 text-center text-xs text-gray-500 flex flex-col sm:flex-row justify-between items-center gap-2">
            <p>© 2026 Badan Pusat Statistik (BPS) Kabupaten Bandung - SABAR (Sistem Ambil Barang)</p>
            <div class="flex items-center gap-4 text-gray-400">
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-orange-500"></span> Orange Dominan</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-blue-600"></span> Biru Aksen</span>
                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-green-600"></span> Hijau Aksen</span>
            </div>
        </div>
    </footer>

    <!-- MAIN JAVASCRIPT -->
    <script src="{{ asset('js/main.js') }}"></script>
    <script>
    function togglePasswordVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (!input || !icon) return;
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    function dismissToast(el) {
        if (!el) return;
        el.style.transform = 'translateX(120%)';
        el.style.opacity = '0';
        setTimeout(() => el.remove(), 400);
    }

    document.addEventListener('DOMContentLoaded', () => {
        const toasts = document.querySelectorAll('.toast-item');
        toasts.forEach((toast) => {
            const progress = toast.querySelector('.toast-progress');
            if (progress) {
                requestAnimationFrame(() => {
                    progress.style.width = '0%';
                });
            }
            setTimeout(() => {
                dismissToast(toast);
            }, 5000);
        });
    });

    function bukaModalLogout() {
        const modal = document.getElementById('modalKonfirmasiLogout');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.classList.add('overflow-hidden');
        }
    }

    function tutupModalLogout() {
        const modal = document.getElementById('modalKonfirmasiLogout');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            tutupModalLogout();
        }
    });
    </script>

    <!-- MODAL KONFIRMASI LOGOUT (BATAL / IYA) -->
    @auth
    <div id="modalKonfirmasiLogout" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[99999] flex items-center justify-center hidden p-4 transition-all" onclick="if(event.target===this) tutupModalLogout()">
        <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl space-y-5 text-center transform transition-all border border-gray-100">
            <div class="w-16 h-16 bg-red-50 text-red-600 rounded-2xl mx-auto flex items-center justify-center text-2xl shadow-inner border border-red-100">
                <i class="fa-solid fa-arrow-right-from-bracket"></i>
            </div>

            <div class="space-y-1.5">
                <h3 class="text-lg font-bold text-gray-900">Konfirmasi Keluar</h3>
                <p class="text-xs text-gray-600 leading-relaxed">
                    Apakah Anda yakin ingin keluar dari sistem?
                </p>
                <div class="pt-2">
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold text-gray-600 bg-gray-100 px-3 py-1 rounded-full border border-gray-200">
                        <i class="fa-solid {{ Auth::user()->isAdmin() ? 'fa-shield-halved text-amber-600' : 'fa-user-check text-blue-600' }}"></i>
                        {{ Auth::user()->nama_lengkap }} ({{ Auth::user()->isAdmin() ? 'Super Admin' : 'Pengguna' }})
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 pt-2">
                <button type="button" onclick="tutupModalLogout()"
                    class="w-full py-2.5 px-4 rounded-xl border-2 border-gray-300 hover:bg-gray-100 text-gray-700 font-bold text-xs uppercase tracking-wider transition cursor-pointer flex items-center justify-center gap-1.5">
                    <i class="fa-solid fa-xmark"></i>
                    <span>BATAL</span>
                </button>
                <form method="POST" action="{{ route('logout.post') }}" class="w-full">
                    @csrf
                    <button type="submit"
                        class="w-full py-2.5 px-4 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs uppercase tracking-wider shadow-md hover:shadow-lg transition flex items-center justify-center gap-1.5 cursor-pointer">
                        <i class="fa-solid fa-check"></i>
                        <span>IYA</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endauth

    @yield('scripts')
</body>
</html>
