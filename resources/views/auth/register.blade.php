@extends('layouts.app')

@section('content')
<div class="py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-xl mx-auto bg-white p-8 sm:p-10 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200/80 space-y-6">
        
        <!-- HEADER -->
        <div class="text-center space-y-2">
            <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-extrabold text-xl mx-auto shadow-md shadow-blue-500/20">
                B
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Pendaftaran Akun Majikan</h1>
            <p class="text-xs text-slate-500">Buat akun untuk memesan asisten rumah tangga bergaransi dengan mudah</p>
        </div>

        <!-- ERROR ALERT -->
        @if (isset($errors) && $errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-2xl text-xs font-medium space-y-1">
                <p class="font-bold">Mohon perbaiki data berikut:</p>
                <ul class="list-disc pl-5 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register') }}" method="POST" class="space-y-4">
            @csrf
            
            <!-- Fullname -->
            <div>
                <label for="fullname" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                    Nama Lengkap (Sesuai KTP)
                </label>
                <input type="text" 
                       name="fullname" 
                       id="fullname" 
                       value="{{ old('fullname') }}" 
                       placeholder="Contoh: Budi Santoso" 
                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                       required>
            </div>

            <!-- Email & Password -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        Alamat Email
                    </label>
                    <input type="email" 
                           name="email" 
                           id="email" 
                           value="{{ old('email') }}" 
                           placeholder="nama@email.com" 
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                           required>
                </div>
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        Kata Sandi (Min. 6 Karakter)
                    </label>
                    <input type="password" 
                           name="password" 
                           id="password" 
                           placeholder="Minimal 6 karakter" 
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                           required>
                </div>
            </div>

            <!-- NIK & Nomor Telepon -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="nik" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        NIK (16 Digit)
                    </label>
                    <input type="text" 
                           name="nik" 
                           id="nik" 
                           maxlength="16" 
                           value="{{ old('nik') }}" 
                           placeholder="3201xxxxxxxxxxxx" 
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                           required>
                </div>
                <div>
                    <label for="phone_number" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        Nomor WhatsApp / HP
                    </label>
                    <input type="tel" 
                           name="phone_number" 
                           id="phone_number" 
                           value="{{ old('phone_number') }}" 
                           placeholder="081234567890" 
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                           required>
                </div>
            </div>

            <!-- Jenis Kelamin & Tanggal Lahir -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-2">
                        Jenis Kelamin
                    </label>
                    <div class="flex items-center space-x-6 pt-1">
                        <label class="inline-flex items-center text-sm font-medium text-slate-700 cursor-pointer">
                            <input type="radio" 
                                   name="sex" 
                                   value="Female" 
                                   class="text-blue-600 focus:ring-blue-500 mr-2" 
                                   {{ old('sex') == 'Female' ? 'checked' : '' }} 
                                   required> 
                            Perempuan
                        </label>
                        <label class="inline-flex items-center text-sm font-medium text-slate-700 cursor-pointer">
                            <input type="radio" 
                                   name="sex" 
                                   value="Male" 
                                   class="text-blue-600 focus:ring-blue-500 mr-2" 
                                   {{ old('sex') == 'Male' ? 'checked' : '' }} 
                                   required> 
                            Laki-laki
                        </label>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        Tanggal Lahir
                    </label>
                    <div class="grid grid-cols-3 gap-2">
                        <input type="text" 
                               name="birth_day" 
                               value="{{ old('birth_day') }}" 
                               placeholder="DD" 
                               maxlength="2" 
                               class="bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm text-center font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                        <input type="text" 
                               name="birth_month" 
                               value="{{ old('birth_month') }}" 
                               placeholder="MM" 
                               maxlength="2" 
                               class="bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm text-center font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                        <input type="text" 
                               name="birth_year" 
                               value="{{ old('birth_year') }}" 
                               placeholder="YYYY" 
                               maxlength="4" 
                               class="bg-slate-50 border border-slate-300 rounded-xl p-2.5 text-sm text-center font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                    </div>
                </div>
            </div>

            <!-- Provinsi & Kabupaten (Chained Select dari Database) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="selectProvinsi" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        Provinsi Domisili <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <select name="provinsi" 
                                id="selectProvinsi" 
                                onchange="onProvinsiChange(this.value, 'selectKabupaten')" 
                                class="w-full appearance-none bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                                required>
                            <option value="">-- Pilih Provinsi --</option>
                            @foreach($provinces ?? [] as $p)
                                <option value="{{ $p->name }}" {{ old('provinsi') == $p->name ? 'selected' : '' }}>
                                    {{ $p->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="selectKabupaten" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        Kabupaten / Kota <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <select name="kabupaten" 
                                id="selectKabupaten" 
                                class="w-full appearance-none bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                                required>
                            <option value="">-- Pilih Kabupaten / Kota --</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-500">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Alamat Lengkap -->
            <div>
                <label for="alamat_lengkap" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                    Alamat Lengkap Hunian
                </label>
                <textarea name="alamat_lengkap" 
                          id="alamat_lengkap" 
                          rows="3" 
                          placeholder="Jalan, Nomor Rumah, RT/RW, Kelurahan, Kecamatan, Patokan Alamat" 
                          class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                          required>{{ old('alamat_lengkap') }}</textarea>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button type="submit" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-extrabold py-3.5 px-4 rounded-xl text-sm transition duration-150 shadow-md shadow-blue-600/25 flex items-center justify-center space-x-2">
                    <span>Daftar Akun Majikan &rarr;</span>
                </button>
            </div>
        </form>

        <!-- FOOTER LINKS -->
        <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-2">
            <span>
                Sudah memiliki akun? 
                <a href="{{ route('login', ['role' => 'user']) }}" class="font-bold text-blue-600 hover:text-blue-800 transition">
                    Masuk Akun Majikan
                </a>
            </span>
            <span>
                Ingin mencari kerja? 
                <a href="{{ route('register.seller') }}" class="font-bold text-emerald-600 hover:text-emerald-800 transition">
                    Gabung Mitra ART &rarr;
                </a>
            </span>
        </div>

    </div>
</div>

<script>
    const wilayahData = @json(isset($provinces) ? $provinces->mapWithKeys(fn($p) => [$p->name => $p->regencies->pluck('name')]) : []);

    function onProvinsiChange(provinsiName, kabupatenElementId, selectedKabupaten = null) {
        const kabSelect = document.getElementById(kabupatenElementId);
        kabSelect.innerHTML = '<option value="">-- Pilih Kabupaten / Kota --</option>';

        if (!provinsiName || !wilayahData[provinsiName]) {
            return;
        }

        wilayahData[provinsiName].forEach(kab => {
            const opt = document.createElement('option');
            opt.value = kab;
            opt.textContent = kab;
            if (selectedKabupaten && selectedKabupaten === kab) {
                opt.selected = true;
            }
            kabSelect.appendChild(opt);
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const initialProv = document.getElementById('selectProvinsi').value;
        const oldKab = @json(old('kabupaten'));
        if (initialProv) {
            onProvinsiChange(initialProv, 'selectKabupaten', oldKab);
        }
    });
</script>
@endsection
