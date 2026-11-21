@extends('layouts.app')

@section('content')
<div class="py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto bg-white p-8 sm:p-12 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200/80 space-y-8">
        
        <!-- HEADER -->
        <div class="text-center space-y-2">
            <div class="inline-flex items-center space-x-2 bg-emerald-50 text-emerald-700 text-xs font-bold px-3.5 py-1.5 rounded-full mb-1">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Peluang Kerja Resmi & Penghasilan Pasti</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">Pendaftaran Mitra Asisten Rumah Tangga</h1>
            <p class="text-xs sm:text-sm text-slate-500 max-w-md mx-auto">
                Bergabung dengan ribuan asisten rumah tangga terpercaya di platform MyBantu dengan sistem pembayaran aman tanpa potongan.
            </p>
        </div>

        <!-- ERROR ALERT -->
        @if (isset($errors) && $errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-2xl text-xs font-medium space-y-1">
                <p class="font-bold">Mohon perbaiki data pendaftaran berikut:</p>
                <ul class="list-disc pl-5 space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('register.seller') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <!-- SECTION 1: INFORMASI AKUN -->
            <div class="space-y-4">
                <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider pb-2 border-b border-slate-100 flex items-center space-x-2">
                    <span class="w-5 h-5 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-[10px]">1</span>
                    <span>Informasi Akun & Kontak</span>
                </h3>

                <!-- Fullname -->
                <div>
                    <label for="fullname" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        Nama Lengkap (Sesuai KTP)
                    </label>
                    <input type="text" 
                           name="fullname" 
                           id="fullname" 
                           value="{{ old('fullname') }}" 
                           placeholder="Contoh: Siti Rahayu" 
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
                            Kata Sandi Akun
                        </label>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               placeholder="Minimal 6 karakter" 
                               class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                               required>
                    </div>
                </div>

                <!-- NIK & Nomor WhatsApp -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="nik" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                            Nomor Induk Kependudukan (NIK)
                        </label>
                        <input type="text" 
                               name="nik" 
                               id="nik" 
                               maxlength="16" 
                               value="{{ old('nik') }}" 
                               placeholder="16 digit nomor KTP" 
                               class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                               required>
                    </div>
                    <div>
                        <label for="phone_number" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                            Nomor WhatsApp Aktif
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
                                       {{ old('sex', 'Female') == 'Female' ? 'checked' : '' }} 
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
            </div>

            <!-- SECTION 2: DOKUMEN & FOTO PROFIL -->
            <div class="space-y-4 pt-2">
                <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider pb-2 border-b border-slate-100 flex items-center space-x-2">
                    <span class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px]">2</span>
                    <span>Upload Foto Profil Resmi</span>
                </h3>

                <!-- Upload Foto Profil Asli -->
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200/80 space-y-1.5">
                    <label class="block text-xs font-bold text-slate-800 uppercase tracking-wide">
                        Foto Profil Asli (Wajib) <span class="text-rose-500">*</span>
                    </label>
                    <p class="text-[11px] text-slate-500">Gunakan foto rapi, wajah tampak jelas dan pencahayaan terang (Format: JPG, PNG, Maks 2MB). Foto ini akan dilihat oleh calon majikan.</p>
                    <input type="file" 
                           name="foto" 
                           class="w-full text-xs text-slate-600 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-600 file:text-white hover:file:bg-blue-700 file:cursor-pointer mt-1" 
                           accept="image/*" 
                           required>
                </div>
            </div>

            <!-- SECTION 3: DOMISILI & KESIAPAN KERJA -->
            <div class="space-y-4 pt-2">
                <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider pb-2 border-b border-slate-100 flex items-center space-x-2">
                    <span class="w-5 h-5 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center text-[10px]">3</span>
                    <span>Wilayah Domisili & Tipe Kesiapan Kerja</span>
                </h3>

                <!-- Provinsi & Kabupaten (Chained Select dari Database) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="selectProvinsi" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                            Provinsi Penugasan <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <select name="provinsi" 
                                    id="selectProvinsi" 
                                    onchange="onProvinsiChange(this.value, 'selectKabupaten')" 
                                    class="w-full appearance-none bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" 
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
                                    class="w-full appearance-none bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm text-slate-800 font-medium focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" 
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
                        Alamat Tempat Tinggal Sekarang <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="alamat_lengkap" 
                              id="alamat_lengkap" 
                              rows="3" 
                              placeholder="Alamat tempat tinggal lengkap Anda saat ini" 
                              class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                              required>{{ old('alamat_lengkap') }}</textarea>
                </div>

                <!-- Pilihan Tipe Pekerjaan Checkbox -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-2">
                        Kesiapan Tipe Pekerjaan (Pilih minimal satu)
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="flex items-center p-3.5 rounded-xl border border-slate-300 bg-slate-50 hover:bg-white cursor-pointer transition">
                            <input type="checkbox" 
                                   name="tipe_pekerjaan[]" 
                                   value="Harian" 
                                   class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 mr-3" 
                                   {{ (is_array(old('tipe_pekerjaan')) && in_array('Harian', old('tipe_pekerjaan'))) || !old('tipe_pekerjaan') ? 'checked' : '' }}>
                            <div>
                                <span class="font-bold text-slate-900 text-sm block">Siap Kerja Harian</span>
                                <span class="text-[11px] text-slate-500">Menerima pesanan 1 hari atau beberapa hari lepas</span>
                            </div>
                        </label>

                        <label class="flex items-center p-3.5 rounded-xl border border-slate-300 bg-slate-50 hover:bg-white cursor-pointer transition">
                            <input type="checkbox" 
                                   name="tipe_pekerjaan[]" 
                                   value="Mingguan" 
                                   class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 mr-3" 
                                   {{ (is_array(old('tipe_pekerjaan')) && in_array('Mingguan', old('tipe_pekerjaan'))) ? 'checked' : '' }}>
                            <div>
                                <span class="font-bold text-slate-900 text-sm block">Siap Kerja Mingguan</span>
                                <span class="text-[11px] text-slate-500">Menerima pesanan durasi 7 hari atau kelipatannya</span>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- SECTION 4: SYARAT & KETENTUAN KEMITRAAN (TERMS & CONDITIONS) -->
            <div class="space-y-4 pt-2">
                <h3 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider pb-2 border-b border-slate-100 flex items-center space-x-2">
                    <span class="w-5 h-5 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center text-[10px] font-black">4</span>
                    <span>Syarat & Ketentuan Kemitraan (Wajib Disetujui)</span>
                </h3>

                <!-- Box T&C Scrollable & Terstruktur -->
                <div class="bg-amber-50/40 border border-amber-200/90 rounded-2xl p-5 space-y-4 text-xs text-slate-700 leading-relaxed max-h-72 overflow-y-auto shadow-2xs">
                    <div class="flex items-center space-x-2 text-amber-900 font-extrabold pb-2.5 border-b border-amber-200/60">
                        <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <span class="text-xs sm:text-sm">Integritas Kerja & Kebijakan Perlindungan Hukum MyBantu</span>
                    </div>

                    <div class="space-y-3">
                        <!-- Pasal 1: Larangan Keras Tindak Pidana -->
                        <div class="bg-white/80 p-3.5 rounded-xl border border-rose-200/80 space-y-1">
                            <p class="font-extrabold text-rose-700 flex items-center space-x-1.5 text-xs">
                                <span>⚖️</span>
                                <span>Pasal 1: Larangan Keras Kejahatan & Ancaman Sanksi Pidana</span>
                            </p>
                            <p class="text-[11px] text-slate-600 leading-relaxed text-justify">
                                Mitra Asisten Rumah Tangga (ART) <strong>DILARANG KERAS</strong> melakukan segala bentuk tindak kejahatan atau pelanggaran hukum di lingkungan kerja majikan, termasuk namun tidak terbatas pada: <em>pencurian, penggelapan uang/perhiasan/barang rumah tangga, kekerasan fisik maupun verbal, penipuan, perusakan properti, serta penyebaran privasi keluarga majikan</em>.
                            </p>
                            <p class="text-[11px] text-rose-800 font-bold bg-rose-50 p-2 rounded-lg border border-rose-200/60">
                                🚨 <strong>Tindakan Tegas Kepolisian:</strong> Setiap indikasi tindak pidana akan <strong>LANGSUNG DILAPORKAN OLEH MANAJEMEN MYBANTU KE PIHAK KEPOLISIAN (POLRI)</strong> untuk diproses hukum pidana sesuai Kitab Undang-Undang Hukum Pidana (KUHP), disertai penyerahan seluruh data KTP, foto, alamat, dan kontak Anda, serta pemblokiran akun permanen.
                            </p>
                        </div>

                        <!-- Pasal 2: Skema Fee 15% & Upah Bersih 85% -->
                        <div class="bg-white/80 p-3.5 rounded-xl border border-emerald-200/80 space-y-1">
                            <p class="font-extrabold text-emerald-800 flex items-center space-x-1.5 text-xs">
                                <span>💰</span>
                                <span>Pasal 2: Skema Upah & Pemotongan Biaya Layanan 15%</span>
                            </p>
                            <p class="text-[11px] text-slate-600 leading-relaxed text-justify">
                                Mitra ART memahami dan menyetujui bahwa dari total nilai transaksi pesanan yang berhasil diselesaikan, berlaku <strong>pemotongan biaya platform resmi sebesar 15%</strong> untuk operasional sistem garansi Rekening Bersama (Rekber), perlindungan asuransi kerja, dan verifikasi data majikan.
                            </p>
                            <p class="text-[11px] text-emerald-800 font-bold bg-emerald-50 p-2 rounded-lg border border-emerald-200/60">
                                ✓ <strong>Hak Upah Bersih Mitra (85%):</strong> Mitra ART menerima penuh <strong>85% upah bersih</strong> tanpa ada potongan tersembunyi lainnya, yang dapat dicairkan langsung ke rekening bank Anda (BCA, Mandiri, BRI, BNI) atau dompet digital resmi.
                            </p>
                        </div>

                        <!-- Pasal 3: Integritas & Disiplin -->
                        <div class="bg-white/80 p-3.5 rounded-xl border border-slate-200 space-y-1">
                            <p class="font-extrabold text-slate-800 flex items-center space-x-1.5 text-xs">
                                <span>⏱️</span>
                                <span>Pasal 3: Disiplin Jadwal & Tanggung Jawab Tugas</span>
                            </p>
                            <p class="text-[11px] text-slate-600 leading-relaxed text-justify">
                                Mitra ART berkomitmen hadir tepat waktu sesuai pesanan yang telah dikonfirmasi (Harian maupun Mingguan), bekerja secara jujur, menjaga kebersihan, bersikap sopan santun, dan menyelesaikan tugas kerumahtanggaan dengan teliti dan penuh dedikasi.
                            </p>
                        </div>

                        <!-- Pasal 4: Keamanan Sistem Rekber -->
                        <div class="bg-white/80 p-3.5 rounded-xl border border-slate-200 space-y-1">
                            <p class="font-extrabold text-slate-800 flex items-center space-x-1.5 text-xs">
                                <span>🛡️</span>
                                <span>Pasal 4: Transaksi Wajib Melalui Platform Resmi</span>
                            </p>
                            <p class="text-[11px] text-slate-600 leading-relaxed text-justify">
                                Seluruh pembayaran upah wajib melalui sistem Rekber MyBantu demi menjamin kepastian pembayaran Anda. Mitra dilarang meminta atau menerima transaksi tunai langsung di luar sistem sebelum pekerjaan tuntas diverifikasi.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Checkbox Wajib Setuju (Mandatory Agreement) -->
                <div class="bg-white p-4 sm:p-5 rounded-2xl border-2 border-emerald-400 shadow-xs">
                    <label class="flex items-start space-x-3.5 cursor-pointer">
                        <input type="checkbox" 
                               name="terms" 
                               id="checkTerms" 
                               value="1" 
                               {{ old('terms') ? 'checked' : '' }} 
                               class="w-5 h-5 mt-0.5 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 flex-shrink-0" 
                               required>
                        <div class="text-xs text-slate-800">
                            <span class="font-extrabold text-slate-900 block mb-0.5">
                                Lembar Persetujuan Pendaftaran Mitra ART <span class="text-rose-500">*</span>
                            </span>
                            <span class="text-slate-600 leading-normal block">
                                Saya menyatakan telah membaca, memahami, dan <strong>menyetujui seluruh Syarat & Ketentuan Kemitraan</strong> di atas, termasuk pemotongan fee platform 15%, hak upah bersih 85%, serta <strong>bersedia diproses secara hukum pidana ke pihak Kepolisian</strong> apabila melakukan tindak kejahatan atau penggelapan selama bertugas.
                            </span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- SUBMIT BUTTON -->
            <div class="pt-4 border-t border-slate-100">
                <button type="submit" 
                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold py-3.5 px-4 rounded-xl text-sm transition duration-150 shadow-md shadow-emerald-600/25 flex items-center justify-center space-x-2">
                    <span>Setujui Syarat & Daftarkan Diri Sebagai Mitra ART &rarr;</span>
                </button>
            </div>
        </form>

        <div class="text-center text-xs text-slate-500 pt-2">
            <span>Sudah pernah mendaftar sebagai mitra?</span> 
            <a href="{{ route('login', ['role' => 'worker']) }}" class="font-bold text-emerald-700 hover:text-emerald-900 transition">Masuk ke Panel Mitra ART &rarr;</a>
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
