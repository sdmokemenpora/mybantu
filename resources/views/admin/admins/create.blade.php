@extends('layouts.admin')

@section('title', 'Register Administrator Baru')
@section('page_title', 'Pendaftaran Akun Admin Baru')

@section('content')
<div class="max-w-2xl mx-auto space-y-6 pt-2">

    <!-- BREADCRUMB & BACK -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.admins.index') }}" class="inline-flex items-center space-x-1 text-xs font-bold text-slate-500 hover:text-slate-800 transition">
            <span>&larr; Kembali ke Daftar Admin</span>
        </a>
    </div>

    <!-- FORM REGISTER ADMIN -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8 space-y-6">
        
        <div class="border-b border-slate-100 pb-4">
            <h1 class="text-lg font-black text-slate-900 tracking-tight">Formulir Pendaftaran Administrator Baru</h1>
            <p class="text-xs text-slate-500 mt-0.5">Admin baru akan memiliki hak akses penuh atau terbatas sesuai penugasan peran.</p>
        </div>

        <form action="{{ route('admin.admins.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Nama Admin -->
            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                    Nama Lengkap Administrator <span class="text-rose-500">*</span>
                </label>
                <input type="text" 
                       name="name" 
                       id="name" 
                       value="{{ old('name') }}" 
                       placeholder="Contoh: Budi Santoso" 
                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                       required>
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                    Alamat Email Login <span class="text-rose-500">*</span>
                </label>
                <input type="email" 
                       name="email" 
                       id="email" 
                       value="{{ old('email') }}" 
                       placeholder="admin.nama@mybantu.id" 
                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                       required>
            </div>

            <!-- Nomor Telepon -->
            <div>
                <label for="phone_number" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                    Nomor WhatsApp / HP
                </label>
                <input type="text" 
                       name="phone_number" 
                       id="phone_number" 
                       value="{{ old('phone_number') }}" 
                       placeholder="081234567890" 
                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
            </div>

            <!-- Role & Status -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="role" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        Peran / Hak Akses <span class="text-rose-500">*</span>
                    </label>
                    <select name="role" id="role" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" required>
                        <option value="Super Admin" {{ old('role') === 'Super Admin' ? 'selected' : '' }}>Super Admin (Akses Penuh)</option>
                        <option value="Admin Verifikator" {{ old('role') === 'Admin Verifikator' ? 'selected' : '' }}>Admin Verifikator (Verifikasi Berkas)</option>
                        <option value="Admin Operasional" {{ old('role') === 'Admin Operasional' ? 'selected' : '' }}>Admin Operasional (Manajemen Data)</option>
                    </select>
                </div>

                <div>
                    <label for="status" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        Status Akun <span class="text-rose-500">*</span>
                    </label>
                    <select name="status" id="status" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" required>
                        <option value="AKTIF" {{ old('status', 'AKTIF') === 'AKTIF' ? 'selected' : '' }}>🟢 AKTIF</option>
                        <option value="NONAKTIF" {{ old('status') === 'NONAKTIF' ? 'selected' : '' }}>🔴 NONAKTIF</option>
                    </select>
                </div>
            </div>

            <!-- Password & Confirmation -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        Password (Min. 6 Karakter) <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" 
                           name="password" 
                           id="password" 
                           placeholder="Minimal 6 karakter" 
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                           required>
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        Konfirmasi Password <span class="text-rose-500">*</span>
                    </label>
                    <input type="password" 
                           name="password_confirmation" 
                           id="password_confirmation" 
                           placeholder="Ulangi password" 
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                           required>
                </div>
            </div>

            <!-- SUBMIT BUTTONS -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('admin.admins.index') }}" class="px-5 py-3 rounded-xl border border-slate-300 text-slate-600 font-bold text-xs hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs transition shadow-md shadow-blue-600/25">
                    Daftarkan Administrator Baru &rarr;
                </button>
            </div>

        </form>

    </div>

</div>
@endsection

