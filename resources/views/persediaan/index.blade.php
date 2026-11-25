@extends('layouts.app')

@section('title', 'Tabel Persediaan Barang')

@section('extra_head')
<link rel="stylesheet" href="{{ asset('css/persediaan.css') }}">
@endsection

@section('content')
<div class="space-y-6">

    <!-- TOP TOOLBAR AKSI -->
    <div class="no-print bg-white p-5 rounded-2xl border border-gray-200 shadow-sm space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-700 flex items-center justify-center text-lg shadow-inner">
                    <i class="fa-solid fa-boxes-stacked"></i>
                </div>
                <div>
                    <h1 class="text-xl font-black text-gray-900 flex items-center gap-2">
                        <span>PERSEDIAAN BARANG</span>
                        <span class="text-xs bg-orange-100 text-orange-700 px-2 py-0.5 rounded-full font-bold">Resmi BPS</span>
                    </h1>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                @if(Auth::user()->isAdmin())
                <button type="button" onclick="bukaModalTambah()"
                    class="bg-orange-600 hover:bg-orange-700 text-white font-bold px-3.5 py-2 rounded-xl text-xs shadow transition flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-plus-circle"></i>
                    <span>Tambah Barang</span>
                </button>

                <button type="button" onclick="document.getElementById('modalHapusSemuaPersediaan').classList.remove('hidden')"
                    class="bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 font-bold px-3.5 py-2 rounded-xl text-xs transition flex items-center gap-1.5 shadow-sm cursor-pointer">
                    <i class="fa-solid fa-trash-can text-red-600"></i>
                    <span>Hapus Data</span>
                </button>
                @endif

                <a href="{{ route('persediaan.export.excel') }}"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-3.5 py-2 rounded-xl text-xs shadow transition flex items-center gap-1.5">
                    <i class="fa-solid fa-file-excel text-sm"></i>
                    <span>Download Excel</span>
                </a>

                <a href="{{ route('persediaan.export.pdf') }}"
                    class="bg-red-600 hover:bg-red-700 text-white font-bold px-3.5 py-2 rounded-xl text-xs shadow transition flex items-center gap-1.5">
                    <i class="fa-solid fa-file-pdf text-sm"></i>
                    <span>Download PDF</span>
                </a>

                <a href="{{ route('persediaan.cetak') }}" target="_blank"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-3.5 py-2 rounded-xl text-xs shadow transition flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-print text-sm"></i>
                    <span>Cetak Dokumen</span>
                </a>

                <button type="button" onclick="bukaModalEditPejabat()"
                    class="bg-amber-50 hover:bg-amber-100 text-amber-800 border border-amber-300 font-bold px-3.5 py-2 rounded-xl text-xs transition flex items-center gap-1.5 shadow-sm cursor-pointer">
                    <i class="fa-solid fa-user-pen text-amber-700"></i>
                    <span>Edit Pejabat</span>
                </button>
            </div>
        </div>

        <!-- Filter, Pencarian, dan Pengurutan -->
        <form method="GET" action="{{ route('persediaan.index') }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 pt-3 border-t border-gray-100 text-xs">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
                <input type="text" name="q" value="{{ $searchQuery }}" placeholder="Cari nama barang atau no..."
                    class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-xs outline-none">
            </div>

            <div>
                <select name="filter_kondisi" class="w-full py-2 px-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-xs outline-none bg-white">
                    <option value="" {{ !$filterKondisi ? 'selected' : '' }}>Semua Kondisi Barang</option>
                    <option value="rusak" {{ $filterKondisi === 'rusak' ? 'selected' : '' }}>Hanya Barang Rusak (&gt; 0)</option>
                    <option value="usang" {{ $filterKondisi === 'usang' ? 'selected' : '' }}>Hanya Barang Usang (&gt; 0)</option>
                    <option value="normal" {{ $filterKondisi === 'normal' ? 'selected' : '' }}>Barang Normal / Baik</option>
                </select>
            </div>

            <div>
                <select name="sort" class="w-full py-2 px-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 text-xs outline-none bg-white">
                    <option value="no_asc" {{ $sortBy === 'no_asc' ? 'selected' : '' }}>Urut: Nomor (1 s.d 51+)</option>
                    <option value="no_desc" {{ $sortBy === 'no_desc' ? 'selected' : '' }}>Urut: Nomor Menurun</option>
                    <option value="nama_asc" {{ $sortBy === 'nama_asc' ? 'selected' : '' }}>Urut: Nama Barang (A - Z)</option>
                    <option value="harga_desc" {{ $sortBy === 'harga_desc' ? 'selected' : '' }}>Urut: Harga Tertinggi</option>
                    <option value="total_desc" {{ $sortBy === 'total_desc' ? 'selected' : '' }}>Urut: Total Nilai Tertinggi</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-3 rounded-xl transition flex items-center justify-center gap-1.5 shadow-sm cursor-pointer">
                    <i class="fa-solid fa-filter"></i> Terapkan
                </button>
                <a href="{{ route('persediaan.index') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-600 font-semibold py-2 px-3 rounded-xl transition" title="Reset Pencarian & Filter">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>

        <!-- Ringkasan Statistik -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2 text-xs">
            <div class="bg-orange-50 p-2.5 rounded-xl border border-orange-200">
                <span class="text-gray-500 block text-[10px] uppercase font-bold">Total Jenis Barang</span>
                <span class="text-sm font-black text-orange-700">{{ $summary['total_items'] }} Item</span>
            </div>
            <div class="bg-blue-50 p-2.5 rounded-xl border border-blue-200">
                <span class="text-gray-500 block text-[10px] uppercase font-bold">Total Fisik Persediaan</span>
                <span class="text-sm font-black text-blue-700">{{ number_format($summary['total_persediaan_qty'], 0, ',', '.') }} Unit</span>
            </div>
            <div class="bg-emerald-50 p-2.5 rounded-xl border border-emerald-200">
                <span class="text-gray-500 block text-[10px] uppercase font-bold">Total Nilai Persediaan</span>
                <span class="text-sm font-black text-emerald-700">Rp {{ number_format($summary['total_persediaan_nilai'], 0, ',', '.') }}</span>
            </div>
            <div class="bg-gray-50 p-2.5 rounded-xl border border-gray-200">
                <span class="text-gray-500 block text-[10px] uppercase font-bold">Rusak / Usang</span>
                <span class="text-sm font-bold text-gray-700">{{ $summary['total_rusak_qty'] }} Rusak &bull; {{ $summary['total_usang_qty'] }} Usang</span>
            </div>
        </div>
    </div>

    <!-- DOKUMEN RESMI TABEL PERSEDIAAN BARANG -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-300 p-4 sm:p-6 overflow-x-auto">
        <div class="doc-sheet-persediaan p-4 bg-white">

            <div class="border-b-2 border-black pb-4 mb-5 text-center relative">
                <div class="absolute right-0 top-0 text-right text-[10px] font-mono text-gray-600 no-print">
                    <span class="bg-gray-100 px-2 py-0.5 rounded border border-gray-200">FORM-BPS-PERSED-3204</span>
                </div>

                <div class="flex flex-col items-center justify-center">
                    <div class="flex flex-row items-center justify-center gap-3.5">
                        <img src="{{ asset('img/bps_logo.svg') }}" alt="Logo BPS" class="h-10 w-auto object-contain bg-transparent flex-shrink-0">
                        <h2 class="font-bold text-base uppercase tracking-wider text-black">BADAN PUSAT STATISTIK KABUPATEN BANDUNG</h2>
                    </div>
                    <p class="text-[10px] text-gray-800 mt-1 font-normal text-center">
                        Jl. Raya Soreang Km. 17 No. 23, Soreang, Kabupaten Bandung, Jawa Barat 40911 &bull; Telepon: (022) 5891726 &bull; Pos-el: bps3204@bps.go.id
                    </p>
                    <div class="mt-2 text-center">
                        <h3 class="font-black text-sm uppercase tracking-wider text-black">TABEL PERSEDIAAN BARANG</h3>
                    </div>
                </div>
            </div>

            <table class="table-persediaan-bps">
                <thead>
                    <tr>
                        <th rowspan="2" class="col-no">No.</th>
                        <th rowspan="2" class="col-uraian">Uraian Barang</th>
                        <th rowspan="2" class="col-satuan">Satuan</th>
                        <th rowspan="2" class="col-harga-beli">Harga Beli<br/>(Rupiah)</th>
                        <th colspan="2" class="text-center">Total Persediaan</th>
                        <th colspan="2" class="text-center">Barang Rusak</th>
                        <th colspan="2" class="text-center">Barang Usang</th>
                        @if(Auth::user()->isAdmin())
                        <th rowspan="2" class="col-aksi no-print">Aksi</th>
                        @endif
                    </tr>
                    <tr class="sub-header">
                        <th class="col-persediaan-jml">Jumlah</th>
                        <th class="col-persediaan-total">Harga Total (Rupiah)<br/><span class="text-[9px] font-normal font-mono">(4) × (5)</span></th>
                        <th class="col-rusak-jml">Jumlah</th>
                        <th class="col-rusak-total">Harga Total (Rupiah)<br/><span class="text-[9px] font-normal font-mono">(4) × (7)</span></th>
                        <th class="col-usang-jml">Jumlah</th>
                        <th class="col-usang-total">Harga Total (Rupiah)<br/><span class="text-[9px] font-normal font-mono">(4) × (9)</span></th>
                    </tr>
                    <tr class="col-num-row">
                        <th>(1)</th><th>(2)</th><th>(3)</th><th>(4)</th><th>(5)</th>
                        <th>(6)</th><th>(7)</th><th>(8)</th><th>(9)</th><th>(10)</th>
                        @if(Auth::user()->isAdmin())
                        <th class="no-print">-</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $item)
                    <tr>
                        <td class="text-center font-semibold">{{ $item->nomor_urut }}</td>
                        <td class="font-medium">
                            {{ $item->uraian_barang }}
                            @if($item->keterangan)
                            <span class="text-[9px] text-gray-500 italic block">{{ $item->keterangan }}</span>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->satuan }}</td>
                        <td class="text-rupiah">Rp {{ number_format($item->harga_beli, 0, ',', '.') }}</td>
                        <td class="text-rupiah-center font-semibold">{{ number_format($item->total_persediaan_jumlah, 0, ',', '.') }}</td>
                        <td class="text-rupiah font-semibold">Rp {{ number_format($item->harga_total_persediaan, 0, ',', '.') }}</td>
                        <td class="{{ $item->rusak_jumlah > 0 ? 'text-rupiah text-red-600 font-bold' : 'text-dash' }}">
                            {{ $item->rusak_jumlah > 0 ? $item->rusak_jumlah : '-' }}
                        </td>
                        <td class="{{ $item->rusak_harga_total > 0 ? 'text-rupiah text-red-600 font-semibold' : 'text-dash' }}">
                            {{ $item->rusak_harga_total > 0 ? 'Rp ' . number_format($item->rusak_harga_total, 0, ',', '.') : '-' }}
                        </td>
                        <td class="{{ $item->usang_jumlah > 0 ? 'text-rupiah text-amber-700 font-bold' : 'text-dash' }}">
                            {{ $item->usang_jumlah > 0 ? $item->usang_jumlah : '-' }}
                        </td>
                        <td class="{{ $item->usang_harga_total > 0 ? 'text-rupiah text-amber-700 font-semibold' : 'text-dash' }}">
                            {{ $item->usang_harga_total > 0 ? 'Rp ' . number_format($item->usang_harga_total, 0, ',', '.') : '-' }}
                        </td>
                        @if(Auth::user()->isAdmin())
                        <td class="col-aksi no-print text-center">
                            <div class="inline-flex items-center gap-1">
                                <button type="button"
                                    onclick="bukaModalEdit({{ $item->id }}, {{ $item->nomor_urut }}, '{{ addslashes($item->uraian_barang) }}', '{{ addslashes($item->satuan) }}', {{ $item->harga_beli }}, {{ $item->total_persediaan_jumlah }}, {{ $item->rusak_jumlah }}, {{ $item->usang_jumlah }}, '{{ addslashes($item->keterangan ?? '') }}')"
                                    class="p-1 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded cursor-pointer" title="Edit Data Barang">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <button type="button"
                                    onclick="konfirmasiHapus({{ $item->id }}, {{ $item->nomor_urut }}, '{{ addslashes($item->uraian_barang) }}')"
                                    class="p-1 text-red-600 hover:text-red-800 hover:bg-red-50 rounded cursor-pointer" title="Hapus Data Barang">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ Auth::user()->isAdmin() ? 11 : 10 }}" class="py-8 text-center text-gray-500">
                            <i class="fa-solid fa-inbox text-3xl mb-2 text-gray-300 block"></i>
                            Tidak ada data barang yang sesuai dengan filter atau kata kunci pencarian.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="4" class="text-center uppercase font-black tracking-wide">JUMLAH TOTAL</td>
                        <td class="text-rupiah-center font-black">{{ number_format($summary['total_persediaan_qty'], 0, ',', '.') }}</td>
                        <td class="text-rupiah font-black">Rp {{ number_format($summary['total_persediaan_nilai'], 0, ',', '.') }}</td>
                        <td class="{{ $summary['total_rusak_qty'] > 0 ? 'text-rupiah font-black' : 'text-dash' }}">{{ $summary['total_rusak_qty'] > 0 ? $summary['total_rusak_qty'] : '-' }}</td>
                        <td class="{{ $summary['total_rusak_nilai'] > 0 ? 'text-rupiah font-black' : 'text-dash' }}">{{ $summary['total_rusak_nilai'] > 0 ? 'Rp ' . number_format($summary['total_rusak_nilai'], 0, ',', '.') : '-' }}</td>
                        <td class="{{ $summary['total_usang_qty'] > 0 ? 'text-rupiah font-black' : 'text-dash' }}">{{ $summary['total_usang_qty'] > 0 ? $summary['total_usang_qty'] : '-' }}</td>
                        <td class="{{ $summary['total_usang_nilai'] > 0 ? 'text-rupiah font-black' : 'text-dash' }}">{{ $summary['total_usang_nilai'] > 0 ? 'Rp ' . number_format($summary['total_usang_nilai'], 0, ',', '.') : '-' }}</td>
                        @if(Auth::user()->isAdmin())
                        <td class="no-print"></td>
                        @endif
                    </tr>
                </tfoot>
            </table>

            <!-- PENGESAHAN -->
            <div class="pengesahan-container">
                <div class="ttd-box">
                    <p class="font-bold">{{ $pejabat->jabatan_kiri_1 ?? 'Mengetahui,' }}</p>
                    <p class="font-bold">{{ $pejabat->jabatan_kiri_3 ?? 'Kepala BPS Kabupaten Bandung' }}</p>
                    <div class="ttd-space"></div>
                    <p class="font-bold underline">{{ $pejabat->nama_pejabat_kiri ?? 'Agus Nuwibowo S.Si., M.M.' }}</p>
                    <p class="text-[10px] text-gray-700">NIP. {{ $pejabat->nip_pejabat_kiri ?? '197208271993121001' }}</p>
                </div>

                <div class="ttd-box">
                    <p>{{ $pejabat->tempat_tanggal_kanan ?? 'Soreang, ..............................' }}</p>
                    <p class="font-bold">{{ $pejabat->jabatan_kanan ?? 'Petugas Pengelola Persediaan' }}</p>
                    <div class="ttd-space"></div>
                    <p class="font-bold underline">{{ $pejabat->nama_pejabat_kanan ?? 'Firmansyah Saragih, A.Md.' }}</p>
                    <p class="text-[10px] text-gray-700">NIP. {{ $pejabat->nip_pejabat_kanan ?? '198702092022031003' }}</p>
                </div>
            </div>

            <div class="mt-8 pt-2 border-t border-gray-400 text-[9px] text-gray-500 flex justify-between items-center">
                <span>Dokumen Resmi Sistem Inventori Persediaan BPS Kabupaten Bandung</span>
                <span>{{ $pejabat->tempat_tanggal_kanan ?? 'Soreang, ..............................' }}</span>
            </div>

        </div>
    </div>

</div>

<!-- MODAL TAMBAH -->
<div id="modalTambahPersediaan" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[9999] flex items-center justify-center hidden p-4" onclick="if(event.target===this) tutupModalTambah()">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 border border-gray-100 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-gray-200">
            <h3 class="font-bold text-gray-900 text-base flex items-center gap-2">
                <i class="fa-solid fa-plus-circle text-orange-600"></i>
                Tambah Data Persediaan Barang
            </h3>
            <button type="button" onclick="tutupModalTambah()" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>

        <form method="POST" action="{{ route('persediaan.store') }}" class="space-y-4 text-xs">
            @csrf
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">No. Urut</label>
                    <input type="number" name="nomor_urut" id="tambah_nomor_urut" value="{{ $nextNoUrut }}" required min="1"
                        class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 outline-none font-bold">
                </div>
                <div class="col-span-2">
                    <label class="block font-semibold text-gray-700 mb-1">Uraian / Nama Barang</label>
                    <input type="text" name="uraian_barang" required placeholder="Contoh: HVS F4 75 Gram"
                        class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Satuan</label>
                    <input type="text" name="satuan" required placeholder="Rim / Dus / Pcs / Box"
                        class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 outline-none">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Harga Beli (Rupiah)</label>
                    <input type="number" name="harga_beli" id="tambah_harga_beli" value="0" min="0" step="100" required oninput="hitungTotalTambah()"
                        class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 outline-none text-right font-mono">
                </div>
            </div>

            <div class="p-3 bg-orange-50/60 rounded-xl border border-orange-200 space-y-2">
                <div class="flex items-center justify-between">
                    <label class="font-bold text-gray-800">Total Persediaan (Jumlah Fisik)</label>
                    <span id="tambah_harga_total_persediaan_preview" class="font-bold text-orange-700 font-mono text-sm">Rp 0</span>
                </div>
                <input type="number" name="total_persediaan_jumlah" id="tambah_total_persediaan_jumlah" value="0" min="0" required oninput="hitungTotalTambah()"
                    class="w-full p-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 outline-none text-right font-bold text-sm bg-white">
                <p class="text-[10px] text-gray-500">Rumus: (4) Harga Beli &times; (5) Jumlah Persediaan = (6) Harga Total</p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="p-3 bg-red-50/50 rounded-xl border border-red-200 space-y-1">
                    <label class="block font-bold text-gray-800">Barang Rusak (Jumlah)</label>
                    <input type="number" name="rusak_jumlah" id="tambah_rusak_jumlah" value="0" min="0" oninput="hitungTotalTambah()"
                        class="w-full p-2 border border-gray-300 rounded-lg text-right font-mono bg-white">
                    <div class="text-[10px] text-gray-500 flex justify-between pt-1">
                        <span>Total Rusak:</span>
                        <span id="tambah_rusak_harga_total_preview" class="font-semibold text-red-600 font-mono">Rp 0</span>
                    </div>
                </div>

                <div class="p-3 bg-amber-50/50 rounded-xl border border-amber-200 space-y-1">
                    <label class="block font-bold text-gray-800">Barang Usang (Jumlah)</label>
                    <input type="number" name="usang_jumlah" id="tambah_usang_jumlah" value="0" min="0" oninput="hitungTotalTambah()"
                        class="w-full p-2 border border-gray-300 rounded-lg text-right font-mono bg-white">
                    <div class="text-[10px] text-gray-500 flex justify-between pt-1">
                        <span>Total Usang:</span>
                        <span id="tambah_usang_harga_total_preview" class="font-semibold text-amber-700 font-mono">Rp 0</span>
                    </div>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-gray-700 mb-1">Keterangan Tambahan (Opsional)</label>
                <input type="text" name="keterangan" placeholder="Catatan kondisi, nomor register BMN, dsb."
                    class="w-full p-2 border border-gray-300 rounded-lg outline-none">
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-200">
                <button type="button" onclick="tutupModalTambah()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition">Batal</button>
                <button type="submit" class="px-5 py-2 bg-orange-600 hover:bg-orange-700 text-white font-bold rounded-xl shadow transition flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Data Barang
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT -->
<div id="modalEditPersediaan" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[9999] flex items-center justify-center hidden p-4" onclick="if(event.target===this) tutupModalEdit()">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 border border-gray-100 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-gray-200">
            <h3 class="font-bold text-gray-900 text-base flex items-center gap-2">
                <i class="fa-solid fa-pen-to-square text-blue-600"></i>
                Edit Data Persediaan Barang
            </h3>
            <button type="button" onclick="tutupModalEdit()" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>

        <form id="formEditPersediaan" method="POST" action="" class="space-y-4 text-xs">
            @csrf
            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">No. Urut</label>
                    <input type="number" name="nomor_urut" id="edit_nomor_urut" required min="1"
                        class="w-full p-2 border border-gray-300 rounded-lg font-bold">
                </div>
                <div class="col-span-2">
                    <label class="block font-semibold text-gray-700 mb-1">Uraian / Nama Barang</label>
                    <input type="text" name="uraian_barang" id="edit_uraian_barang" required
                        class="w-full p-2 border border-gray-300 rounded-lg font-semibold">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Satuan</label>
                    <input type="text" name="satuan" id="edit_satuan" required class="w-full p-2 border border-gray-300 rounded-lg">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Harga Beli (Rupiah)</label>
                    <input type="number" name="harga_beli" id="edit_harga_beli" min="0" step="100" required oninput="hitungTotalEdit()"
                        class="w-full p-2 border border-gray-300 rounded-lg text-right font-mono">
                </div>
            </div>

            <div class="p-3 bg-blue-50/60 rounded-xl border border-blue-200 space-y-2">
                <div class="flex items-center justify-between">
                    <label class="font-bold text-gray-800">Total Persediaan (Jumlah Fisik)</label>
                    <span id="edit_harga_total_persediaan_preview" class="font-bold text-blue-700 font-mono text-sm">Rp 0</span>
                </div>
                <input type="number" name="total_persediaan_jumlah" id="edit_total_persediaan_jumlah" min="0" required oninput="hitungTotalEdit()"
                    class="w-full p-2 border border-gray-300 rounded-lg text-right font-bold text-sm bg-white">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div class="p-3 bg-red-50/50 rounded-xl border border-red-200 space-y-1">
                    <label class="block font-bold text-gray-800">Barang Rusak (Jumlah)</label>
                    <input type="number" name="rusak_jumlah" id="edit_rusak_jumlah" min="0" oninput="hitungTotalEdit()"
                        class="w-full p-2 border border-gray-300 rounded-lg text-right font-mono bg-white">
                    <div class="text-[10px] text-gray-500 flex justify-between pt-1">
                        <span>Total Rusak:</span>
                        <span id="edit_rusak_harga_total_preview" class="font-semibold text-red-600 font-mono">Rp 0</span>
                    </div>
                </div>

                <div class="p-3 bg-amber-50/50 rounded-xl border border-amber-200 space-y-1">
                    <label class="block font-bold text-gray-800">Barang Usang (Jumlah)</label>
                    <input type="number" name="usang_jumlah" id="edit_usang_jumlah" min="0" oninput="hitungTotalEdit()"
                        class="w-full p-2 border border-gray-300 rounded-lg text-right font-mono bg-white">
                    <div class="text-[10px] text-gray-500 flex justify-between pt-1">
                        <span>Total Usang:</span>
                        <span id="edit_usang_harga_total_preview" class="font-semibold text-amber-700 font-mono">Rp 0</span>
                    </div>
                </div>
            </div>

            <div>
                <label class="block font-semibold text-gray-700 mb-1">Keterangan</label>
                <input type="text" name="keterangan" id="edit_keterangan" class="w-full p-2 border border-gray-300 rounded-lg">
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-200">
                <button type="button" onclick="tutupModalEdit()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition">Batal</button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow transition flex items-center gap-1.5 cursor-pointer">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL HAPUS DATA (BATAL / HAPUS) -->
<div id="modalHapusPersediaan" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[9999] flex items-center justify-center hidden p-4" onclick="if(event.target===this) tutupModalHapus()">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl space-y-4 text-center border border-gray-100">
        <div class="w-14 h-14 bg-red-100 text-red-600 rounded-2xl mx-auto flex items-center justify-center text-2xl shadow-inner">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <div class="space-y-1">
            <h3 class="text-base font-bold text-gray-900">Konfirmasi Hapus Data</h3>
            <p class="text-xs text-gray-500 leading-relaxed">
                Apakah Anda yakin ingin menghapus data barang ini?
            </p>
            <p id="hapus_nama_barang" class="text-xs font-bold text-red-600 bg-red-50 py-1.5 px-3 rounded-lg border border-red-200 mt-2 break-words"></p>
        </div>
        <form id="formHapusPersediaan" method="POST" action="" class="grid grid-cols-2 gap-3 pt-2">
            @csrf
            <button type="button" onclick="tutupModalHapus()" class="py-2 px-4 rounded-xl border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold text-xs uppercase tracking-wider transition cursor-pointer">
                BATAL
            </button>
            <button type="submit" class="py-2 px-4 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs uppercase tracking-wider shadow transition cursor-pointer">
                HAPUS
            </button>
        </form>
    </div>
</div>

<!-- MODAL HAPUS SEMUA PERSEDIAAN -->
<div id="modalHapusSemuaPersediaan" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[9999] flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl space-y-4 text-center border border-gray-100">
        <div class="w-14 h-14 bg-red-100 text-red-600 rounded-2xl mx-auto flex items-center justify-center text-2xl">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <h3 class="text-base font-bold text-gray-900">Hapus Seluruh Data Persediaan?</h3>
        <p class="text-xs text-gray-500">Apakah Anda yakin ingin menghapus seluruh data barang dari tabel persediaan?</p>
        <div class="grid grid-cols-2 gap-3 pt-2">
            <button type="button" onclick="document.getElementById('modalHapusSemuaPersediaan').classList.add('hidden')" class="py-2 px-4 rounded-xl border border-gray-300 hover:bg-gray-100 text-gray-700 font-bold text-xs uppercase">BATAL</button>
            <form method="POST" action="{{ route('persediaan.destroy_all') }}">
                @csrf
                <button type="submit" class="w-full py-2 px-4 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs uppercase">HAPUS</button>
            </form>
        </div>
    </div>
</div>

<!-- MODAL EDIT PEJABAT -->
<div id="modalEditPejabatPersediaan" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-[9999] flex items-center justify-center hidden p-4" onclick="if(event.target===this) tutupModalEditPejabat()">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4 border border-gray-100 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-gray-200">
            <h3 class="font-bold text-gray-900 text-base flex items-center gap-2">
                <i class="fa-solid fa-user-pen text-amber-600"></i>
                Edit Pejabat Penandatangan
            </h3>
            <button type="button" onclick="tutupModalEditPejabat()" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>

        <form method="POST" action="{{ route('persediaan.pejabat') }}" class="space-y-4 text-xs">
            @csrf
            <div class="p-3.5 bg-amber-50/70 rounded-xl border border-amber-200 space-y-3">
                <div class="font-bold text-amber-900 border-b border-amber-200/80 pb-2">Kepala Badan Pusat Statistik (Pihak Mengetahui)</div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Nama Lengkap &amp; Gelar Kepala BPS</label>
                    <input type="text" name="nama_pejabat_kiri" value="{{ $pejabat->nama_pejabat_kiri ?? 'Agus Nuwibowo S.Si., M.M.' }}" required class="w-full p-2 border rounded-lg bg-white font-semibold">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">NIP Kepala BPS</label>
                    <input type="text" name="nip_pejabat_kiri" value="{{ $pejabat->nip_pejabat_kiri ?? '197208271993121001' }}" required class="w-full p-2 border rounded-lg bg-white font-mono">
                </div>
            </div>

            <div class="p-3.5 bg-blue-50/70 rounded-xl border border-blue-200 space-y-3">
                <div class="font-bold text-blue-900 border-b border-blue-200/80 pb-2">Petugas Pengelola Persediaan</div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Tempat &amp; Tanggal Pengesahan</label>
                    <input type="text" name="tempat_tanggal_kanan" value="{{ $pejabat->tempat_tanggal_kanan ?? 'Soreang, ..............................' }}" required class="w-full p-2 border rounded-lg bg-white font-semibold">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">Nama Lengkap &amp; Gelar Petugas Pengelola</label>
                    <input type="text" name="nama_pejabat_kanan" value="{{ $pejabat->nama_pejabat_kanan ?? 'Firmansyah Saragih, A.Md.' }}" required class="w-full p-2 border rounded-lg bg-white font-semibold">
                </div>
                <div>
                    <label class="block font-semibold text-gray-700 mb-1">NIP Petugas Pengelola</label>
                    <input type="text" name="nip_pejabat_kanan" value="{{ $pejabat->nip_pejabat_kanan ?? '198702092022031003' }}" required class="w-full p-2 border rounded-lg bg-white font-mono">
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-gray-200">
                <button type="button" onclick="tutupModalEditPejabat()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition">Batal</button>
                <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-xl shadow transition cursor-pointer">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Pejabat
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/persediaan.js') }}"></script>
@endsection
