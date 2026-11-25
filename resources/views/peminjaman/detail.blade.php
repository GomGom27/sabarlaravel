@extends('layouts.app')

@section('title', 'Bukti Pengambilan - ' . $p->no_transaksi)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="no-print flex items-center justify-between">
        <a href="{{ route('peminjaman.index') }}" class="text-sm font-semibold text-gray-600 hover:text-orange-600 flex items-center gap-1.5 transition">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Daftar Peminjaman
        </a>
        <button onclick="window.print()" class="bg-orange-600 hover:bg-orange-700 text-white font-bold px-4 py-2 rounded-xl text-xs sm:text-sm shadow transition flex items-center gap-2 cursor-pointer">
            <i class="fa-solid fa-print"></i> Cetak Bukti Pengambilan
        </button>
    </div>

    <div class="doc-sheet doc-sheet-portrait bg-white border border-gray-300 shadow-md p-10" style="font-family: Arial, sans-serif;">
        <div class="border-b-2 border-black pb-4 mb-6 text-center">
            <div class="flex flex-row items-center justify-center gap-3.5">
                <img src="{{ asset('img/bps_logo.svg') }}" alt="Logo BPS" class="h-12 w-auto object-contain bg-transparent flex-shrink-0">
                <div class="text-center">
                    <h2 class="text-base font-bold uppercase tracking-wider text-black leading-tight">BADAN PUSAT STATISTIK KABUPATEN BANDUNG</h2>
                    <p class="text-xs text-black mt-0.5">Jl. Raya Soreang Km. 17 No. 23, Soreang, Kabupaten Bandung, Jawa Barat</p>
                </div>
            </div>
            <div class="mt-3.5">
                <h3 class="text-sm font-bold uppercase tracking-wide text-black underline">SURAT BUKTI PENGELUARAN / PEMINJAMAN BARANG</h3>
                <p class="text-xs font-mono font-bold text-black mt-1">NOMOR: {{ $p->no_bon ?: $p->no_transaksi }}</p>
            </div>
        </div>

        <div class="space-y-4 text-xs text-black">
            <p>Pada hari ini tanggal {{ $p->tanggal_pinjam }}, telah diberikan/dikeluarkan barang persediaan inventori BPS Kabupaten Bandung dengan rincian sebagai berikut:</p>

            <table class="table-official">
                <tbody>
                    <tr>
                        <td class="w-48 font-semibold bg-gray-50">Nama Pengambil</td>
                        <td><b>{{ $p->peminjam_nama }}</b></td>
                    </tr>
                    <tr>
                        <td class="font-semibold bg-gray-50">NIP / Identitas</td>
                        <td>{{ $p->nip_pemohon ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="font-semibold bg-gray-50">Unit Kerja / Seksi</td>
                        <td>{{ $p->unit_kerja ?: 'Badan Pusat Statistik Kabupaten Bandung' }}</td>
                    </tr>
                    <tr>
                        <td class="font-semibold bg-gray-50">Nama Barang</td>
                        <td><b>{{ $p->nama_barang }}</b></td>
                    </tr>
                    <tr>
                        <td class="font-semibold bg-gray-50">Kode Barang</td>
                        <td class="font-mono">{{ $p->kode_barang }}</td>
                    </tr>
                    <tr>
                        <td class="font-semibold bg-gray-50">Jumlah Dikeluarkan</td>
                        <td><b>{{ $p->jumlah }} {{ $p->satuan ?: 'Pcs' }}</b></td>
                    </tr>
                    <tr>
                        <td class="font-semibold bg-gray-50">Keterangan / Estimasi</td>
                        <td>{{ $p->tanggal_kembali ?: 'Pengeluaran Habis Pakai / Menyesuaikan Kegiatan' }}</td>
                    </tr>
                    <tr>
                        <td class="font-semibold bg-gray-50">Keperluan Penggunaan</td>
                        <td>{{ $p->keperluan ?: '-' }}</td>
                    </tr>
                    <tr>
                        <td class="font-semibold bg-gray-50">Status Peminjaman</td>
                        <td class="font-bold uppercase">{{ in_array($p->status, ['selesai', 'dikembalikan']) ? 'DIKEMBALIKAN' : 'DIPINJAM' }}</td>
                    </tr>
                </tbody>
            </table>

            <p class="text-[11px] italic mt-2">Catatan: Pengambil bertanggung jawab penuh terhadap pemanfaatan barang inventori sesuai dengan ketentuan kedinasan BPS.</p>
        </div>

        @php $pejabat = \App\Models\PengaturanPejabat::orderByDesc('id')->first(); @endphp
        <div class="mt-12 grid grid-cols-2 gap-8 text-center text-xs text-black">
            <div>
                <p>Pengambil / Penerima Barang,</p>
                <div class="h-20"></div>
                <p class="font-bold underline">{{ $p->peminjam_nama }}</p>
                <p class="text-[10px]">NIP. {{ $p->nip_pemohon ?: '....................................' }}</p>
            </div>
            <div>
                <p>Petugas Pengelola Persediaan,</p>
                <div class="h-20"></div>
                <p class="font-bold underline">{{ $pejabat->nama_pejabat_kanan ?? 'Firmansyah Saragih, A.Md.' }}</p>
                <p class="text-[10px]">NIP. {{ $pejabat->nip_pejabat_kanan ?? '198702092022031003' }}</p>
            </div>
        </div>
    </div>

</div>
@endsection
