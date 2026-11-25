@extends('layouts.app')

@section('title', 'Peminjaman & Pengambilan Barang')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-hand-holding-hand"></i>
            </div>
            <div>
                <h1 class="text-xl font-black text-gray-900">Peminjaman Barang</h1>
                <p class="text-xs text-gray-500">Pencatatan sirkulasi peminjaman &amp; pengambilan barang persediaan BPS Kabupaten Bandung</p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            @if(Auth::user()->isAdmin())
            <button type="button" onclick="document.getElementById('modalHapusSemuaPeminjaman').classList.remove('hidden')"
                class="bg-red-50 hover:bg-red-100 text-red-700 border border-red-200 font-bold px-3.5 py-2.5 rounded-xl text-sm transition flex items-center gap-1.5 shadow-sm cursor-pointer">
                <i class="fa-solid fa-trash-can text-red-600"></i> Hapus Data
            </button>
            @endif
            <a href="{{ route('peminjaman.create') }}" class="bg-orange-600 hover:bg-orange-700 text-white font-bold px-4 py-2.5 rounded-xl text-sm shadow transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Ajukan Peminjaman Baru
            </a>
        </div>
    </div>

    <!-- Status Filter Badges (HANYA DIPINJAM & DIKEMBALIKAN) -->
    <div class="flex flex-wrap items-center gap-2 bg-white p-3 rounded-xl border border-gray-200 shadow-sm text-xs font-semibold">
        <span class="text-gray-500 mr-1"><i class="fa-solid fa-filter text-orange-600 mr-1"></i>Status:</span>
        <a href="{{ route('peminjaman.index') }}" class="px-3 py-1.5 rounded-lg transition {{ !$statusFilter ? 'bg-orange-600 text-white shadow-sm' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
            Semua
        </a>
        <a href="{{ route('peminjaman.index', ['status' => 'dipinjam']) }}" class="px-3 py-1.5 rounded-lg transition {{ in_array($statusFilter, ['dipinjam', 'disetujui']) ? 'bg-green-600 text-white shadow-sm' : 'bg-green-50 text-green-800 hover:bg-green-100' }}">
            <i class="fa-solid fa-circle-check mr-1"></i> DIPINJAM
        </a>
        <a href="{{ route('peminjaman.index', ['status' => 'dikembalikan']) }}" class="px-3 py-1.5 rounded-lg transition {{ in_array($statusFilter, ['dikembalikan', 'selesai']) ? 'bg-blue-600 text-white shadow-sm' : 'bg-blue-50 text-blue-800 hover:bg-blue-100' }}">
            <i class="fa-solid fa-check-double mr-1"></i> DIKEMBALIKAN
        </a>
    </div>

    <!-- Tabel Daftar Peminjaman -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 border-b border-gray-200 text-xs uppercase tracking-wider font-semibold">
                        <th class="py-3 px-4">No. Transaksi / Bon</th>
                        <th class="py-3 px-4">Tgl Peminjaman</th>
                        <th class="py-3 px-4">Pegawai &amp; Unit</th>
                        <th class="py-3 px-4">Barang</th>
                        <th class="py-3 px-4 text-center">Jumlah</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-xs">
                    @forelse($peminjamanList as $p)
                    <tr class="hover:bg-orange-50/30 transition">
                        <td class="py-3 px-4 font-mono font-bold text-gray-900">
                            <div>{{ $p->no_bon ?: $p->no_transaksi }}</div>
                            <span class="text-[10px] text-gray-400 font-normal">{{ $p->no_transaksi }}</span>
                        </td>
                        <td class="py-3 px-4 text-gray-600 whitespace-nowrap">
                            {{ $p->tanggal_pinjam }}
                            @if($p->tanggal_kembali)
                                <div class="text-[10px] text-gray-400">Kembali: {{ $p->tanggal_kembali }}</div>
                            @endif
                        </td>
                        <td class="py-3 px-4">
                            <div class="font-bold text-gray-900">{{ $p->peminjam_nama }}</div>
                            <div class="text-[11px] text-gray-500">{{ $p->unit_kerja ?: 'BPS Kabupaten Bandung' }}</div>
                        </td>
                        <td class="py-3 px-4">
                            <div class="font-semibold text-gray-900">{{ $p->nama_barang }}</div>
                            <div class="text-[10px] font-mono text-gray-400">{{ $p->kode_barang }}</div>
                        </td>
                        <td class="py-3 px-4 text-center font-bold text-sm text-orange-600">
                            {{ $p->jumlah }}
                        </td>
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
                            <div class="flex items-center justify-center gap-1.5">
                                <a href="{{ route('peminjaman.detail', $p->id) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Lihat Bukti / Detail">
                                    <i class="fa-solid fa-file-lines text-sm"></i>
                                </a>

                                @if(Auth::user()->isAdmin())
                                    @if(!in_array($p->status, ['selesai', 'dikembalikan']))
                                    <form method="POST" action="{{ route('peminjaman.kembalikan', $p->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2 py-1 text-[11px] font-bold bg-blue-50 text-blue-700 hover:bg-blue-100 rounded-lg transition cursor-pointer" title="Tandai Dikembalikan">
                                            <i class="fa-solid fa-rotate-left mr-1"></i>Kembalikan
                                        </button>
                                    </form>
                                    @endif

                                    <button type="button" onclick="openEditPinjam({{ $p->id }}, '{{ addslashes($p->peminjam_nama) }}', '{{ addslashes($p->unit_kerja ?? '') }}', {{ $p->jumlah }}, '{{ addslashes($p->keperluan ?? '') }}', '{{ $p->status }}')"
                                        class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition cursor-pointer" title="Edit Transaksi">
                                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                                    </button>

                                    <form method="POST" action="{{ route('peminjaman.destroy', $p->id) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition cursor-pointer"
                                            onclick="return confirm('Hapus data transaksi {{ $p->no_transaksi }}?')" title="Hapus Data">
                                            <i class="fa-solid fa-trash text-sm"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-8 text-gray-400">Belum ada data peminjaman barang.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@if(Auth::user()->isAdmin())
<!-- Modal Edit Transaksi -->
<div id="modalEditPeminjaman" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="text-base font-bold text-gray-900">Edit Peminjaman Barang</h3>
            <button type="button" onclick="document.getElementById('modalEditPeminjaman').classList.add('hidden')" class="text-gray-400 hover:text-gray-700"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="formEditPeminjaman" method="POST" class="space-y-3 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-gray-700 mb-1">Nama Peminjam / Pengambil</label>
                <input type="text" name="peminjam_nama" id="ep_nama" required class="w-full px-3 py-2 border rounded-xl">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Unit Kerja</label>
                    <input type="text" name="unit_kerja" id="ep_unit" class="w-full px-3 py-2 border rounded-xl">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Jumlah</label>
                    <input type="number" name="jumlah" id="ep_jumlah" min="1" required class="w-full px-3 py-2 border rounded-xl">
                </div>
            </div>
            <div>
                <label class="block font-bold text-gray-700 mb-1">Status</label>
                <select name="status" id="ep_status" class="w-full px-3 py-2 border rounded-xl bg-white">
                    <option value="disetujui">DIPINJAM</option>
                    <option value="selesai">DIKEMBALIKAN</option>
                </select>
            </div>
            <div>
                <label class="block font-bold text-gray-700 mb-1">Keperluan</label>
                <textarea name="keperluan" id="ep_keperluan" rows="2" class="w-full px-3 py-2 border rounded-xl"></textarea>
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="document.getElementById('modalEditPeminjaman').classList.add('hidden')" class="px-4 py-2 border rounded-xl font-semibold text-gray-600">Batal</button>
                <button type="submit" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-xl font-bold">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Hapus Semua -->
<div id="modalHapusSemuaPeminjaman" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl space-y-4 text-center">
        <div class="w-14 h-14 bg-red-100 text-red-600 rounded-2xl mx-auto flex items-center justify-center text-2xl">
            <i class="fa-solid fa-triangle-exclamation"></i>
        </div>
        <h3 class="text-lg font-bold text-gray-900">Hapus Seluruh Data?</h3>
        <p class="text-xs text-gray-500">Seluruh data transaksi peminjaman barang akan dihapus permanen.</p>
        <div class="grid grid-cols-2 gap-3 pt-2">
            <button type="button" onclick="document.getElementById('modalHapusSemuaPeminjaman').classList.add('hidden')" class="py-2.5 border rounded-xl font-bold text-xs text-gray-700">BATAL</button>
            <form method="POST" action="{{ route('peminjaman.destroy_all') }}">
                @csrf
                <button type="submit" class="w-full py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold text-xs">HAPUS</button>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@section('scripts')
<script>
function openEditPinjam(id, nama, unit, jumlah, keperluan, status) {
    document.getElementById('formEditPeminjaman').action = '/peminjaman/update/' + id;
    document.getElementById('ep_nama').value = nama;
    document.getElementById('ep_unit').value = unit;
    document.getElementById('ep_jumlah').value = jumlah;
    document.getElementById('ep_keperluan').value = keperluan;
    document.getElementById('ep_status').value = (status === 'selesai' || status === 'dikembalikan') ? 'selesai' : 'disetujui';
    document.getElementById('modalEditPeminjaman').classList.remove('hidden');
}
</script>
@endsection
