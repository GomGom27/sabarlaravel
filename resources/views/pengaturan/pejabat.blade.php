@extends('layouts.app')

@section('title', 'Pengaturan Pejabat Penandatangan Dokumen')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <div class="flex items-center justify-between bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-signature"></i>
            </div>
            <div>
                <h1 class="text-xl font-black text-gray-900">Pengaturan Pejabat Penandatangan</h1>
                <p class="text-xs text-gray-500">Ubah Nama dan NIP Kepala BPS serta Petugas Persediaan secara dinamis</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-orange-600 to-orange-500 p-6 text-white">
            <h2 class="text-base font-bold flex items-center gap-2">
                <i class="fa-solid fa-user-pen"></i> Form Data Penandatangan Laporan &amp; Kartu Kendali
            </h2>
            <p class="text-xs text-orange-100 mt-1">Data ini akan otomatis tercetak di bagian bawah Laporan Bulanan, Bukti Peminjaman, dan Dokumen Persediaan BPS.</p>
        </div>

        <form method="POST" action="{{ route('pengaturan.pejabat.update') }}" class="p-6 sm:p-8 space-y-6">
            @csrf
            <div class="p-4 bg-orange-50 border border-orange-200 rounded-xl text-xs text-orange-800 flex items-start gap-3">
                <i class="fa-solid fa-circle-info text-orange-600 text-base mt-0.5"></i>
                <div>
                    <p class="font-bold">Kemudahan Administrasi Dinas:</p>
                    <p class="mt-0.5">Jika terjadi pergantian Kepala BPS Kabupaten Bandung atau rotasi Petugas Pengelola Persediaan, Anda cukup memperbarui form di bawah ini.</p>
                </div>
            </div>

            <div class="border border-gray-200 rounded-xl p-5 bg-gray-50/50 space-y-4">
                <div class="flex items-center gap-2 pb-2 border-b border-gray-200">
                    <span class="w-6 h-6 rounded-full bg-blue-600 text-white flex items-center justify-center text-xs font-bold">1</span>
                    <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide">Pihak Kiri: Kepala BPS Kabupaten Bandung</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Kepala BPS <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_pejabat_kiri" required
                            value="{{ $pejabat->nama_pejabat_kiri ?? 'Agus Nuwibowo S.Si., M.M.' }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">NIP Kepala BPS <span class="text-red-500">*</span></label>
                        <input type="text" name="nip_pejabat_kiri" required
                            value="{{ $pejabat->nip_pejabat_kiri ?? '197208271993121001' }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm font-mono">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Teks Baris 1</label>
                        <input type="text" name="jabatan_kiri_1" value="{{ $pejabat->jabatan_kiri_1 ?? 'Mengetahui' }}" class="w-full px-3 py-1.5 rounded-lg border border-gray-300">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Teks Baris 2</label>
                        <input type="text" name="jabatan_kiri_2" value="{{ $pejabat->jabatan_kiri_2 ?? 'Penanggung Jawab KPB' }}" class="w-full px-3 py-1.5 rounded-lg border border-gray-300">
                    </div>
                    <div>
                        <label class="block font-semibold text-gray-600 mb-1">Teks Baris 3</label>
                        <input type="text" name="jabatan_kiri_3" value="{{ $pejabat->jabatan_kiri_3 ?? 'Kepala BPS Kabupaten Bandung' }}" class="w-full px-3 py-1.5 rounded-lg border border-gray-300">
                    </div>
                </div>
            </div>

            <div class="border border-gray-200 rounded-xl p-5 bg-gray-50/50 space-y-4">
                <div class="flex items-center gap-2 pb-2 border-b border-gray-200">
                    <span class="w-6 h-6 rounded-full bg-green-600 text-white flex items-center justify-center text-xs font-bold">2</span>
                    <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide">Pihak Kanan: Petugas Pengelola Persediaan</h3>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tempat &amp; Tanggal Dokumen <span class="text-red-500">*</span></label>
                        <input type="text" name="tempat_tanggal_kanan" required
                            value="{{ $pejabat->tempat_tanggal_kanan ?? 'Soreang, 30 Juni 2026' }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Jabatan Penandatangan</label>
                        <input type="text" name="jabatan_kanan"
                            value="{{ $pejabat->jabatan_kanan ?? 'Petugas Pengelola Persediaan' }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Petugas Persediaan <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_pejabat_kanan" required
                            value="{{ $pejabat->nama_pejabat_kanan ?? 'Firmansyah Saragih, A.Md.' }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm font-semibold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">NIP Petugas Persediaan <span class="text-red-500">*</span></label>
                        <input type="text" name="nip_pejabat_kanan" required
                            value="{{ $pejabat->nip_pejabat_kanan ?? '198702092022031003' }}"
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 text-sm font-mono">
                    </div>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-200">
                <button type="submit" class="bg-orange-600 hover:bg-orange-700 text-white font-bold px-6 py-2.5 rounded-xl shadow-md transition flex items-center gap-2 text-sm cursor-pointer">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan Pejabat
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
