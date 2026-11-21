@extends('layouts.admin')

@section('title', 'Manajemen Administrator')
@section('page_title', 'Kelola Tim Administrator')

@section('content')
<div class="space-y-6 pt-2">

    <!-- HEADER & BUTTON REGISTER ADMIN -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-black text-slate-900 tracking-tight">Daftar Administrator MyBantu</h1>
            <p class="text-xs text-slate-500">Daftar pengguna dengan hak akses operasional dan verifikasi platform.</p>
        </div>

        <a href="{{ route('admin.admins.create') }}" class="inline-flex items-center space-x-2 px-4 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs transition shadow-md shadow-blue-600/20">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            <span>Register Admin Baru</span>
        </a>
    </div>

    <!-- TABLE LIST ADMIN -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-500 uppercase tracking-wider border-b border-slate-200 text-[10px]">
                        <th class="py-3.5 px-4 font-bold">ID Admin</th>
                        <th class="py-3.5 px-4 font-bold">Nama Admin</th>
                        <th class="py-3.5 px-4 font-bold">Email</th>
                        <th class="py-3.5 px-4 font-bold">No. Telepon</th>
                        <th class="py-3.5 px-4 font-bold">Peran / Role</th>
                        <th class="py-3.5 px-4 font-bold">Status</th>
                        <th class="py-3.5 px-4 font-bold">Terdaftar Sejak</th>
                        <th class="py-3.5 px-4 font-bold text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($admins as $adm)
                        <tr class="hover:bg-slate-50/60 transition">
                            
                            <!-- ID Admin (Grey Mono) -->
                            <td class="py-3.5 px-4">
                                <span class="font-mono font-bold text-xs text-slate-500 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-md">
                                    #ADM-{{ str_pad($adm->id, 4, '0', STR_PAD_LEFT) }}
                                </span>
                            </td>

                            <!-- Nama & Avatar -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-9 h-9 rounded-xl bg-slate-800 text-white font-black text-xs flex items-center justify-center flex-shrink-0">
                                        {{ strtoupper(substr($adm->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-extrabold text-slate-900 truncate">
                                            {{ $adm->name }}
                                            @if(Auth::guard('admin')->id() === $adm->id)
                                                <span class="text-[10px] font-bold text-blue-600 bg-blue-50 px-1.5 py-0.2 rounded border border-blue-200 ml-1">
                                                    (Anda)
                                                </span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <!-- Email -->
                            <td class="py-3.5 px-4 font-medium text-slate-700">
                                {{ $adm->email }}
                            </td>

                            <!-- Telepon -->
                            <td class="py-3.5 px-4 text-slate-600">
                                {{ $adm->phone_number ?? '-' }}
                            </td>

                            <!-- Role -->
                            <td class="py-3.5 px-4">
                                @if(strtolower($adm->role) === 'super admin')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        Super Admin
                                    </span>
                                @elseif(strtolower($adm->role) === 'admin verifikator')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        Admin Verifikator
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                        {{ $adm->role }}
                                    </span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="py-3.5 px-4">
                                @if(strtoupper($adm->status) === 'AKTIF')
                                    <span class="inline-flex items-center space-x-1 text-[10px] font-extrabold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        <span>AKTIF</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center space-x-1 text-[10px] font-extrabold text-slate-500 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-full">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        <span>NONAKTIF</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Tanggal -->
                            <td class="py-3.5 px-4 text-slate-400 text-[11px]">
                                {{ $adm->created_at ? $adm->created_at->format('d M Y') : '-' }}
                            </td>

                            <!-- Aksi -->
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <!-- Edit -->
                                    <a href="{{ route('admin.admins.edit', $adm->id) }}" 
                                       title="Edit Data Admin"
                                       class="p-1.5 rounded-lg text-slate-500 hover:text-amber-600 hover:bg-amber-50 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>

                                    <!-- Delete (Kecuali Akun Sendiri) -->
                                    @if(Auth::guard('admin')->id() !== $adm->id)
                                        <form action="{{ route('admin.admins.destroy', $adm->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun admin {{ $adm->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    title="Hapus Akun Admin"
                                                    class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">
                                Tidak ada data administrator.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

