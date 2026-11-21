@extends('layouts.admin')

@section('title', 'Data User Majikan')
@section('page_title', 'Manajemen Data Pengguna (Majikan)')

@section('content')
<div class="space-y-6 pt-2">

    <!-- HEADER & STATS -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-black text-slate-900 tracking-tight">Daftar Pengguna / Majikan</h1>
            <p class="text-xs text-slate-500">Kelola akun majikan terdaftar, pantau pesanan yang dilakukan, dan perbarui data akun.</p>
        </div>

        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-xs font-bold">
                <span>👥 {{ number_format($totalUsers) }} Majikan Terdaftar</span>
            </span>
        </div>
    </div>

    <!-- FILTER & SEARCH PANEL -->
    <div class="bg-white rounded-3xl p-5 border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Cari Majikan</label>
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

            <!-- Provinsi -->
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Provinsi</label>
                <select name="provinsi" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs font-semibold text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                    <option value="">Semua Provinsi</option>
                    @foreach($provinces as $p)
                        <option value="{{ $p->name }}" {{ request('provinsi') == $p->name ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-end space-x-2">
                <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-3 rounded-xl text-xs transition shadow-xs">
                    Terapkan
                </button>
                <a href="{{ route('admin.users.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-2 px-3 rounded-xl text-xs transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- TABLE LIST USERS -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-500 uppercase tracking-wider border-b border-slate-200 text-[10px]">
                        <th class="py-3.5 px-4 font-bold">ID User</th>
                        <th class="py-3.5 px-4 font-bold">Majikan</th>
                        <th class="py-3.5 px-4 font-bold">Kontak & NIK</th>
                        <th class="py-3.5 px-4 font-bold">Domisili</th>
                        <th class="py-3.5 px-4 font-bold">Aktivitas Pesanan</th>
                        <th class="py-3.5 px-4 font-bold">Terdaftar Sejak</th>
                        <th class="py-3.5 px-4 font-bold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $u)
                        <tr class="hover:bg-slate-50/60 transition">
                            
                            <!-- ID User (Grey Mono Badge) -->
                            <td class="py-3.5 px-4">
                                <span class="font-mono font-bold text-xs text-slate-500 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-md">
                                    #USR-{{ str_pad($u->id, 5, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>

                            <!-- Foto & Nama -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center space-x-3">
                                    @if(!empty($u->foto_path))
                                        <img src="{{ asset('storage/' . $u->foto_path) }}" alt="{{ $u->name }}" class="w-10 h-10 rounded-xl object-cover border border-slate-200 flex-shrink-0">
                                    @else
                                        <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-800 font-extrabold text-sm flex items-center justify-center flex-shrink-0">
                                            {{ strtoupper(substr($u->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    <div class="min-w-0">
                                        <a href="{{ route('admin.users.show', $u->id) }}" class="font-extrabold text-slate-900 hover:text-blue-600 block truncate">
                                            {{ $u->name }}
                                        </a>
                                        <span class="text-[10px] text-slate-400 font-medium">
                                            {{ $u->sex === 'Female' ? 'Perempuan' : ($u->sex === 'Male' ? 'Laki-laki' : 'Pelanggan') }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <!-- Kontak & NIK -->
                            <td class="py-3.5 px-4 space-y-0.5">
                                <p class="text-slate-800 font-semibold">{{ $u->phone_number }}</p>
                                <p class="text-slate-500 text-[11px] truncate">{{ $u->email }}</p>
                                <p class="text-slate-400 font-mono text-[10px]">NIK: {{ $u->nik ?? '-' }}</p>
                            </td>

                            <!-- Domisili -->
                            <td class="py-3.5 px-4">
                                <p class="font-bold text-slate-800">{{ $u->kabupaten ?? '-' }}</p>
                                <p class="text-slate-400 text-[11px]">{{ $u->provinsi ?? '-' }}</p>
                            </td>

                            <!-- Total Pesanan -->
                            <td class="py-3.5 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-extrabold bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ $u->total_orders ?? 0 }} Total Order
                                </span>
                                @if(($u->completed_orders ?? 0) > 0)
                                    <span class="block text-[10px] text-emerald-600 font-semibold mt-1">
                                        ✓ {{ $u->completed_orders }} Selesai
                                    </span>
                                @endif
                            </td>

                            <!-- Terdaftar -->
                            <td class="py-3.5 px-4 text-slate-500">
                                {{ $u->created_at ? $u->created_at->format('d M Y') : '-' }}
                            </td>

                            <!-- Aksi -->
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <!-- Detail -->
                                    <a href="{{ route('admin.users.show', $u->id) }}" 
                                       title="Lihat Detail Majikan"
                                       class="p-1.5 rounded-lg text-slate-500 hover:text-blue-600 hover:bg-blue-50 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>

                                    <!-- Edit -->
                                    <a href="{{ route('admin.users.edit', $u->id) }}" 
                                       title="Edit Data User"
                                       class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>

                                    <!-- Delete -->
                                    <form action="{{ route('admin.users.destroy', $u->id) }}" method="POST" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin menghapus akun majikan {{ $u->name }} (ID: {{ $u->id }})? Semua data pesanan majikan ini akan ikut terhapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                title="Hapus User Secara Permanen"
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
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                Tidak ada data pengguna majikan yang cocok dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

