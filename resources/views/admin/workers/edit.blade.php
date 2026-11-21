@extends('layouts.admin')

@section('title', 'Edit Data Worker - ' . $worker->fullname)
@section('page_title', 'Edit Data Mitra Worker')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pt-2">

    <!-- BREADCRUMB & BACK -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.workers.index') }}" class="inline-flex items-center space-x-1 text-xs font-bold text-slate-500 hover:text-slate-800 transition">
            <span>&larr; Kembali ke Daftar Worker</span>
        </a>
        <div class="flex items-center space-x-2">
            <span class="text-xs font-mono font-bold text-slate-500 bg-slate-200/80 px-2.5 py-1 rounded-lg">
                ID: #WRK-{{ str_pad($worker->id, 5, '0', STR_PAD_LEFT) }}
            </span>
        </div>
    </div>

    <!-- FORM EDIT WORKER -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8 space-y-6">
        
        <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
            <div>
                <h1 class="text-lg font-black text-slate-900 tracking-tight">Perbarui Informasi Mitra ART</h1>
                <p class="text-xs text-slate-500 mt-0.5">Admin berhak mengubah data, status verifikasi berkas, dan status keaktifan worker.</p>
            </div>
            <span class="text-xs bg-blue-50 text-blue-700 font-bold px-3 py-1 rounded-full">
                Akses Administrator
            </span>
        </div>

        <form action="{{ route('admin.workers.update', $worker->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- SECTION 1: ID WORKER (GREY & DISABLED - TIDAK BISA DIUBAH) -->
            <div class="bg-slate-50 p-4 sm:p-5 rounded-2xl border border-slate-200 space-y-3">
                <div class="flex items-center justify-between">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide">
                        ID Sistem Mitra ART (Permanen)
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
                               value="#WRK-{{ str_pad($worker->id, 5, '0', STR_PAD_LEFT) }}" 
                               readonly 
                               disabled 
                               class="w-full bg-slate-100 border border-slate-300 text-slate-500 font-mono font-bold text-sm rounded-xl px-4 py-3 cursor-not-allowed shadow-inner">
                        <p class="text-[11px] text-slate-400 mt-1 font-medium">
                            🔒 ID Mitra dihasilkan otomatis oleh sistem database dan tidak dapat diubah oleh siapapun.
                        </p>
                    </div>
                    <div>
                        <input type="text" 
                               value="Database Record ID: {{ $worker->id }}" 
                               readonly 
                               disabled 
                               class="w-full bg-slate-100 border border-slate-300 text-slate-400 font-mono text-sm rounded-xl px-4 py-3 cursor-not-allowed shadow-inner">
                        <p class="text-[11px] text-slate-400 mt-1 font-medium">
                            Kunci primer (Primary Key) integritas relasi pesanan & rating.
                        </p>
                    </div>
                </div>
            </div>

            <!-- SECTION 2: STATUS & VERIFIKASI (KONTROL ADMIN) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-2xl border border-blue-100 bg-blue-50/20">
                <!-- Status Worker -->
                <div>
                    <label for="status_worker" class="block text-xs font-bold text-slate-800 uppercase tracking-wide mb-1.5">
                        Status Keaktifan Mitra <span class="text-rose-500">*</span>
                    </label>
                    <select name="status_worker" 
                            id="status_worker" 
                            class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                            required>
                        <option value="AKTIF" {{ old('status_worker', $worker->status_worker) === 'AKTIF' ? 'selected' : '' }}>
                            🟢 AKTIF (Bisa dipesan majikan di katalog)
                        </option>
                        <option value="NONAKTIF" {{ old('status_worker', $worker->status_worker) === 'NONAKTIF' ? 'selected' : '' }}>
                            🔴 NONAKTIF / SUSPENDED (Disembunyikan dari katalog)
                        </option>
                    </select>
                </div>

                <!-- Status Data (Verifikasi) -->
                <div>
                    <label for="status_data" class="block text-xs font-bold text-slate-800 uppercase tracking-wide mb-1.5">
                        Status Verifikasi Berkas <span class="text-rose-500">*</span>
                    </label>
                    <select name="status_data" 
                            id="status_data" 
                            class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                            required>
                        <option value="sudah terverifikasi" {{ in_array(old('status_data', $worker->status_data), ['sudah terverifikasi', 'terverifikasi']) ? 'selected' : '' }}>
                            ✓ Sudah Terverifikasi (Lulus verifikasi identitas)
                        </option>
                        <option value="belum terverifikasi" {{ in_array(old('status_data', $worker->status_data), ['belum terverifikasi', '', null]) ? 'selected' : '' }}>
                            ⏳ Belum Terverifikasi (Menunggu pemeriksaan)
                        </option>
                        <option value="ditolak" {{ old('status_data', $worker->status_data) === 'ditolak' ? 'selected' : '' }}>
                            ✗ Ditolak (Berkas tidak valid / bermasalah)
                        </option>
                    </select>
                </div>
            </div>

            <!-- SECTION 3: BIODATA & KONTAK -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Nama Lengkap -->
                <div>
                    <label for="fullname" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        Nama Lengkap Mitra <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="fullname" 
                           id="fullname" 
                           value="{{ old('fullname', $worker->fullname) }}" 
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
                           value="{{ old('email', $worker->email) }}" 
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
                           value="{{ old('nik', $worker->nik) }}" 
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold font-mono text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                           required>
                </div>

                <!-- Nomor Telepon / WA -->
                <div>
                    <label for="phone_number" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        Nomor Handphone / WhatsApp <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="phone_number" 
                           id="phone_number" 
                           value="{{ old('phone_number', $worker->phone_number) }}" 
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
                        <option value="Female" {{ old('sex', $worker->sex) === 'Female' ? 'selected' : '' }}>Perempuan</option>
                        <option value="Male" {{ old('sex', $worker->sex) === 'Male' ? 'selected' : '' }}>Laki-laki</option>
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
                           value="{{ old('birth_date', $worker->birth_date) }}" 
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                </div>
            </div>

            <!-- SECTION 4: DOMISILI PENUGASAN (CHAINED SELECT DATABASE) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="selectProvinsi" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        Provinsi Penugasan <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <select name="provinsi" 
                                id="selectProvinsi" 
                                onchange="onProvinsiChange(this.value, 'selectKabupaten', '{{ old('kabupaten', $worker->kabupaten) }}')" 
                                class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                                required>
                            <option value="">-- Pilih Provinsi --</option>
                            @foreach($provinces as $p)
                                <option value="{{ $p->name }}" {{ old('provinsi', $worker->provinsi) == $p->name ? 'selected' : '' }}>
                                    {{ $p->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label for="selectKabupaten" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        Kabupaten / Kota Penugasan <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <select name="kabupaten" 
                                id="selectKabupaten" 
                                class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                                required>
                            <option value="{{ $worker->kabupaten }}">{{ $worker->kabupaten ?? '-- Pilih Kabupaten / Kota --' }}</option>
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
                          required>{{ old('alamat_lengkap', $worker->alamat_lengkap) }}</textarea>
            </div>

            <!-- Deskripsi Profil -->
            <div>
                <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                    Deskripsi Diri & Komitmen Kerja
                </label>
                <textarea name="description" 
                          id="description" 
                          rows="3" 
                          class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">{{ old('description', $worker->description) }}</textarea>
            </div>

            <!-- SECTION 5: DOKUMEN & FOTO -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-200">
                
                <!-- Ganti Dokumen SKCK -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1">
                        Dokumen SKCK Kepolisian
                    </label>
                    @if(!empty($worker->skck_path))
                        <div class="mb-2">
                            <a href="{{ asset('storage/' . $worker->skck_path) }}" target="_blank" class="text-xs text-blue-600 hover:underline font-bold inline-flex items-center space-x-1">
                                <span>📄 Lihat SKCK Saat Ini ↗</span>
                            </a>
                        </div>
                    @else
                        <p class="text-[11px] text-amber-700 mb-1.5">Belum ada dokumen SKCK terunggah.</p>
                    @endif
                    <input type="file" 
                           name="skck" 
                           accept=".jpg,.jpeg,.png,.pdf" 
                           class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-slate-300 rounded-xl p-1 bg-white">
                    <p class="text-[10px] text-slate-400 mt-1">Kosongkan jika tidak ingin mengubah berkas SKCK.</p>
                </div>

                <!-- Ganti Foto Profil -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1">
                        Foto Profil Mitra
                    </label>
                    @if(!empty($worker->foto_path))
                        <div class="flex items-center space-x-2 mb-2">
                            <img src="{{ asset('storage/' . $worker->foto_path) }}" alt="{{ $worker->fullname }}" class="w-8 h-8 rounded-lg object-cover border border-slate-200">
                            <span class="text-xs text-slate-500">Foto profil terpasang</span>
                        </div>
                    @endif
                    <input type="file" 
                           name="foto" 
                           accept="image/*" 
                           class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-slate-300 rounded-xl p-1 bg-white">
                    <p class="text-[10px] text-slate-400 mt-1">Kosongkan jika tidak ingin mengubah foto profil.</p>
                </div>
            </div>

            <!-- SECTION 6: RESET PASSWORD (OPSIONAL) -->
            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                    Reset Password Worker (Opsional)
                </label>
                <input type="password" 
                       name="password" 
                       id="password" 
                       placeholder="Isi hanya jika ingin mengganti password akun worker ini (Min. 6 Karakter)" 
                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
            </div>

            <!-- SUBMIT BUTTONS -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('admin.workers.index') }}" class="px-5 py-3 rounded-xl border border-slate-300 text-slate-600 font-bold text-xs hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-extrabold text-xs transition shadow-md shadow-blue-600/25">
                    Simpan Perubahan Data Worker &rarr;
                </button>
            </div>

        </form>

    </div>

</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const provinsiCurrent = document.getElementById('selectProvinsi').value;
        const kabupatenCurrent = "{{ old('kabupaten', $worker->kabupaten) }}";
        if (provinsiCurrent) {
            onProvinsiChange(provinsiCurrent, 'selectKabupaten', kabupatenCurrent);
        }
    });
</script>
@endpush
@endsection

