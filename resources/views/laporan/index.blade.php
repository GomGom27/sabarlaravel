@extends('layouts.app')

@section('title', 'Laporan Bulanan')

@section('content')
<div class="space-y-6">

    <!-- Top Action Toolbar -->
    <div class="no-print flex flex-col md:flex-row items-start md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-green-100 text-green-700 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-file-contract"></i>
            </div>
            <div>
                <h1 class="text-xl font-black text-gray-900">Laporan Bulanan</h1>
                <p class="text-xs text-gray-500">Format Rekonstruksi Dokumen Resmi - Kedinasan BPS Kabupaten Bandung</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            @if(Auth::user()->isAdmin())
            <button type="button" onclick="document.getElementById('modalTambahItemLaporan').classList.remove('hidden')"
                class="bg-green-600 hover:bg-green-700 text-white font-bold px-3.5 py-2 rounded-xl text-xs shadow transition flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-plus-circle"></i> Tambah Barang
            </button>

            <button type="button" onclick="document.getElementById('modalHapusSemuaLaporan').classList.remove('hidden')"
                class="bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 font-bold px-3.5 py-2 rounded-xl text-xs transition flex items-center gap-1.5 shadow-sm cursor-pointer">
                <i class="fa-solid fa-trash-can text-red-600"></i> Hapus Data
            </button>
            @endif

            <button type="button" onclick="bukaModalEkspor('excel')" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-3.5 py-2 rounded-xl text-xs shadow transition flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-excel text-sm"></i> Download Excel (.xlsx)
            </button>

            <button type="button" onclick="bukaModalEkspor('pdf')" class="bg-red-600 hover:bg-red-700 text-white font-bold px-3.5 py-2 rounded-xl text-xs shadow transition flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-file-pdf text-sm"></i> Download PDF
            </button>

            <button type="button" onclick="bukaModalEkspor('print')" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-3.5 py-2 rounded-xl text-xs shadow transition flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-print text-sm"></i> Cetak Dokumen
            </button>

            @if(Auth::user()->isAdmin())
            <a href="{{ route('pengaturan.pejabat') }}" class="bg-orange-50 hover:bg-orange-100 text-orange-700 border border-orange-200 font-bold px-3.5 py-2 rounded-xl text-xs transition flex items-center gap-1.5">
                <i class="fa-solid fa-user-pen"></i> Edit Pejabat
            </a>
            @endif
        </div>
    </div>

    <!-- FILTER PERIODE BULAN, TANGGAL, TAHUN -->
    <div class="no-print bg-white p-5 rounded-2xl border border-gray-200 shadow-sm space-y-4">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2 border-b border-gray-100 pb-3">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center text-sm shadow-inner">
                    <i class="fa-solid fa-filter"></i>
                </div>
                <div>
                    <h2 class="text-sm font-black text-gray-900 tracking-tight">Filter Riwayat Laporan Bulanan</h2>
                    <p class="text-[11px] text-gray-500">Pilih Bulan, Tanggal, atau Tahun untuk memfilter dan melihat riwayat data sebelumnya</p>
                </div>
            </div>
            @if($selectedBulan || $selectedTahun || $selectedTanggal || $searchQ)
            <a href="{{ route('laporan.index') }}" class="text-xs text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 border border-red-200 px-3 py-1.5 rounded-lg font-bold flex items-center gap-1.5 transition">
                <i class="fa-solid fa-arrow-rotate-left"></i> Reset Filter
            </a>
            @endif
        </div>

        <form method="GET" action="{{ route('laporan.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end text-xs">
            <div>
                <label class="block font-bold text-gray-700 uppercase mb-1"><i class="fa-regular fa-calendar text-orange-500 mr-1"></i> Bulan</label>
                <select name="bulan" class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-orange-500 bg-white font-medium text-xs">
                    <option value="">-- Semua Bulan --</option>
                    @foreach($daftarBulan as $bln)
                    <option value="{{ $bln }}" {{ $selectedBulan === $bln ? 'selected' : '' }}>{{ $bln }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-gray-700 uppercase mb-1"><i class="fa-regular fa-calendar-days text-orange-500 mr-1"></i> Tahun</label>
                <select name="tahun" class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-orange-500 bg-white font-medium text-xs">
                    <option value="">-- Semua Tahun --</option>
                    @foreach($daftarTahun as $th)
                    <option value="{{ $th }}" {{ $selectedTahun === (string)$th ? 'selected' : '' }}>{{ $th }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-bold text-gray-700 uppercase mb-1"><i class="fa-regular fa-clock text-orange-500 mr-1"></i> Tanggal</label>
                <input type="date" name="tanggal" value="{{ $selectedTanggal }}" class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-orange-500 bg-white font-medium text-xs">
            </div>

            <div>
                <label class="block font-bold text-gray-700 uppercase mb-1"><i class="fa-solid fa-magnifying-glass text-orange-500 mr-1"></i> Cari Barang</label>
                <input type="text" name="q" value="{{ $searchQ }}" placeholder="Contoh: Kertas, Toner..." class="w-full px-3 py-2 rounded-xl border border-gray-300 focus:ring-2 focus:ring-orange-500 bg-white text-xs">
            </div>

            <div>
                <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white font-bold py-2 px-3 rounded-xl shadow transition flex items-center justify-center gap-1.5 text-xs h-[38px] cursor-pointer">
                    <i class="fa-solid fa-filter"></i> Terapkan Filter
                </button>
            </div>
        </form>

        <!-- Status & Ringkasan Periode Aktif -->
        <div class="flex flex-wrap items-center justify-between gap-3 pt-3 border-t border-gray-100 text-xs">
            <div class="flex items-center gap-2">
                <span class="text-gray-500 font-medium">Periode Aktif:</span>
                <span class="font-bold text-orange-700 bg-orange-50 border border-orange-200 px-3 py-0.5 rounded-full">
                    <i class="fa-solid fa-calendar-check mr-1 text-orange-600"></i> {{ $periodeText }}
                </span>
            </div>
            <div class="flex flex-wrap items-center gap-3 text-xs">
                <span class="text-gray-600">Ditemukan: <b class="text-gray-900">{{ $items->count() }}</b> item</span>
                <span class="text-gray-600">Total Fisik: <b class="text-gray-900">{{ $totalQtyPersediaan }}</b> unit</span>
                <span class="text-gray-600">Total Nilai: <b class="text-emerald-700 font-mono">Rp {{ number_format($totalNilaiPersediaan, 0, ',', '.') }}</b></span>
                <div class="flex items-center gap-1.5 pl-2 border-l border-gray-200">
                    <span class="text-gray-400 text-[11px]">Unduh Hasil:</span>
                    <a href="{{ route('laporan.export.excel', ['bulan' => $selectedBulan, 'tahun' => $selectedTahun, 'tanggal' => $selectedTanggal, 'q' => $searchQ]) }}"
                        class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border border-emerald-200 px-2 py-0.5 rounded-lg text-[11px] font-bold flex items-center gap-1 transition">
                        <i class="fa-solid fa-file-excel"></i> Excel
                    </a>
                    <a href="{{ route('laporan.export.pdf', ['bulan' => $selectedBulan, 'tahun' => $selectedTahun, 'tanggal' => $selectedTanggal, 'q' => $searchQ]) }}"
                        class="bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 px-2 py-0.5 rounded-lg text-[11px] font-bold flex items-center gap-1 transition">
                        <i class="fa-solid fa-file-pdf"></i> PDF
                    </a>
                    <a href="{{ route('laporan.print', ['bulan' => $selectedBulan, 'tahun' => $selectedTahun, 'tanggal' => $selectedTanggal, 'q' => $searchQ]) }}" target="_blank"
                        class="bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 px-2 py-0.5 rounded-lg text-[11px] font-bold flex items-center gap-1 transition">
                        <i class="fa-solid fa-print"></i> Cetak
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- DOKUMEN TABEL LAPORAN RESMI -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-300 p-4 sm:p-6 overflow-x-auto">
        <div class="doc-sheet doc-sheet-landscape bg-white border border-black p-6 mx-auto" style="font-family: Arial, sans-serif; color: #000; width: 100%; min-width: 1050px;">

            <div class="relative border-b-2 border-black pb-3 mb-4">
                <div class="absolute right-0 top-0 text-right text-xs text-black whitespace-nowrap">
                    <span class="font-mono text-[10px] text-gray-600 bg-gray-100 px-2.5 py-1 rounded border border-gray-200">BMN.BPS-3204</span>
                </div>

                <div class="flex flex-col items-center justify-center">
                    <div class="flex flex-row items-center justify-center gap-3.5">
                        <img src="{{ asset('img/bps_logo.svg') }}" alt="Logo BPS" class="h-11 w-auto object-contain bg-transparent flex-shrink-0">
                        <div class="text-center">
                            <h2 class="font-bold text-sm tracking-tight text-black uppercase leading-tight">BADAN PUSAT STATISTIK KABUPATEN BANDUNG</h2>
                            <h3 class="font-bold text-xs uppercase text-gray-800 tracking-wide mt-0.5">DAFTAR PENGAMBILAN BARANG / ATK</h3>
                        </div>
                    </div>
                    <p class="text-[10px] text-gray-600 mt-1 text-center">Jl. Raya Soreang Km. 17 No. 23, Soreang, Kabupaten Bandung, Jawa Barat 40911 &bull; Telepon: (022) 5891726 &bull; Periode: {{ $periodeText }}</p>
                </div>
            </div>

            <table class="table-official text-[10px] w-full" style="border: 1px solid #000;">
                <thead>
                    <tr>
                        <th class="w-8 py-2 text-center" style="border: 1px solid #000;">No</th>
                        <th class="py-2 text-center" style="border: 1px solid #000;">Uraian Barang</th>
                        <th class="w-16 py-2 text-center" style="border: 1px solid #000;">Satuan</th>
                        <th class="w-24 py-2 text-center" style="border: 1px solid #000;">Harga Beli (Rp)</th>
                        <th class="w-20 py-2 text-center" style="border: 1px solid #000;">Total Persediaan (Jml)</th>
                        <th class="w-28 py-2 text-center" style="border: 1px solid #000;">Harga Total (Rp)</th>
                        <th class="w-24 py-2 text-center" style="border: 1px solid #000;">Rusak - Total (Rp)</th>
                        <th class="w-16 py-2 text-center" style="border: 1px solid #000;">Rusak (Jml)</th>
                        <th class="w-16 py-2 text-center" style="border: 1px solid #000;">Usang (Jml)</th>
                        <th class="w-24 py-2 text-center" style="border: 1px solid #000;">Usang - Total (Rp)</th>
                        @if(Auth::user()->isAdmin())
                        <th class="no-print w-16 py-2 text-center" style="border: 1px solid #000;">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($items as $itm)
                    <tr>
                        <td class="text-center py-1" style="border: 1px solid #000;">{{ $itm->nomor_urut }}</td>
                        <td class="py-1 px-2" style="border: 1px solid #000;">{{ $itm->uraian_barang }}</td>
                        <td class="text-center py-1" style="border: 1px solid #000;">{{ $itm->satuan }}</td>
                        <td class="text-right py-1 px-2" style="border: 1px solid #000;">Rp {{ number_format($itm->harga_beli, 0, ',', '.') }}</td>
                        <td class="text-center py-1" style="border: 1px solid #000;">{{ $itm->total_persediaan_jumlah }}</td>
                        <td class="text-right py-1 px-2 font-semibold" style="border: 1px solid #000;">Rp {{ number_format($itm->harga_total_persediaan, 0, ',', '.') }}</td>
                        <td class="text-center py-1" style="border: 1px solid #000;">{{ $itm->rusak_harga_total }}</td>
                        <td class="text-center py-1" style="border: 1px solid #000;">{{ $itm->rusak_jumlah }}</td>
                        <td class="text-center py-1" style="border: 1px solid #000;">{{ $itm->usang_jumlah }}</td>
                        <td class="text-center py-1" style="border: 1px solid #000;">{{ $itm->usang_harga_total }}</td>
                        @if(Auth::user()->isAdmin())
                        <td class="no-print text-center py-1" style="border: 1px solid #000;">
                            <button type="button" onclick="openEditLaporan({{ $itm->id }}, '{{ addslashes($itm->uraian_barang) }}', '{{ addslashes($itm->satuan) }}', {{ $itm->harga_beli }}, {{ $itm->total_persediaan_jumlah }}, '{{ addslashes($itm->periode_bulan) }}', {{ $itm->periode_tahun }}, '{{ $itm->tanggal_laporan }}')"
                                class="text-blue-600 hover:text-blue-800 p-0.5 cursor-pointer" title="Edit"><i class="fa-solid fa-pen text-[10px]"></i></button>
                            <form method="POST" action="{{ route('laporan.destroy', $itm->id) }}" class="inline">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-800 p-0.5 cursor-pointer" onclick="return confirm('Hapus item {{ addslashes($itm->uraian_barang) }}?')" title="Hapus">
                                    <i class="fa-solid fa-trash text-[10px]"></i>
                                </button>
                            </form>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ Auth::user()->isAdmin() ? 11 : 10 }}" class="text-center py-6 text-gray-400" style="border: 1px solid #000;">
                            Tidak ada data laporan untuk periode filter yang dipilih.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="font-bold bg-gray-100">
                        <td colspan="4" class="text-center py-1.5" style="border: 1px solid #000;">TOTAL</td>
                        <td class="text-center py-1.5" style="border: 1px solid #000;">{{ $totalQtyPersediaan }}</td>
                        <td class="text-right py-1.5 px-2" style="border: 1px solid #000;">Rp {{ number_format($totalNilaiPersediaan, 0, ',', '.') }}</td>
                        <td class="text-center py-1.5" style="border: 1px solid #000;">-</td>
                        <td class="text-center py-1.5" style="border: 1px solid #000;">-</td>
                        <td class="text-center py-1.5" style="border: 1px solid #000;">-</td>
                        <td class="text-center py-1.5" style="border: 1px solid #000;">-</td>
                        @if(Auth::user()->isAdmin())
                        <td class="no-print" style="border: 1px solid #000;"></td>
                        @endif
                    </tr>
                </tfoot>
            </table>

            <!-- Tanda Tangan Kiri & Kanan Sejajar Tabel -->
            <div class="mt-8 flex justify-between text-xs" style="page-break-inside: avoid;">
                <div class="text-left">
                    <p>{{ $pejabat->jabatan_kiri_1 ?? 'Mengetahui' }},</p>
                    <p>{{ $pejabat->jabatan_kiri_2 ?? 'Penanggung Jawab KPB' }}</p>
                    <p class="font-bold">{{ $pejabat->jabatan_kiri_3 ?? 'Kepala BPS Kabupaten Bandung' }}</p>
                    <div class="h-14"></div>
                    <p class="font-bold underline">{{ $pejabat->nama_pejabat_kiri ?? 'Agus Nuwibowo S.Si., M.M.' }}</p>
                    <p>NIP. {{ $pejabat->nip_pejabat_kiri ?? '197208271993121001' }}</p>
                </div>

                <div class="text-right">
                    <p>{{ $pejabat->tempat_tanggal_kanan ?? 'Soreang, ..............................' }}</p>
                    <p class="font-bold">{{ $pejabat->jabatan_kanan ?? 'Petugas Pengelola Persediaan' }}</p>
                    <div class="h-14"></div>
                    <p class="font-bold underline">{{ $pejabat->nama_pejabat_kanan ?? 'Firmansyah Saragih, A.Md.' }}</p>
                    <p>NIP. {{ $pejabat->nip_pejabat_kanan ?? '198702092022031003' }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Item Laporan -->
<div id="modalTambahItemLaporan" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="text-base font-bold text-gray-900">Tambah Item Laporan Bulanan</h3>
            <button type="button" onclick="document.getElementById('modalTambahItemLaporan').classList.add('hidden')" class="text-gray-400 hover:text-gray-700"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="{{ route('laporan.store') }}" class="space-y-3 text-xs">
            @csrf
            <div><label class="block font-bold mb-1">Uraian Barang *</label><input type="text" name="uraian_barang" required class="w-full px-3 py-2 border rounded-xl"></div>
            <div class="grid grid-cols-3 gap-2">
                <div><label class="block font-bold mb-1">Satuan *</label><input type="text" name="satuan" value="Pcs" required class="w-full px-3 py-2 border rounded-xl"></div>
                <div><label class="block font-bold mb-1">Harga Beli</label><input type="number" name="harga_beli" value="0" min="0" class="w-full px-3 py-2 border rounded-xl"></div>
                <div><label class="block font-bold mb-1">Jumlah</label><input type="number" name="total_persediaan_jumlah" value="0" min="0" class="w-full px-3 py-2 border rounded-xl"></div>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <div>
                    <label class="block font-bold mb-1">Bulan</label>
                    <select name="periode_bulan" class="w-full px-2 py-2 border rounded-xl bg-white">
                        @foreach($daftarBulan as $bln)
                        <option value="{{ $bln }}" {{ $bln === 'Juni' ? 'selected' : '' }}>{{ $bln }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="block font-bold mb-1">Tahun</label><input type="number" name="periode_tahun" value="2026" class="w-full px-3 py-2 border rounded-xl"></div>
                <div><label class="block font-bold mb-1">Tanggal</label><input type="date" name="tanggal_laporan" value="{{ date('Y-m-d') }}" class="w-full px-2 py-2 border rounded-xl"></div>
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="document.getElementById('modalTambahItemLaporan').classList.add('hidden')" class="px-4 py-2 border rounded-xl font-semibold text-gray-600">Batal</button>
                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Item Laporan -->
<div id="modalEditItemLaporan" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="text-base font-bold text-gray-900">Edit Item Laporan Bulanan</h3>
            <button type="button" onclick="document.getElementById('modalEditItemLaporan').classList.add('hidden')" class="text-gray-400 hover:text-gray-700"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="formEditItemLaporan" method="POST" class="space-y-3 text-xs">
            @csrf
            <div><label class="block font-bold mb-1">Uraian Barang *</label><input type="text" name="uraian_barang" id="el_nama" required class="w-full px-3 py-2 border rounded-xl"></div>
            <div class="grid grid-cols-3 gap-2">
                <div><label class="block font-bold mb-1">Satuan *</label><input type="text" name="satuan" id="el_satuan" required class="w-full px-3 py-2 border rounded-xl"></div>
                <div><label class="block font-bold mb-1">Harga Beli</label><input type="number" name="harga_beli" id="el_harga" min="0" class="w-full px-3 py-2 border rounded-xl"></div>
                <div><label class="block font-bold mb-1">Jumlah</label><input type="number" name="total_persediaan_jumlah" id="el_qty" min="0" class="w-full px-3 py-2 border rounded-xl"></div>
            </div>
            <div class="grid grid-cols-3 gap-2">
                <div>
                    <label class="block font-bold mb-1">Bulan</label>
                    <select name="periode_bulan" id="el_bulan" class="w-full px-2 py-2 border rounded-xl bg-white">
                        @foreach($daftarBulan as $bln)
                        <option value="{{ $bln }}">{{ $bln }}</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="block font-bold mb-1">Tahun</label><input type="number" name="periode_tahun" id="el_tahun" class="w-full px-3 py-2 border rounded-xl"></div>
                <div><label class="block font-bold mb-1">Tanggal</label><input type="date" name="tanggal_laporan" id="el_tgl" class="w-full px-2 py-2 border rounded-xl"></div>
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="document.getElementById('modalEditItemLaporan').classList.add('hidden')" class="px-4 py-2 border rounded-xl font-semibold text-gray-600">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Hapus Semua Laporan -->
<div id="modalHapusSemuaLaporan" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl space-y-4 text-center">
        <div class="w-14 h-14 bg-red-100 text-red-600 rounded-2xl mx-auto flex items-center justify-center text-2xl"><i class="fa-solid fa-triangle-exclamation"></i></div>
        <h3 class="text-base font-bold text-gray-900">Hapus Seluruh Data Laporan?</h3>
        <p class="text-xs text-gray-500">Apakah Anda yakin ingin menghapus seluruh item di Laporan Bulanan?</p>
        <div class="grid grid-cols-2 gap-3 pt-2">
            <button type="button" onclick="document.getElementById('modalHapusSemuaLaporan').classList.add('hidden')" class="py-2 border rounded-xl font-bold text-xs text-gray-700">BATAL</button>
            <form method="POST" action="{{ route('laporan.destroy_all') }}">
                @csrf
                <button type="submit" class="w-full py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold text-xs">HAPUS</button>
            </form>
        </div>
    </div>
</div>

<!-- Modal Ekspor dengan Filter Periode -->
<div id="modalEksporLaporan" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b pb-3">
            <h3 id="modalEksporTitle" class="text-base font-bold text-gray-900">Filter Periode Dokumen</h3>
            <button type="button" onclick="document.getElementById('modalEksporLaporan').classList.add('hidden')" class="text-gray-400 hover:text-gray-700"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="formEksporLaporan" method="GET" action="" class="space-y-3 text-xs">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold mb-1">Bulan</label>
                    <select name="bulan" class="w-full px-3 py-2 border rounded-xl bg-white">
                        <option value="">-- Semua Bulan --</option>
                        @foreach($daftarBulan as $bln)
                        <option value="{{ $bln }}" {{ $selectedBulan === $bln ? 'selected' : '' }}>{{ $bln }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block font-bold mb-1">Tahun</label>
                    <select name="tahun" class="w-full px-3 py-2 border rounded-xl bg-white">
                        <option value="">-- Semua Tahun --</option>
                        @foreach($daftarTahun as $th)
                        <option value="{{ $th }}" {{ $selectedTahun === (string)$th ? 'selected' : '' }}>{{ $th }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div>
                <label class="block font-bold mb-1">Tanggal Spesifik (Opsional)</label>
                <input type="date" name="tanggal" value="{{ $selectedTanggal }}" class="w-full px-3 py-2 border rounded-xl">
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="document.getElementById('modalEksporLaporan').classList.add('hidden')" class="px-4 py-2 border rounded-xl font-semibold text-gray-600">Batal</button>
                <button type="submit" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl font-bold cursor-pointer">Proses &amp; Unduh</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function openEditLaporan(id, nama, satuan, harga, qty, bulan, tahun, tgl) {
    document.getElementById('formEditItemLaporan').action = '/laporan/edit/' + id;
    document.getElementById('el_nama').value = nama;
    document.getElementById('el_satuan').value = satuan;
    document.getElementById('el_harga').value = harga;
    document.getElementById('el_qty').value = qty;
    document.getElementById('el_bulan').value = bulan;
    document.getElementById('el_tahun').value = tahun;
    document.getElementById('el_tgl').value = tgl;
    document.getElementById('modalEditItemLaporan').classList.remove('hidden');
}

function bukaModalEkspor(mode) {
    const form = document.getElementById('formEksporLaporan');
    const title = document.getElementById('modalEksporTitle');
    if (mode === 'excel') {
        form.action = "{{ route('laporan.export.excel') }}";
        form.target = "_self";
        title.textContent = "Filter Download Excel (.xlsx)";
    } else if (mode === 'pdf') {
        form.action = "{{ route('laporan.export.pdf') }}";
        form.target = "_self";
        title.textContent = "Filter Download PDF";
    } else {
        form.action = "{{ route('laporan.print') }}";
        form.target = "_blank";
        title.textContent = "Filter Cetak Dokumen";
    }
    document.getElementById('modalEksporLaporan').classList.remove('hidden');
}
</script>
@endsection
