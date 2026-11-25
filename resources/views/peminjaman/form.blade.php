@extends('layouts.app')

@section('title', 'Formulir Pengajuan Peminjaman Barang')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('peminjaman.index') }}" class="text-sm font-semibold text-gray-600 hover:text-orange-600 flex items-center gap-1.5 transition">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Peminjaman
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-orange-600 via-orange-500 to-amber-600 p-6 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-xl">
                    <i class="fa-solid fa-hand-holding-hand"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold">Formulir Peminjaman &amp; Pengambilan Barang</h2>
                    <p class="text-xs text-orange-100">Badan Pusat Statistik Kabupaten Bandung</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('peminjaman.store') }}" class="p-6 sm:p-8 space-y-5">
            @csrf
            <div>
                <label for="nama_barang_input" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                    Nama Barang Persediaan <span class="text-red-500">*</span>
                    <span class="text-[10px] text-orange-600 font-normal lowercase">(bisa diketik langsung atau pilih dari daftar)</span>
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-gray-400">
                        <i class="fa-solid fa-keyboard"></i>
                    </span>
                    <input type="text" id="nama_barang_input" name="nama_barang_input" list="barang_list_options" required
                        value="{{ old('nama_barang_input') }}"
                        placeholder="Ketik langsung nama barang di sini (contoh: HVS F4 75 Gram, Spidol Snowman, dll)..."
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm">
                    <datalist id="barang_list_options">
                        @foreach($barangList as $b)
                            <option value="{{ $b->nama_barang }}">{{ $b->kode_barang }}</option>
                        @endforeach
                    </datalist>
                </div>
                <p class="text-[11px] text-gray-500 mt-1 flex items-center gap-1">
                    <i class="fa-solid fa-circle-check text-green-600"></i>
                    <span>Stok persediaan akan otomatis terpotong dan tersinkron ke Laporan Bulanan.</span>
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="jumlah" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Jumlah Barang <span class="text-red-500">*</span>
                    </label>
                    <input type="number" id="jumlah" name="jumlah" min="1" value="{{ old('jumlah', 1) }}" required
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm font-semibold">
                </div>

                <div>
                    <label for="tanggal_pinjam" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Tanggal Peminjaman / Pengambilan <span class="text-red-500">*</span>
                    </label>
                    <input type="date" id="tanggal_pinjam" name="tanggal_pinjam" required
                        value="{{ old('tanggal_pinjam', date('Y-m-d')) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="peminjam_nama" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Nama Pengambil / Pegawai <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="peminjam_nama" name="peminjam_nama" required
                        value="{{ old('peminjam_nama', Auth::user()->nama_lengkap) }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm">
                </div>

                <div>
                    <label for="unit_kerja" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Seksi / Fungsi Kerja
                    </label>
                    <input type="text" id="unit_kerja" name="unit_kerja"
                        value="{{ old('unit_kerja', Auth::user()->unit_kerja ?: 'BPS Kabupaten Bandung') }}"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm">
                </div>
            </div>

            <div>
                <label for="keperluan" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                    Keperluan / Aktivitas Kegiatan Dinas
                </label>
                <textarea id="keperluan" name="keperluan" rows="3"
                    placeholder="Contoh: Kebutuhan cetak kuesioner sensus / persiapan rapat koordinasi teknis..."
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm">{{ old('keperluan') }}</textarea>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                <a href="{{ route('peminjaman.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-semibold text-sm hover:bg-gray-50 transition">Batal</a>
                <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white font-bold px-6 py-2.5 rounded-xl shadow-md transition flex items-center gap-2 text-sm cursor-pointer">
                    <i class="fa-solid fa-paper-plane"></i> Kirim Formulir Peminjaman
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
