@extends('layouts.admin')

@section('title', 'Edit Data Majikan - ' . $user->name)
@section('page_title', 'Edit Data Pengguna Majikan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pt-2">

    <!-- BREADCRUMB & BACK -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center space-x-1 text-xs font-bold text-slate-500 hover:text-slate-800 transition">
            <span>&larr; Kembali ke Daftar Majikan</span>
        </a>
        <div class="flex items-center space-x-2">
            <span class="text-xs font-mono font-bold text-slate-500 bg-slate-200/80 px-2.5 py-1 rounded-lg">
                ID: #USR-{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}
            </span>
        </div>
    </div>

    <!-- FORM EDIT USER -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8 space-y-6">
        
        <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
            <div>
                <h1 class="text-lg font-black text-slate-900 tracking-tight">Perbarui Informasi Majikan</h1>
                <p class="text-xs text-slate-500 mt-0.5">Admin dapat memperbarui data akun majikan, kontak, dan alamat hunian.</p>
            </div>
            <span class="text-xs bg-blue-50 text-blue-700 font-bold px-3 py-1 rounded-full">
                Akses Administrator
            </span>
        </div>

        <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- SECTION 1: ID USER (GREY & DISABLED - TIDAK BISA DIUBAH) -->
            <div class="bg-slate-50 p-4 sm:p-5 rounded-2xl border border-slate-200 space-y-3">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide">
                        ID Sistem Pengguna Majikan (Permanen)
                    </label>
                    <span class="inline-flex items-center space-x-1 text-[10px] font-bold text-slate-500 bg-slate-200 px-2 py-0.5 rounded">
                        <svg class="w-3 h-3 text-slate-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span>Terkunci / Readonly</span>
                    </span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <input type="text" 
                               value="#USR-{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}" 
                               readonly 
                               disabled 
                               class="w-full bg-slate-100 border border-slate-300 text-slate-500 font-mono font-bold text-sm rounded-xl px-4 py-3 cursor-not-allowed shadow-inner">
                        <p class="text-[11px] text-slate-400 mt-1 font-medium">
                            🔒 ID Majikan bersifat unik dan permanen sehingga tidak dapat dimanipulasi atau diubah.
                        </p>
                    </div>
                    <div>
                        <input type="text" 
                               value="Database Record ID: {{ $user->id }}" 
                               readonly 
                               disabled 
                               class="w-full bg-slate-100 border border-slate-300 text-slate-400 font-mono text-sm rounded-xl px-4 py-3 cursor-not-allowed shadow-inner">
                        <p class="text-[11px] text-slate-400 mt-1 font-medium">
                            Kunci primer (Primary Key) untuk seluruh transaksi pesanan majikan.
                        </p>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: IDENTITAS & KONTAK -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Nama Lengkap -->
                <div>
                    <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        Nama Lengkap Majikan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="name" 
                           id="name" 
                           value="{{ old('name', $user->name) }}" 
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                           required>
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        Alamat Email <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" 
                           name="email" 
                           id="email" 
                           value="{{ old('email', $user->email) }}" 
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                           required>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- NIK KTP -->
                <div>
                    <label for="nik" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        NIK KTP (16 Digit) <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="nik" 
                           id="nik" 
                           maxlength="16" 
                           value="{{ old('nik', $user->nik) }}" 
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                           required>
                </div>

                <!-- Nomor Telepon -->
                <div>
                    <label for="phone_number" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        Nomor Handphone / WhatsApp <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="phone_number" 
                           id="phone_number" 
                           value="{{ old('phone_number', $user->phone_number) }}" 
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                           required>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Jenis Kelamin -->
                <div>
                    <label for="sex" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        Jenis Kelamin
                    </label>
                    <select name="sex" id="sex" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        <option value="Female" {{ old('sex', $user->sex) === 'Female' ? 'selected' : '' }}>Perempuan</option>
                        <option value="Male" {{ old('sex', $user->sex) === 'Male' ? 'selected' : '' }}>Laki-laki</option>
                    </select>
                </div>

                <!-- Tanggal Lahir -->
                <div>
                    <label for="birth_date" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        Tanggal Lahir
                    </label>
                    <input type="date" 
                           name="birth_date" 
                           id="birth_date" 
                           value="{{ old('birth_date', $user->birth_date) }}" 
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                </div>
            </div>

            <!-- SECTION 3: DOMISILI HUNIAN (CHAINED SELECT DATABASE) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="selectProvinsi" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        Provinsi Domisili <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <select name="provinsi" 
                                id="selectProvinsi" 
                                onchange="onProvinsiChange(this.value, 'selectKabupaten', '{{ old('kabupaten', $user->kabupaten) }}')" 
                                class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                                required>
                            <option value="">-- Pilih Provinsi --</option>
                            @foreach($provinces as $p)
                                <option value="{{ $p->name }}" {{ old('provinsi', $user->provinsi) == $p->name ? 'selected' : '' }}>
                                    {{ $p->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label for="selectKabupaten" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        Kabupaten / Kota Domisili <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <select name="kabupaten" 
                                id="selectKabupaten" 
                                class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                                required>
                            <option value="{{ $user->kabupaten }}">{{ $user->kabupaten ?? '-- Pilih Kabupaten / Kota --' }}</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Alamat Lengkap -->
            <div>
                <label for="alamat_lengkap" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                    Alamat Lengkap Hunian <span class="text-rose-500">*</span>
                </label>
                <textarea name="alamat_lengkap" 
                          id="alamat_lengkap" 
                          rows="3" 
                          class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                          required>{{ old('alamat_lengkap', $user->alamat_lengkap) }}</textarea>
            </div>

            <!-- SECTION 4: RESET PASSWORD (OPSIONAL) -->
            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                    Reset Password Majikan (Opsional)
                </label>
                <input type="password" 
                       name="password" 
                       id="password" 
                       placeholder="Isi hanya jika ingin mereset password akun majikan ini (Min. 6 Karakter)" 
                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
            </div>

            <!-- SUBMIT BUTTONS -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('admin.users.index') }}" class="px-5 py-3 rounded-xl border border-slate-300 text-slate-600 font-bold text-xs hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs transition shadow-md shadow-blue-600/25">
                    Simpan Perubahan Data Majikan &rarr;
                </button>
            </div>

        </form>

    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const provinsiCurrent = document.getElementById('selectProvinsi').value;
        const kabupatenCurrent = "{{ old('kabupaten', $user->kabupaten) }}";
        if (provinsiCurrent) {
            onProvinsiChange(provinsiCurrent, 'selectKabupaten', kabupatenCurrent);
        }
    });
</script>
@endpush
@endsection

