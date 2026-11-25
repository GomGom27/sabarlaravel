@extends('layouts.app')

@section('title', 'Kartu Kendali Persediaan Barang - BPS Kabupaten Bandung')

@section('content')
<div class="space-y-6">

    <!-- Top Toolbar -->
    <div class="no-print flex flex-col md:flex-row items-start md:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-table-list"></i>
            </div>
            <div>
                <h1 class="text-xl font-black text-gray-900">Kartu Kendali Persediaan Barang</h1>
                <p class="text-xs text-gray-500">Format Rekonstruksi Formulir Fisik BPS Kabupaten Bandung (Tabel Kiri &amp; Tabel Kanan)</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <form method="GET" action="{{ route('kartu_kendali.index') }}" class="flex items-center gap-2">
                <select name="barang_id" onchange="this.form.submit()"
                    class="text-xs font-semibold py-2 px-3 rounded-xl border border-gray-300 focus:outline-none focus:ring-2 focus:ring-orange-500 bg-white shadow-sm">
                    @foreach($barangList as $b)
                        <option value="{{ $b->id }}" {{ ($selectedBarang && $selectedBarang->id == $b->id) ? 'selected' : '' }}>
                            {{ $b->nama_barang }} ({{ $b->kode_barang }})
                        </option>
                    @endforeach
                </select>
            </form>

            @if(Auth::user()->isAdmin())
            <button type="button" onclick="document.getElementById('modalTambahTransaksi').classList.remove('hidden')"
                class="bg-green-600 hover:bg-green-700 text-white font-bold px-3.5 py-2 rounded-xl text-xs shadow transition flex items-center gap-1.5 cursor-pointer">
                <i class="fa-solid fa-plus-circle"></i> Tambah Transaksi
            </button>
            <button type="button" onclick="document.getElementById('modalHapusSemuaKK').classList.remove('hidden')"
                class="bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 font-bold px-3.5 py-2 rounded-xl text-xs transition flex items-center gap-1.5 shadow-sm cursor-pointer">
                <i class="fa-solid fa-trash-can text-red-600"></i> Hapus Data
            </button>
            @endif

            <a href="{{ route('kartu_kendali.export.excel', ['barang_id' => $selectedBarang->id ?? 1]) }}"
                class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-3.5 py-2 rounded-xl text-xs shadow transition flex items-center gap-1.5">
                <i class="fa-solid fa-file-excel"></i> Download Excel
            </a>

            <a href="{{ route('kartu_kendali.export.pdf', ['barang_id' => $selectedBarang->id ?? 1]) }}"
                class="bg-red-600 hover:bg-red-700 text-white font-bold px-3.5 py-2 rounded-xl text-xs shadow transition flex items-center gap-1.5">
                <i class="fa-solid fa-file-pdf"></i> Download PDF
            </a>

            <a href="{{ route('kartu_kendali.print', ['barang_id' => $selectedBarang->id ?? 1]) }}" target="_blank"
                class="bg-orange-600 hover:bg-orange-700 text-white font-bold px-4 py-2 rounded-xl text-xs shadow transition flex items-center gap-1.5">
                <i class="fa-solid fa-print"></i> Cetak Dokumen
            </a>
        </div>
    </div>

    <!-- LEMBAR KARTU KENDALI -->
    <div class="p-4 sm:p-6 overflow-x-auto bg-gray-50/50 rounded-2xl border border-gray-200">
        <div id="kartuKendaliSheet" class="doc-sheet doc-sheet-landscape bg-white border border-black p-6 mx-auto shadow-md" style="font-family: Arial, sans-serif; min-width: 1050px;">

            <div class="relative border-b-2 border-black pb-3 mb-3">
                <div class="absolute right-0 top-0 text-right text-xs text-black whitespace-nowrap">
                    <div><b>Halaman</b> : 1</div>
                </div>

                <div class="flex flex-row items-center justify-center gap-3">
                    <img src="{{ asset('img/bps_logo.svg') }}" alt="Logo BPS" class="h-14 w-auto object-contain bg-transparent flex-shrink-0">
                    <div class="text-center">
                        <h2 class="font-bold text-sm tracking-tight text-black uppercase">BADAN PUSAT STATISTIK KABUPATEN BANDUNG</h2>
                        <h3 class="font-bold text-xs uppercase text-gray-800 tracking-wide">KARTU KENDALI PERSEDIAAN BARANG</h3>
                    </div>
                </div>
            </div>

            <div class="flex justify-between text-xs mb-2 font-semibold">
                <div>Nama Barang: <span class="font-bold">{{ $selectedBarang->nama_barang ?? '-' }}</span></div>
                <div>Kode: <span class="font-mono">{{ $selectedBarang->kode_barang ?? '-' }}</span> | Satuan: {{ $selectedBarang->satuan ?? 'Rim' }}</div>
            </div>

            <!-- PENGELUARAN TIAP BULAN -->
            <div class="mb-3">
                <p class="text-[10px] font-semibold text-black mb-1">Pengeluaran Tiap Bulan</p>
                <table class="table-official text-center text-xs w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th>Jan</th><th>Feb</th><th>Mar</th><th>Apr</th><th>Mei</th><th>Jun</th>
                            <th>Jul</th><th>Agt</th><th>Sept</th><th>Okt</th><th>Nov</th><th>Des</th>
                            <th>Jumlah Pengeluaran</th><th>Stock Awal</th><th>Stock Akhir</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $bulanan->jan ?? '-' }}</td>
                            <td>{{ $bulanan->feb ?? '-' }}</td>
                            <td>{{ $bulanan->mar ?? '-' }}</td>
                            <td>{{ $bulanan->apr ?? '-' }}</td>
                            <td>{{ $bulanan->mei ?? '-' }}</td>
                            <td>{{ $bulanan->jun ?? '-' }}</td>
                            <td>{{ $bulanan->jul ?? '-' }}</td>
                            <td>{{ $bulanan->agt ?? '-' }}</td>
                            <td>{{ $bulanan->sept ?? '-' }}</td>
                            <td>{{ $bulanan->okt ?? '-' }}</td>
                            <td>{{ $bulanan->nov ?? '-' }}</td>
                            <td>{{ $bulanan->des ?? '-' }}</td>
                            <td class="font-bold">{{ $bulanan->jumlah_pengeluaran ?? '-' }}</td>
                            <td>{{ $bulanan->stock_awal ?? '-' }}</td>
                            <td class="font-bold">{{ $bulanan->stock_akhir ?? '-' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- TABEL KIRI & KANAN -->
            <div class="grid grid-cols-1 xl:grid-cols-2 gap-3 text-xs">
                @foreach(['kiri' => $transaksiKiri, 'kanan' => $transaksiKanan] as $bagian => $rows)
                <div class="w-full">
                    <table class="table-official text-[9.5px] w-full">
                        <thead>
                            <tr class="bg-gray-50">
                                <th rowspan="2" class="w-6 text-center">No</th>
                                <th rowspan="2" class="w-24 text-center">No. Bon/Faktur</th>
                                <th rowspan="2" class="w-16 text-center">Tgl M/K</th>
                                <th rowspan="2" class="w-28 text-center">Uraian / Penerima</th>
                                <th colspan="3" class="text-center">Pemasukan</th>
                                <th colspan="3" class="text-center">Pengeluaran</th>
                                <th rowspan="2" class="w-10 text-center">{{ $bagian === 'kiri' ? 'Sisa' : 'Sisa / Pindahan' }}</th>
                                @if(Auth::user()->isAdmin())
                                <th rowspan="2" class="no-print w-12 text-center">Aksi</th>
                                @endif
                            </tr>
                            <tr class="bg-gray-50 text-[8px]">
                                <th class="text-center">Beli</th><th class="text-center">Trf</th><th class="text-center">Lain</th>
                                <th class="text-center">Beli</th><th class="text-center">Trf</th><th class="text-center">Lain</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rows as $row)
                            <tr>
                                <td class="text-center">{{ $row->baris_no }}</td>
                                <td class="font-mono text-[8.5px]">{{ $row->no_bon_faktur ?? '' }}</td>
                                <td class="text-center text-[8.5px]">{{ $row->tgl_mk ?? '' }}</td>
                                <td class="truncate max-w-[120px]" title="{{ $row->uraian_penerima ?? '' }}">{{ $row->uraian_penerima ?? '' }}</td>
                                <td class="text-center font-semibold">{{ $row->masuk_pembelian > 0 ? $row->masuk_pembelian : '' }}</td>
                                <td class="text-center">{{ $row->masuk_transfer > 0 ? $row->masuk_transfer : '' }}</td>
                                <td class="text-center">{{ $row->masuk_lainnya > 0 ? $row->masuk_lainnya : '' }}</td>
                                <td class="text-center">{{ $row->keluar_pembelian > 0 ? $row->keluar_pembelian : '' }}</td>
                                <td class="text-center font-semibold">{{ $row->keluar_transfer > 0 ? $row->keluar_transfer : '' }}</td>
                                <td class="text-center">{{ $row->keluar_lainnya > 0 ? $row->keluar_lainnya : '' }}</td>
                                <td class="text-center font-bold">{{ $row->sisa_saldo > 0 ? $row->sisa_saldo : '' }}</td>
                                @if(Auth::user()->isAdmin())
                                <td class="no-print text-center whitespace-nowrap">
                                    <button type="button"
                                        onclick="bukaModalEditKK('{{ $row->id }}', '{{ addslashes($row->no_bon_faktur ?? '') }}', '{{ addslashes($row->tgl_mk ?? '') }}', '{{ addslashes($row->uraian_penerima ?? '') }}', '{{ $row->masuk_pembelian }}', '{{ $row->masuk_transfer }}', '{{ $row->masuk_lainnya }}', '{{ $row->keluar_pembelian }}', '{{ $row->keluar_transfer }}', '{{ $row->keluar_lainnya }}', '{{ $row->sisa_saldo }}')"
                                        class="{{ $row->no_bon_faktur ? 'text-blue-600 hover:text-blue-800' : 'text-gray-400 hover:text-green-600' }} p-0.5 cursor-pointer"
                                        title="{{ $row->no_bon_faktur ? 'Edit' : 'Isi' }} Baris {{ $row->baris_no }}">
                                        <i class="fa-solid {{ $row->no_bon_faktur ? 'fa-pen' : 'fa-plus' }} text-[9px]"></i>
                                    </button>
                                    @if($row->no_bon_faktur)
                                    <form method="POST" action="{{ route('kartu_kendali.hapus', $row->id) }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="barang_id" value="{{ $selectedBarang->id ?? 1 }}">
                                        <button type="submit" class="text-red-600 hover:text-red-800 p-0.5 cursor-pointer"
                                            onclick="return confirm('Hapus transaksi baris {{ $row->baris_no }}?')" title="Hapus">
                                            <i class="fa-solid fa-trash text-[9px]"></i>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Transaksi -->
<div id="modalTambahTransaksi" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="text-base font-bold text-gray-900">Tambah Transaksi Kartu Kendali</h3>
            <button type="button" onclick="document.getElementById('modalTambahTransaksi').classList.add('hidden')" class="text-gray-400 hover:text-gray-700"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="{{ route('kartu_kendali.tambah') }}" class="space-y-3 text-xs">
            @csrf
            <input type="hidden" name="barang_id" value="{{ $selectedBarang->id ?? 1 }}">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Bagian Tabel</label>
                    <select name="bagian" class="w-full px-3 py-2 border rounded-xl bg-white">
                        <option value="kiri">Tabel Kiri</option>
                        <option value="kanan">Tabel Kanan</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Tanggal M/K</label>
                    <input type="text" name="tgl_mk" value="{{ date('d/m/Y') }}" class="w-full px-3 py-2 border rounded-xl">
                </div>
            </div>
            <div>
                <label class="block font-bold text-gray-700 mb-1">No. Bon / Faktur</label>
                <input type="text" name="no_bon_faktur" required class="w-full px-3 py-2 border rounded-xl" placeholder="Contoh: 35/PK-3204/2026">
            </div>
            <div>
                <label class="block font-bold text-gray-700 mb-1">Uraian / Penerima</label>
                <input type="text" name="uraian_penerima" required class="w-full px-3 py-2 border rounded-xl" placeholder="Contoh: Listya P / IPDS">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Tipe Transaksi</label>
                    <select name="tipe_transaksi" class="w-full px-3 py-2 border rounded-xl bg-white">
                        <option value="masuk_pembelian">Masuk (Pembelian)</option>
                        <option value="keluar_transfer">Keluar (Transfer)</option>
                        <option value="keluar_pembelian">Keluar (Pembelian)</option>
                    </select>
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Jumlah</label>
                    <input type="number" name="jumlah" min="1" value="1" required class="w-full px-3 py-2 border rounded-xl">
                </div>
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="document.getElementById('modalTambahTransaksi').classList.add('hidden')" class="px-4 py-2 border rounded-xl font-semibold text-gray-600">Batal</button>
                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl font-bold">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Baris KK -->
<div id="modalEditKK" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="text-base font-bold text-gray-900">Edit Baris Transaksi Kartu Kendali</h3>
            <button type="button" onclick="document.getElementById('modalEditKK').classList.add('hidden')" class="text-gray-400 hover:text-gray-700"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="formEditKK" method="POST" class="space-y-3 text-xs">
            @csrf
            <input type="hidden" name="barang_id" value="{{ $selectedBarang->id ?? 1 }}">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">No. Bon / Faktur</label>
                    <input type="text" name="no_bon_faktur" id="kk_bon" class="w-full px-3 py-2 border rounded-xl">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Tanggal M/K</label>
                    <input type="text" name="tgl_mk" id="kk_tgl" class="w-full px-3 py-2 border rounded-xl">
                </div>
            </div>
            <div>
                <label class="block font-bold text-gray-700 mb-1">Uraian / Penerima</label>
                <input type="text" name="uraian_penerima" id="kk_uraian" class="w-full px-3 py-2 border rounded-xl">
            </div>
            <div class="grid grid-cols-3 gap-2">
                <div><label class="block font-bold mb-1">Masuk Beli</label><input type="number" name="masuk_pembelian" id="kk_mp" class="w-full px-2 py-1.5 border rounded-lg"></div>
                <div><label class="block font-bold mb-1">Masuk Trf</label><input type="number" name="masuk_transfer" id="kk_mt" class="w-full px-2 py-1.5 border rounded-lg"></div>
                <div><label class="block font-bold mb-1">Masuk Lain</label><input type="number" name="masuk_lainnya" id="kk_ml" class="w-full px-2 py-1.5 border rounded-lg"></div>
                <div><label class="block font-bold mb-1">Keluar Beli</label><input type="number" name="keluar_pembelian" id="kk_kp" class="w-full px-2 py-1.5 border rounded-lg"></div>
                <div><label class="block font-bold mb-1">Keluar Trf</label><input type="number" name="keluar_transfer" id="kk_kt" class="w-full px-2 py-1.5 border rounded-lg"></div>
                <div><label class="block font-bold mb-1">Keluar Lain</label><input type="number" name="keluar_lainnya" id="kk_kl" class="w-full px-2 py-1.5 border rounded-lg"></div>
            </div>
            <div>
                <label class="block font-bold text-gray-700 mb-1">Sisa Saldo</label>
                <input type="number" name="sisa_saldo" id="kk_sisa" class="w-full px-3 py-2 border rounded-xl font-bold">
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="document.getElementById('modalEditKK').classList.add('hidden')" class="px-4 py-2 border rounded-xl font-semibold text-gray-600">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Hapus Semua KK -->
<div id="modalHapusSemuaKK" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl space-y-4 text-center">
        <div class="w-14 h-14 bg-red-100 text-red-600 rounded-2xl mx-auto flex items-center justify-center text-2xl">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <h3 class="text-base font-bold text-gray-900">Kosongkan Data Kartu Kendali?</h3>
        <p class="text-xs text-gray-500">Apakah Anda yakin ingin mengosongkan transaksi untuk barang yang dipilih?</p>
        <div class="grid grid-cols-2 gap-3 pt-2">
            <button type="button" onclick="document.getElementById('modalHapusSemuaKK').classList.add('hidden')" class="py-2 border rounded-xl font-bold text-xs text-gray-700">BATAL</button>
            <form method="POST" action="{{ route('kartu_kendali.hapus_semua') }}">
                @csrf
                <input type="hidden" name="barang_id" value="{{ $selectedBarang->id ?? 1 }}">
                <input type="hidden" name="scope" value="selected">
                <button type="submit" class="w-full py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold text-xs">HAPUS</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function bukaModalEditKK(id, bon, tgl, uraian, mp, mt, ml, kp, kt, kl, sisa) {
    document.getElementById('formEditKK').action = '/kartu-kendali/edit-transaksi/' + id;
    document.getElementById('kk_bon').value = bon;
    document.getElementById('kk_tgl').value = tgl;
    document.getElementById('kk_uraian').value = uraian;
    document.getElementById('kk_mp').value = mp || 0;
    document.getElementById('kk_mt').value = mt || 0;
    document.getElementById('kk_ml').value = ml || 0;
    document.getElementById('kk_kp').value = kp || 0;
    document.getElementById('kk_kt').value = kt || 0;
    document.getElementById('kk_kl').value = kl || 0;
    document.getElementById('kk_sisa').value = sisa || 0;
    document.getElementById('modalEditKK').classList.remove('hidden');
}
</script>
@endsection
