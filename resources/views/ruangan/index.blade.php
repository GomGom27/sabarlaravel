@extends('layouts.app')

@section('title', 'Data Ruangan Penyimpanan')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-white p-5 rounded-2xl border border-gray-200 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-green-100 text-green-700 flex items-center justify-center text-xl shadow-inner">
                <i class="fa-solid fa-door-open"></i>
            </div>
            <div>
                <h1 class="text-xl font-black text-gray-900">Data Ruangan</h1>
                <p class="text-xs text-gray-500">Ruang Penyimpanan Persediaan Barang BPS Kabupaten Bandung</p>
            </div>
        </div>
        <a href="{{ route('ruangan.create') }}" class="bg-green-600 hover:bg-green-700 text-white font-bold px-4 py-2.5 rounded-xl text-xs sm:text-sm shadow transition flex items-center gap-2">
            <i class="fa-solid fa-plus"></i> Tambah Ruangan
        </a>
    </div>

    <!-- Grid Kartu Ruangan -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($ruanganList as $r)
        <div class="bg-white rounded-2xl p-5 border border-gray-200 shadow-sm hover:shadow-md transition flex flex-col justify-between group">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="font-mono text-xs font-bold px-2.5 py-1 bg-green-50 text-green-700 border border-green-200 rounded-lg">
                        {{ $r->kode_ruangan }}
                    </span>
                    <span class="text-xs font-semibold text-gray-400 flex items-center gap-1">
                        <i class="fa-solid fa-layer-group text-green-600"></i> {{ $r->lokasi_lantai ?: 'Lantai 1' }}
                    </span>
                </div>

                <h3 class="text-base font-bold text-gray-900 group-hover:text-green-700 transition mb-1">
                    {{ $r->nama_ruangan }}
                </h3>
                <p class="text-xs text-gray-500 line-clamp-2 mb-3">
                    {{ $r->keterangan ?: 'Ruangan kedinasan BPS Kabupaten Bandung' }}
                </p>
            </div>

            <div class="pt-3 border-t border-gray-100 flex items-center justify-between text-xs">
                <div class="flex items-center gap-1.5 text-gray-600">
                    <i class="fa-solid fa-user-tie text-gray-400"></i>
                    <span class="font-medium truncate max-w-[150px]">{{ $r->penanggung_jawab ?: 'Petugas BPS' }}</span>
                </div>

                @if(Auth::user()->isAdmin())
                <div class="flex items-center gap-2">
                    <a href="{{ route('ruangan.edit', $r->id) }}" class="p-1.5 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Edit Ruangan">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                    <form method="POST" action="{{ route('ruangan.destroy', $r->id) }}" class="inline">
                        @csrf
                        <button type="submit" class="p-1.5 text-red-600 hover:bg-red-50 rounded-lg transition"
                            onclick="return confirm('Apakah Anda yakin ingin menghapus ruangan {{ addslashes($r->nama_ruangan) }}?')" title="Hapus Ruangan">
                            <i class="fa-solid fa-trash-can"></i>
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

</div>
@endsection
