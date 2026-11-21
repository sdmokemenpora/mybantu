@extends('layouts.admin')

@section('title', 'Data Mitra ART (Worker)')
@section('page_title', 'Manajemen Data Mitra Worker (ART)')

@section('content')
<div class="space-y-6 pt-2">

    <!-- HEADER & SUMMARY BADGES -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-black text-slate-900 tracking-tight">Daftar Mitra ART (Workers)</h1>
            <p class="text-xs text-slate-500">Kelola informasi mitra ART, ubah status keaktifan, verifikasi berkas, dan perbarui data mitra.</p>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.workers.verify') }}" class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs transition shadow-xs">
                <span>🛡️ Antrean Verifikasi ({{ $pendingCount }})</span>
            </a>
        </div>
    </div>

    <!-- STATS CHIPS -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Mitra</span>
            <p class="text-xl font-black text-slate-900 mt-0.5">{{ number_format($totalCount) }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
            <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-wider">Mitra Aktif</span>
            <p class="text-xl font-black text-emerald-700 mt-0.5">{{ number_format($activeCount) }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
            <span class="text-[10px] font-bold text-blue-600 uppercase tracking-wider">Terverifikasi</span>
            <p class="text-xl font-black text-blue-700 mt-0.5">{{ number_format($verifiedCount) }}</p>
        </div>
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-2xs">
            <span class="text-[10px] font-bold text-amber-600 uppercase tracking-wider">Butuh Verifikasi</span>
            <p class="text-xl font-black text-amber-700 mt-0.5">{{ number_format($pendingCount) }}</p>
        </div>
    </div>

    <!-- FILTER & SEARCH PANEL -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('admin.workers.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Cari Mitra</label>
                <div class="relative">
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Nama, email, telepon, NIK, atau ID..." 
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Status Data -->
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Status Verifikasi</label>
                <select name="status_data" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                    <option value="">Semua Status Data</option>
                    <option value="belum terverifikasi" {{ request('status_data') === 'belum terverifikasi' ? 'selected' : '' }}>Belum Terverifikasi</option>
                    <option value="sudah terverifikasi" {{ in_array(request('status_data'), ['sudah terverifikasi', 'terverifikasi']) ? 'selected' : '' }}>Sudah Terverifikasi</option>
                    <option value="ditolak" {{ request('status_data') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
            </div>

            <!-- Status Worker -->
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Status Keaktifan</label>
                <select name="status_worker" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                    <option value="">Semua Keaktifan</option>
                    <option value="AKTIF" {{ request('status_worker') === 'AKTIF' ? 'selected' : '' }}>AKTIF</option>
                    <option value="NONAKTIF" {{ request('status_worker') === 'NONAKTIF' ? 'selected' : '' }}>NONAKTIF</option>
                </select>
            </div>

            <!-- Actions -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-3 rounded-xl text-xs transition shadow-xs flex items-center justify-center space-x-1">
                    <span>Terapkan</span>
                </button>
                <a href="{{ route('admin.workers.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-2 px-3 rounded-xl text-xs transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- TABLE LIST WORKER -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-500 uppercase tracking-wider border-b border-slate-200 text-[10px]">
                        <th class="py-3.5 px-4 font-bold">ID Worker</th>
                        <th class="py-3.5 px-4 font-bold">Profil Mitra</th>
                        <th class="py-3.5 px-4 font-bold">Kontak & NIK</th>
                        <th class="py-3.5 px-4 font-bold">Domisili</th>
                        <th class="py-3.5 px-4 font-bold">Verifikasi Data</th>
                        <th class="py-3.5 px-4 font-bold">Status Worker</th>
                        <th class="py-3.5 px-4 font-bold">Rating</th>
                        <th class="py-3.5 px-4 font-bold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($workers as $w)
                        <tr class="hover:bg-slate-50/60 transition">
                            
                            <!-- ID Worker (Grey Mono Badge) -->
                            <td class="py-3.5 px-4">
                                <span class="font-mono font-bold text-xs text-slate-500 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-md">
                                    #WRK-{{ str_pad($w->id, 5, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>

                            <!-- Foto & Nama -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center space-x-3">
                                    @if(!empty($w->foto_path))
                                        <img src="{{ asset('storage/' . $w->foto_path) }}" alt="{{ $w->fullname }}" class="w-10 h-10 rounded-xl object-cover border border-slate-200 flex-shrink-0">
                                    @else
                                        <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-800 font-extrabold text-sm flex items-center justify-center flex-shrink-0">
                                            {{ strtoupper(substr($w->fullname, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <a href="{{ route('admin.workers.show', $w->id) }}" class="font-extrabold text-slate-900 hover:text-blue-600 block truncate">
                                            {{ $w->fullname }}
                                        </a>
                                        <span class="text-[10px] text-slate-400 font-medium">
                                            {{ $w->sex === 'Female' ? 'Perempuan' : ($w->sex === 'Male' ? 'Laki-laki' : '-') }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Kontak & NIK -->
                            <td class="py-3.5 px-4 space-y-0.5">
                                <p class="text-slate-800 font-semibold">{{ $w->phone_number }}</p>
                                <p class="text-slate-500 text-[11px] truncate">{{ $w->email }}</p>
                                <p class="text-slate-400 font-mono text-[10px]">NIK: {{ $w->nik }}</p>
                            </td>

                            <!-- Domisili -->
                            <td class="py-3.5 px-4">
                                <p class="font-bold text-slate-800">{{ $w->kabupaten ?? '-' }}</p>
                                <p class="text-slate-400 text-[11px]">{{ $w->provinsi ?? '-' }}</p>
                            </td>

                            <!-- Verifikasi Data (Badge) -->
                            <td class="py-3.5 px-4">
                                @if(in_array(strtolower($w->status_data ?? ''), ['sudah terverifikasi', 'terverifikasi']))
                                    <span class="inline-flex items-center text-[10px] font-extrabold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-0.5 rounded-full">
                                        ✓ Sudah Terverifikasi
                                    </span>
                                @elseif(strtolower($w->status_data ?? '') === 'ditolak')
                                    <span class="inline-flex items-center text-[10px] font-extrabold text-rose-700 bg-rose-50 border border-rose-200 px-2.5 py-0.5 rounded-full">
                                        ✗ Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center text-[10px] font-extrabold text-amber-700 bg-amber-50 border border-amber-200 px-2.5 py-0.5 rounded-full">
                                        ⏳ Belum Terverifikasi
                                    </span>
                                @endif

                                @if(!empty($w->skck_path))
                                    <span class="block text-[10px] text-emerald-600 font-semibold mt-1">📄 Ada SKCK</span>
                                @endif
                            </td>

                            <!-- Status Worker (Toggle Form) -->
                            <td class="py-3.5 px-4">
                                <form action="{{ route('admin.workers.updateStatus', $w->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status_worker" value="{{ strtoupper($w->status_worker) === 'AKTIF' ? 'NONAKTIF' : 'AKTIF' }}">
                                    <button type="submit" 
                                            title="Klik untuk ubah status keaktifan"
                                            class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold border transition cursor-pointer {{ strtoupper($w->status_worker) === 'AKTIF' ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 border-slate-300 hover:bg-slate-200' }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ strtoupper($w->status_worker) === 'AKTIF' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                                        <span>{{ strtoupper($w->status_worker) }}</span>
                                    </button>
                                </form>
                            </td>

                            <!-- Rating -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center space-x-1 text-amber-500 font-extrabold">
                                    <span>★</span>
                                    <span>{{ number_format((float)($w->ratings_avg_rating ?? 0), 1) }}</span>
                                </div>
                                <span class="text-[10px] text-slate-400">({{ $w->ratings_count ?? 0 }} review)</span>
                            </td>

                            <!-- Actions -->
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <!-- Detail -->
                                    <a href="{{ route('admin.workers.show', $w->id) }}" 
                                       title="Lihat Detail Profil"
                                       class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>

                                    <!-- Edit -->
                                    <a href="{{ route('admin.workers.edit', $w->id) }}" 
                                       title="Edit Data Worker"
                                       class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>

                                    <!-- Delete -->
                                    <form action="{{ route('admin.workers.destroy', $w->id) }}" method="POST" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin menghapus mitra ART {{ $w->fullname }} (ID: {{ $w->id }})? Tindakan ini tidak dapat dibatalkan.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                title="Hapus Worker Secara Permanen"
                                                class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                Tidak ada data mitra ART yang cocok dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($workers->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $workers->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

