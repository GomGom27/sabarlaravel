@extends('layouts.app')

@section('title', 'Dashboard Utama')

@section('content')
<div class="space-y-6">

    <!-- WELCOME BANNER (SUPER ADMIN) -->
    <div class="no-print bg-gradient-to-r from-orange-600 via-orange-500 to-amber-600 rounded-2xl p-6 text-white shadow-md flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold uppercase tracking-wider bg-white/20 text-white backdrop-blur-sm">
                    <i class="fa-solid fa-building-columns mr-1"></i>
                    BPS KABUPATEN BANDUNG
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-extrabold uppercase tracking-wider bg-green-600 text-white">
                    <i class="fa-solid fa-shield-halved mr-1"></i> SUPER ADMIN
                </span>
            </div>
            <h1 class="text-2xl font-black tracking-tight">
                Selamat Datang, {{ Auth::user()->nama_lengkap }}
            </h1>
            <p class="text-orange-100 text-sm mt-1">
                SABAR (Sistem Ambil Barang) — Pengelolaan Inventori &amp; Persediaan Barang BPS Kabupaten Bandung.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('peminjaman.create') }}"
               class="bg-white text-orange-700 hover:bg-orange-50 font-bold px-4 py-2 rounded-xl text-xs sm:text-sm shadow transition flex items-center gap-2">
                <i class="fa-solid fa-plus-circle text-orange-600"></i>
                Ajukan Pengambilan
            </a>
            <a href="{{ route('kartu_kendali.index') }}"
               class="bg-orange-800/40 hover:bg-orange-800/60 text-white border border-white/20 font-bold px-4 py-2 rounded-xl text-xs sm:text-sm shadow transition flex items-center gap-2">
                <i class="fa-solid fa-table-list"></i>
                Kartu Kendali Persediaan
            </a>
        </div>
    </div>

    <!-- GRAFIK LINGKARAN PENGAMBILAN BARANG & RINGKASAN METRIK -->
    <div class="no-print grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- KARTU GRAFIK LINGKARAN -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-gray-200 shadow-sm flex flex-col justify-between">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-gray-100 gap-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center text-xl shadow-inner">
                        <i class="fa-solid fa-chart-pie"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-bold text-gray-900 flex items-center gap-2">
                            <span>Grafik Lingkaran Pengambilan Barang</span>
                            <span class="text-[10px] bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-bold">Aktif</span>
                        </h2>
                        <p class="text-xs text-gray-500">Visualisasi lingkaran proporsi transaksi &amp; komoditas pengambilan barang</p>
                    </div>
                </div>
                <div class="flex items-center gap-1.5 bg-gray-100 p-1 rounded-xl self-start sm:self-auto">
                    <button type="button" id="btnTabStatus" onclick="switchChartMode('status')"
                        class="px-3 py-1 text-xs font-bold rounded-lg bg-orange-600 text-white shadow-sm transition cursor-pointer">
                        <i class="fa-solid fa-signal mr-1"></i> Status
                    </button>
                    <button type="button" id="btnTabBarang" onclick="switchChartMode('barang')"
                        class="px-3 py-1 text-xs font-bold rounded-lg text-gray-600 hover:text-orange-600 hover:bg-white/80 transition cursor-pointer">
                        <i class="fa-solid fa-box mr-1"></i> Komoditas
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 items-center my-4">
                <div class="relative flex items-center justify-center p-2" style="height: 240px; width: 100%;">
                    <canvas id="peminjamanPieChart"></canvas>
                    <div id="chartCenterBadge" class="absolute pointer-events-none text-center flex flex-col items-center justify-center">
                        <span id="chartCenterNumber" class="text-3xl font-black text-gray-800 leading-none">{{ $totalPeminjaman }}</span>
                        <span id="chartCenterLabel" class="text-[10px] uppercase font-bold text-gray-400 tracking-wider mt-1">Transaksi</span>
                    </div>
                </div>

                <div class="space-y-2.5 text-xs">
                    <div id="legendStatusContainer" class="space-y-2.5">
                        <div class="flex items-center justify-between p-3 rounded-xl bg-green-50 border border-green-200">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-green-600 shadow-sm"></span>
                                <span class="font-bold text-green-900">DIPINJAM (Aktif)</span>
                            </div>
                            <span class="font-black text-green-700 text-sm">{{ $totalPinjamAktif }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 rounded-xl bg-blue-50 border border-blue-200">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-blue-600 shadow-sm"></span>
                                <span class="font-bold text-blue-900">DIKEMBALIKAN</span>
                            </div>
                            <span class="font-black text-blue-700 text-sm">{{ $totalPinjamSelesai }}</span>
                        </div>

                        <div class="flex items-center justify-between p-3 rounded-xl bg-orange-50 border border-orange-200">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-orange-600 shadow-sm"></span>
                                <span class="font-bold text-orange-900">Total Transaksi</span>
                            </div>
                            <span class="font-black text-orange-700 text-sm">{{ $totalPeminjaman }}</span>
                        </div>
                    </div>

                    <div id="legendBarangContainer" class="space-y-2 hidden">
                        @php $barColors = ['#ea580c', '#0284c7', '#16a34a', '#8b5cf6', '#f59e0b']; @endphp
                        @forelse($topBarangPinjam as $idx => $b)
                            <div class="flex items-center justify-between p-2 rounded-xl bg-gray-50 border border-gray-200">
                                <div class="flex items-center gap-2 truncate pr-2">
                                    <span class="w-3 h-3 rounded-full flex-shrink-0" style="background-color: {{ $barColors[$idx % 5] }};"></span>
                                    <span class="font-bold text-gray-800 truncate" title="{{ $b->nama_barang }}">{{ $b->nama_barang }}</span>
                                </div>
                                <span class="font-black text-orange-700 text-xs flex-shrink-0">{{ $b->total_qty }} Unit</span>
                            </div>
                        @empty
                            <p class="text-gray-400 italic text-center py-4">Belum ada komoditas pengambilan</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-between text-xs text-gray-500">
                <span><i class="fa-solid fa-circle-check text-green-600 mr-1"></i>Data grafik terhubung dinamis &amp; otomatis dengan SQLite</span>
                <a href="{{ route('peminjaman.index') }}" class="text-orange-600 hover:text-orange-800 font-bold flex items-center gap-1">
                    Kelola Peminjaman <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- STAT CARDS -->
        <div class="space-y-4 flex flex-col justify-between">
            <div class="bg-white rounded-2xl p-5 border border-orange-100 shadow-sm flex items-center justify-between hover:shadow-md transition">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Total Barang &amp; Persediaan</p>
                    <h3 class="text-3xl font-black text-orange-600 mt-1">
                        {{ $totalBarang }}
                        <span class="text-xs font-normal text-gray-500">Item ({{ number_format($totalPersediaan, 0, ',', '.') }} Unit)</span>
                    </h3>
                    <p class="text-[11px] text-gray-400 mt-0.5">Barang Persediaan Resmi BPS</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center text-2xl shadow-inner">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-green-100 shadow-sm flex items-center justify-between hover:shadow-md transition">
                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Data Ruangan</p>
                    <h3 class="text-3xl font-black text-green-600 mt-1">
                        {{ $totalRuangan }}
                        <span class="text-xs font-normal text-gray-500">Ruangan</span>
                    </h3>
                    <p class="text-[11px] text-gray-500 mt-0.5 font-semibold">Ruang Penyimpanan Utama BPS</p>
                </div>
                <div class="w-14 h-14 rounded-2xl bg-green-50 text-green-600 flex items-center justify-center text-2xl shadow-inner">
                    <i class="fa-solid fa-door-open"></i>
                </div>
            </div>

            <div class="bg-gradient-to-br from-orange-50 to-amber-50 rounded-2xl p-5 border border-orange-200 shadow-sm">
                <h4 class="text-xs font-bold uppercase tracking-wider text-orange-800 mb-1 flex items-center gap-1.5">
                    <i class="fa-solid fa-hand-holding-hand text-orange-600"></i>
                    Akses Cepat Pengeluaran
                </h4>
                <p class="text-xs text-orange-700/80 mb-3">
                    Pengajuan pengambilan barang persediaan dapat diketik langsung tanpa wajib memilih dari dropdown.
                </p>
                <a href="{{ route('peminjaman.create') }}" class="w-full py-2 px-3 bg-orange-600 hover:bg-orange-700 text-white font-bold text-xs rounded-xl shadow transition inline-flex items-center justify-center gap-2">
                    <i class="fa-solid fa-plus-circle"></i> Input Pengambilan Baru
                </a>
            </div>
        </div>

    </div>

    <!-- RIWAYAT TRANSAKSI PENGAMBILAN TERKINI -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-orange-600"></i>
                Riwayat Transaksi Peminjaman &amp; Pengambilan Terkini
            </h3>
            <a href="{{ route('peminjaman.index') }}" class="text-xs font-bold text-orange-600 hover:text-orange-700">
                Lihat Semua <i class="fa-solid fa-arrow-right ml-1"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 border-b uppercase tracking-wider font-semibold">
                        <th class="py-3 px-4">No. Bon / Transaksi</th>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Peminjam / Pemohon</th>
                        <th class="py-3 px-4">Barang</th>
                        <th class="py-3 px-4 text-center">Jumlah</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($recentPeminjaman as $p)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="py-3 px-4 font-mono font-semibold text-gray-900">{{ $p->no_bon ?: $p->no_transaksi }}</td>
                        <td class="py-3 px-4 text-gray-600">{{ $p->tanggal_pinjam }}</td>
                        <td class="py-3 px-4 font-semibold text-gray-900">{{ $p->peminjam_nama }}</td>
                        <td class="py-3 px-4 text-gray-800 font-medium">{{ $p->barang->nama_barang ?? '-' }}</td>
                        <td class="py-3 px-4 text-center font-bold text-orange-600">{{ $p->jumlah }}</td>
                        <td class="py-3 px-4 text-center">
                            @if(in_array($p->status, ['selesai', 'dikembalikan']))
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-800">
                                <i class="fa-solid fa-check-double mr-1"></i> DIKEMBALIKAN
                            </span>
                            @else
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-green-100 text-green-800">
                                <i class="fa-solid fa-circle-check mr-1"></i> DIPINJAM
                            </span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            <a href="{{ route('peminjaman.detail', $p->id) }}"
                               class="inline-flex items-center gap-1 text-orange-600 hover:text-orange-800 font-bold text-xs bg-orange-50 hover:bg-orange-100 px-2.5 py-1 rounded-lg transition">
                                <i class="fa-solid fa-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-6 text-gray-400">Belum ada riwayat transaksi pengambilan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
let peminjamanChartInstance = null;
let currentChartMode = 'status';

const statusData = {
    labels: ['DIPINJAM', 'DIKEMBALIKAN'],
    data: [{{ $totalPinjamAktif }}, {{ $totalPinjamSelesai }}],
    colors: ['#16a34a', '#2563eb'],
    total: {{ $totalPinjamAktif + $totalPinjamSelesai }},
    unit: 'Transaksi'
};

@php
    $barangNames = $topBarangPinjam->pluck('nama_barang')->toArray();
    $barangQtys = $topBarangPinjam->pluck('total_qty')->map(fn($v) => (int)$v)->toArray();
@endphp

const barangData = {
    labels: @json($barangNames),
    data: @json($barangQtys),
    colors: ['#ea580c', '#0284c7', '#16a34a', '#8b5cf6', '#f59e0b'],
    total: {{ array_sum($barangQtys) }},
    unit: 'Unit'
};

function switchChartMode(mode) {
    currentChartMode = mode;
    const btnStatus = document.getElementById('btnTabStatus');
    const btnBarang = document.getElementById('btnTabBarang');
    const legStatus = document.getElementById('legendStatusContainer');
    const legBarang = document.getElementById('legendBarangContainer');

    if (mode === 'status') {
        btnStatus.className = 'px-3 py-1 text-xs font-bold rounded-lg bg-orange-600 text-white shadow-sm transition cursor-pointer';
        btnBarang.className = 'px-3 py-1 text-xs font-bold rounded-lg text-gray-600 hover:text-orange-600 hover:bg-white/80 transition cursor-pointer';
        legStatus.classList.remove('hidden');
        legBarang.classList.add('hidden');
    } else {
        btnBarang.className = 'px-3 py-1 text-xs font-bold rounded-lg bg-orange-600 text-white shadow-sm transition cursor-pointer';
        btnStatus.className = 'px-3 py-1 text-xs font-bold rounded-lg text-gray-600 hover:text-orange-600 hover:bg-white/80 transition cursor-pointer';
        legBarang.classList.remove('hidden');
        legStatus.classList.add('hidden');
    }
    renderChart();
}

function renderChart() {
    const canvas = document.getElementById('peminjamanPieChart');
    if (!canvas) return;
    if (typeof Chart === 'undefined') { setTimeout(renderChart, 100); return; }

    const cfg = currentChartMode === 'status' ? statusData : barangData;
    const totalVal = cfg.total;

    const badgeNum = document.getElementById('chartCenterNumber');
    const badgeLbl = document.getElementById('chartCenterLabel');
    if (badgeNum) badgeNum.textContent = totalVal;
    if (badgeLbl) badgeLbl.textContent = cfg.unit;

    const chartValues = totalVal === 0 ? [1] : cfg.data;
    const chartColors = totalVal === 0 ? ['#e2e8f0'] : cfg.colors.slice(0, chartValues.length);
    const chartLabels = totalVal === 0 ? ['Belum ada data'] : cfg.labels;

    if (peminjamanChartInstance) peminjamanChartInstance.destroy();

    peminjamanChartInstance = new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: chartLabels,
            datasets: [{
                data: chartValues,
                backgroundColor: chartColors,
                borderWidth: totalVal === 0 ? 0 : 3,
                borderColor: '#ffffff',
                hoverOffset: totalVal === 0 ? 0 : 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    enabled: totalVal > 0,
                    callbacks: {
                        label: function(context) {
                            if (totalVal === 0) return ' Belum ada data';
                            const val = context.parsed;
                            const pct = totalVal > 0 ? ((val / totalVal) * 100).toFixed(1) : 0;
                            return ` ${context.label}: ${val} ${cfg.unit} (${pct}%)`;
                        }
                    }
                }
            },
            cutout: '70%'
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', renderChart);
} else {
    renderChart();
}
</script>
@endsection
