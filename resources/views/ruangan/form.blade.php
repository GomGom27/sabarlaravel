@extends('layouts.app')

@section('title', ($action === 'tambah' ? 'Tambah' : 'Edit') . ' Ruangan Penyimpanan')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <a href="{{ route('ruangan.index') }}" class="text-sm font-semibold text-gray-600 hover:text-green-700 flex items-center gap-1.5 transition">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Data Ruangan
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="bg-gradient-to-r from-green-700 to-green-600 p-6 text-white">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-xl">
                    <i class="fa-solid fa-door-open"></i>
                </div>
                <div>
                    <h2 class="text-lg font-bold">{{ $action === 'tambah' ? 'Tambah Ruangan Baru' : 'Edit Ruangan Penyimpanan' }}</h2>
                    <p class="text-xs text-green-100">Penyimpanan Persediaan &amp; Unit Kerja BPS Kabupaten Bandung</p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ $action === 'tambah' ? route('ruangan.store') : route('ruangan.update', $ruangan->id) }}" class="p-6 sm:p-8 space-y-5">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="kode_ruangan" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Kode Ruangan <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="kode_ruangan" name="kode_ruangan" required
                        value="{{ old('kode_ruangan', $ruangan->kode_ruangan ?? '') }}"
                        placeholder="Contoh: R-01 / G-ATK"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm font-mono">
                </div>

                <div>
                    <label for="lokasi_lantai" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                        Lokasi Lantai
                    </label>
                    <select id="lokasi_lantai" name="lokasi_lantai"
                        class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm bg-white">
                        @php $lantai = old('lokasi_lantai', $ruangan->lokasi_lantai ?? 'Lantai 1'); @endphp
                        <option value="Lantai 1" {{ $lantai === 'Lantai 1' ? 'selected' : '' }}>Lantai 1</option>
                        <option value="Lantai 2" {{ $lantai === 'Lantai 2' ? 'selected' : '' }}>Lantai 2</option>
                        <option value="Gedung Arsip" {{ $lantai === 'Gedung Arsip' ? 'selected' : '' }}>Gedung Arsip</option>
                    </select>
                </div>
            </div>

            <div>
                <label for="nama_ruangan" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                    Nama Ruangan <span class="text-red-500">*</span>
                </label>
                <input type="text" id="nama_ruangan" name="nama_ruangan" required
                    value="{{ old('nama_ruangan', $ruangan->nama_ruangan ?? '') }}"
                    placeholder="Contoh: Gudang Persediaan ATK"
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
            </div>

            <div>
                <label for="penanggung_jawab" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                    Penanggung Jawab Ruangan
                </label>
                <input type="text" id="penanggung_jawab" name="penanggung_jawab"
                    value="{{ old('penanggung_jawab', $ruangan->penanggung_jawab ?? '') }}"
                    placeholder="Contoh: Firmansyah Saragih, A.Md."
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">
            </div>

            <div>
                <label for="keterangan" class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">
                    Keterangan &amp; Catatan Ruangan
                </label>
                <textarea id="keterangan" name="keterangan" rows="3"
                    placeholder="Fungsi dan deskripsi ruangan penyimpanan..."
                    class="w-full px-4 py-2.5 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500 text-sm">{{ old('keterangan', $ruangan->keterangan ?? '') }}</textarea>
            </div>

            <div class="pt-4 flex items-center justify-end gap-3 border-t border-gray-100">
                <a href="{{ route('ruangan.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-300 text-gray-700 font-semibold text-sm hover:bg-gray-50 transition">Batal</a>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold px-6 py-2.5 rounded-xl shadow-md transition flex items-center gap-2 text-sm">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Data Ruangan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
