@extends('layouts.app')

@section('title', 'Kelola Pengguna Sistem')

@section('content')
<div class="space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-users"></i>
            </div>
            <div>
                <h1 class="text-xl font-black text-gray-900">Manajemen Pengguna</h1>
                <p class="text-xs text-gray-500">Kelola akun Administrator dan Staf Pengguna SABAR BPS Kabupaten Bandung</p>
            </div>
        </div>
        <button type="button" onclick="document.getElementById('modalTambahPengguna').classList.remove('hidden')"
            class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2.5 rounded-xl text-sm shadow transition flex items-center gap-2 cursor-pointer">
            <i class="fa-solid fa-user-plus"></i> Tambah Pengguna
        </button>
    </div>

    <!-- Tabel Pengguna -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-gray-50 text-gray-600 border-b border-gray-200 uppercase tracking-wider font-semibold">
                        <th class="py-3 px-4 text-center w-12">No</th>
                        <th class="py-3 px-4">Nama Lengkap</th>
                        <th class="py-3 px-4">Username</th>
                        <th class="py-3 px-4">NIP</th>
                        <th class="py-3 px-4">Unit Kerja</th>
                        <th class="py-3 px-4 text-center">Role</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($users as $idx => $u)
                    <tr class="hover:bg-blue-50/30 transition">
                        <td class="py-3 px-4 text-center font-bold text-gray-500">{{ $idx + 1 }}</td>
                        <td class="py-3 px-4 font-bold text-gray-900">{{ $u->nama_lengkap }}</td>
                        <td class="py-3 px-4 font-mono text-gray-700">{{ $u->username }}</td>
                        <td class="py-3 px-4 font-mono text-gray-500">{{ $u->nip ?: '-' }}</td>
                        <td class="py-3 px-4 text-gray-600">{{ $u->unit_kerja ?: '-' }}</td>
                        <td class="py-3 px-4 text-center">
                            @if(strtolower($u->role) === 'admin')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-green-100 text-green-800 uppercase">ADMIN</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-800 uppercase">PENGGUNA</span>
                            @endif
                        </td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <button type="button" onclick="openEditUserModal({{ $u->id }}, '{{ addslashes($u->nama_lengkap) }}', '{{ addslashes($u->username) }}', '{{ addslashes($u->nip ?? '') }}', '{{ addslashes($u->unit_kerja ?? '') }}', '{{ strtolower($u->role) }}')"
                                    class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition cursor-pointer" title="Edit Pengguna">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                @if(Auth::id() !== $u->id)
                                <form method="POST" action="{{ route('pengguna.destroy', $u->id) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition cursor-pointer"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna {{ addslashes($u->username) }}?')" title="Hapus Pengguna">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Pengguna -->
<div id="modalTambahPengguna" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="text-base font-bold text-gray-900">Tambah Pengguna Baru</h3>
            <button type="button" onclick="document.getElementById('modalTambahPengguna').classList.add('hidden')" class="text-gray-400 hover:text-gray-700"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form method="POST" action="{{ route('pengguna.store') }}" class="space-y-3 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-gray-700 mb-1">Nama Lengkap *</label>
                <input type="text" name="nama_lengkap" required class="w-full px-3 py-2 border rounded-xl">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Username *</label>
                    <input type="text" name="username" required class="w-full px-3 py-2 border rounded-xl">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Role *</label>
                    <select name="role" required class="w-full px-3 py-2 border rounded-xl bg-white">
                        <option value="pengguna">PENGGUNA</option>
                        <option value="admin">ADMIN</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">NIP</label>
                    <input type="text" name="nip" class="w-full px-3 py-2 border rounded-xl">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Unit Kerja</label>
                    <input type="text" name="unit_kerja" class="w-full px-3 py-2 border rounded-xl" placeholder="Fungsi / Subbagian">
                </div>
            </div>
            <div>
                <label class="block font-bold text-gray-700 mb-1">Password *</label>
                <input type="password" name="password" required minlength="4" class="w-full px-3 py-2 border rounded-xl">
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="document.getElementById('modalTambahPengguna').classList.add('hidden')" class="px-4 py-2 border rounded-xl font-semibold text-gray-600">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Pengguna -->
<div id="modalEditPengguna" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b pb-3">
            <h3 class="text-base font-bold text-gray-900">Edit Pengguna</h3>
            <button type="button" onclick="document.getElementById('modalEditPengguna').classList.add('hidden')" class="text-gray-400 hover:text-gray-700"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form id="formEditPengguna" method="POST" class="space-y-3 text-xs">
            @csrf
            <div>
                <label class="block font-bold text-gray-700 mb-1">Nama Lengkap *</label>
                <input type="text" name="nama_lengkap" id="edit_nama" required class="w-full px-3 py-2 border rounded-xl">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Username *</label>
                    <input type="text" name="username" id="edit_username" required class="w-full px-3 py-2 border rounded-xl">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Role *</label>
                    <select name="role" id="edit_role" required class="w-full px-3 py-2 border rounded-xl bg-white">
                        <option value="pengguna">PENGGUNA</option>
                        <option value="admin">ADMIN</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block font-bold text-gray-700 mb-1">NIP</label>
                    <input type="text" name="nip" id="edit_nip" class="w-full px-3 py-2 border rounded-xl">
                </div>
                <div>
                    <label class="block font-bold text-gray-700 mb-1">Unit Kerja</label>
                    <input type="text" name="unit_kerja" id="edit_unit" class="w-full px-3 py-2 border rounded-xl">
                </div>
            </div>
            <div>
                <label class="block font-bold text-gray-700 mb-1">Password Baru (Kosongkan jika tidak diubah)</label>
                <input type="password" name="password" minlength="4" class="w-full px-3 py-2 border rounded-xl">
            </div>
            <div class="flex justify-end gap-2 pt-3 border-t">
                <button type="button" onclick="document.getElementById('modalEditPengguna').classList.add('hidden')" class="px-4 py-2 border rounded-xl font-semibold text-gray-600">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function openEditUserModal(id, nama, username, nip, unit, role) {
    document.getElementById('formEditPengguna').action = '/pengguna/edit/' + id;
    document.getElementById('edit_nama').value = nama;
    document.getElementById('edit_username').value = username;
    document.getElementById('edit_nip').value = nip;
    document.getElementById('edit_unit').value = unit;
    document.getElementById('edit_role').value = role;
    document.getElementById('modalEditPengguna').classList.remove('hidden');
}
</script>
@endsection
