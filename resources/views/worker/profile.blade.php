@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">

    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-200/80 pb-6 gap-4">
        <div>
            <div class="inline-flex items-center space-x-2 bg-emerald-50 text-emerald-700 text-xs font-bold px-3 py-1 rounded-full mb-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Mitra Asisten Rumah Tangga</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Pengaturan Profil & Tarif Layanan
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Lengkapi deskripsi keahlian, pasang tarif harian/mingguan, dan perbarui foto profil Anda agar menarik minat majikan.
            </p>
        </div>

        <div class="flex items-center space-x-2">
            <a href="{{ route('product.detail', $worker->id) }}" target="_blank"
               class="inline-flex items-center text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-100 border border-blue-200 px-4 py-2.5 rounded-xl transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                Lihat Tampilan Publik Profil
            </a>
        </div>
    </div>

    @php
        $isWorkerInactive = strtoupper($worker->status_worker ?? '') !== 'AKTIF';
        $admin = $adminContact ?? (\App\Models\Admin::where('status', 'AKTIF')->first() ?? \App\Models\Admin::first());
        $adminEmail = $admin?->email ?? 'admin@mybantu.id';
        $adminPhone = $admin?->phone_number ?? '081234567890';
        $cleanWa = preg_replace('/[^0-9]/', '', $adminPhone);
        if (str_starts_with($cleanWa, '0')) {
            $cleanWa = '62' . substr($cleanWa, 1);
        }
        $waMessage = "Halo Admin MyBantu, saya ingin mengajukan pengaktifan kembali status akun Mitra ART saya:\n- Nama: {$worker->fullname}\n- ID Mitra: #{$worker->id}\n- Email: {$worker->email}\nMohon bantuannya untuk mengaktifkan kembali akun saya. Terima kasih.";
        $mailSubject = "Pengajuan Pengaktifan Kembali Status Akun Mitra ART - {$worker->fullname} (#{$worker->id})";
        $mailBody = "Halo Admin MyBantu,%0D%0A%0D%0ASaya ingin mengajukan permohonan pengaktifan kembali status akun saya sebagai Mitra ART di platform MyBantu:%0D%0A- Nama Lengkap: {$worker->fullname}%0D%0A- ID Mitra: #{$worker->id}%0D%0A- Email Terdaftar: {$worker->email}%0D%0A- No. HP: {$worker->phone_number}%0D%0A%0D%0AMohon bantuannya untuk melakukan verifikasi dan mengaktifkan kembali status akun saya agar dapat menerima pesanan. Terima kasih.";
    @endphp

    @if($isWorkerInactive)
        <!-- BANNER AKUN NONAKTIF & OPSI HUBUNGI ADMIN -->
        <div class="bg-gradient-to-br from-amber-500/10 via-rose-50 to-amber-50 border-2 border-amber-400 rounded-3xl p-6 sm:p-7 shadow-sm space-y-4">
            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 rounded-2xl bg-rose-600 text-white flex items-center justify-center text-2xl flex-shrink-0 shadow-md shadow-rose-600/20">
                    ⚠️
                </div>
                <div class="flex-1 space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center text-[10px] font-extrabold uppercase tracking-wider bg-rose-600 text-white px-2.5 py-0.5 rounded-full">
                            Status Akun: NONAKTIF
                        </span>
                        <span class="text-xs font-semibold text-rose-800">Perhatian Mitra ART</span>
                    </div>
                    <h3 class="text-base sm:text-lg font-black text-slate-900">
                        Akun Anda Saat Ini Berstatus Non-Aktif
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Profil Anda saat ini dinonaktifkan sehingga <strong>tidak muncul pada daftar pencarian ART</strong> dan <strong>tidak dapat menerima pesanan baru</strong> dari majikan. Untuk mengaktifkan kembali status akun Anda, silakan hubungi Administrator MyBantu melalui salah satu opsi berikut:
                    </p>
                </div>
            </div>

            <!-- PILIHAN KE WHATSAPP ATAU KE EMAIL ADMIN -->
            <div class="pt-3 border-t border-amber-200 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <!-- Opsi 1: WhatsApp Admin -->
                <a href="https://wa.me/{{ $cleanWa }}?text={{ urlencode($waMessage) }}" 
                   target="_blank" 
                   class="flex-1 inline-flex items-center justify-center space-x-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-extrabold px-5 py-3 rounded-2xl shadow-sm shadow-emerald-600/20 transition duration-150 group">
                    <span class="text-base group-hover:scale-110 transition-transform">💬</span>
                    <span>Hubungi via WhatsApp Admin ({{ $adminPhone }})</span>
                </a>

                <!-- Opsi 2: Email Admin -->
                <a href="mailto:{{ $adminEmail }}?subject={{ rawurlencode($mailSubject) }}&body={{ $mailBody }}" 
                   class="flex-1 inline-flex items-center justify-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-extrabold px-5 py-3 rounded-2xl shadow-sm shadow-blue-600/20 transition duration-150 group">
                    <span class="text-base group-hover:scale-110 transition-transform">✉️</span>
                    <span>Hubungi via Email Admin ({{ $adminEmail }})</span>
                </a>
            </div>
        </div>
    @endif

    <!-- ERROR ALERT -->
    @if (isset($errors) && $errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-2xl text-xs font-medium space-y-1">
            <p class="font-bold">Mohon periksa kesalahan input berikut:</p>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form id="workerProfileForm" onsubmit="return validateTarifForm(event)" action="{{ route('worker.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <!-- CARD 1: FOTO PROFIL & IDENTITAS -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8 space-y-6">
            <h2 class="text-base font-extrabold text-slate-900 border-b border-slate-100 pb-3 flex items-center justify-between">
                <span>Foto Profil & Data Diri</span>
                @if(!$isWorkerInactive)
                    <span class="text-xs font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-2.5 py-1 rounded-lg">Status: AKTIF</span>
                @else
                    <span class="text-xs font-bold text-rose-700 bg-rose-50 border border-rose-200 px-2.5 py-1 rounded-lg">Status: NONAKTIF</span>
                @endif
            </h2>

            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                <!-- Foto Worker Preview -->
                <div class="flex flex-col items-center space-y-2">
                    <div class="w-28 h-28 rounded-3xl overflow-hidden bg-slate-100 border-2 border-slate-200 flex items-center justify-center relative shadow-xs">
                        @if(!empty($worker->foto_path))
                            <img id="previewWorkerFoto" src="{{ asset('storage/' . $worker->foto_path) }}" alt="{{ $worker->fullname }}" class="w-full h-full object-cover">
                        @else
                            <div id="previewWorkerFallback" class="w-full h-full flex items-center justify-center bg-emerald-600 text-white font-black text-3xl">
                                {{ strtoupper(substr($worker->fullname, 0, 1)) }}
                            </div>
                            <img id="previewWorkerFoto" src="" alt="Preview" class="w-full h-full object-cover hidden">
                        @endif
                    </div>
                    <span class="text-[10px] text-slate-400 font-medium">Foto Profil Resmi</span>
                </div>

                <div class="flex-1 w-full space-y-4">
                    <div>
                        <label for="fotoInput" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                            Ganti / Upload Foto Profil
                        </label>
                        <input type="file" 
                               name="foto" 
                               id="fotoInput" 
                               accept="image/png,image/jpeg,image/jpg" 
                               onchange="previewWorkerImage(event)" 
                               class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 border border-slate-300 rounded-xl p-1 bg-slate-50">
                        <p class="text-[11px] text-slate-400 mt-1">Gunakan foto wajah yang sopan dan jelas (JPG, PNG, maks 2MB).</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">
                                ID Mitra Worker
                            </label>
                            <div class="relative">
                                <input type="text" 
                                       value="ID #{{ str_pad($worker->id, 4, '0', STR_PAD_LEFT) }}" 
                                       disabled 
                                       readonly 
                                       class="w-full bg-slate-100 border border-slate-200 rounded-xl px-4 py-3 text-sm font-extrabold text-slate-400 cursor-not-allowed">
                                <span class="absolute right-3.5 top-3.5 text-[10px] font-bold text-slate-400 bg-slate-200/80 px-2 py-0.5 rounded">ID Resmi</span>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1">ID unik mitra yang terdaftar resmi di MyBantu.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">
                                Nama Lengkap (KTP)
                            </label>
                            <input type="text" 
                                   value="{{ $worker->fullname }}" 
                                   disabled 
                                   readonly 
                                   class="w-full bg-slate-100 border border-slate-200 rounded-xl px-4 py-3 text-sm font-semibold text-slate-400 cursor-not-allowed">
                            <p class="text-[10px] text-slate-400 mt-1">Sesuai identitas terverifikasi.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide mb-1.5">
                                NIK KTP
                            </label>
                            <input type="text" 
                                   value="{{ $worker->nik }}" 
                                   disabled 
                                   readonly 
                                   class="w-full bg-slate-100 border border-slate-200 rounded-xl px-4 py-3 text-sm font-semibold text-slate-400 cursor-not-allowed">
                            <p class="text-[10px] text-slate-400 mt-1">16 digit nomor kependudukan.</p>
                        </div>
                    </div>

                    <!-- Upload SKCK Section -->
                    <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-200/90 space-y-2 mt-2">
                        <div class="flex items-center justify-between">
                            <label for="skckInput" class="block text-xs font-bold text-slate-800 uppercase tracking-wide">
                                Dokumen SKCK Kepolisian (Surat Keterangan Catatan Kepolisian)
                            </label>
                            @if(!empty($worker->skck_path))
                                <span class="inline-flex items-center text-[10px] font-bold text-emerald-700 bg-emerald-100 border border-emerald-200 px-2.5 py-0.5 rounded-full">
                                    ✓ Berkas SKCK Terunggah
                                </span>
                            @else
                                <span class="inline-flex items-center text-[10px] font-bold text-amber-700 bg-amber-100 border border-amber-200 px-2.5 py-0.5 rounded-full">
                                    Belum Ada Dokumen SKCK
                                </span>
                            @endif
                        </div>
                        <p class="text-[11px] text-slate-500">
                            Upload berkas scan/foto SKCK aktif dari Kepolisian untuk membuktikan rekam jejak bebas kejahatan Anda. Dokumen ini menjadi syarat verifikasi prioritas oleh majikan (Format: PDF, JPG, PNG, maks 2MB).
                        </p>
                        <input type="file" 
                               name="skck" 
                               id="skckInput" 
                               accept=".jpg,.jpeg,.png,.pdf" 
                               class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 border border-slate-300 rounded-xl p-1 bg-white">
                        
                        @if(!empty($worker->skck_path))
                            <div class="pt-1.5 flex items-center space-x-2 text-xs">
                                <a href="{{ asset('storage/' . $worker->skck_path) }}" target="_blank" class="inline-flex items-center space-x-1.5 text-blue-600 hover:text-blue-800 font-bold bg-white px-3 py-1.5 rounded-lg border border-blue-200 shadow-2xs">
                                    <span>📄 Lihat Dokumen SKCK Saat Ini</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                    </svg>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD 2: DESKRIPSI & TENTANG SAYA (AKAN MUNCUL DI DETAIL PRODUK) -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8 space-y-4">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Deskripsi Diri & Keahlian ART</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Deskripsi ini akan langsung ditampilkan di halaman detail pemesanan produk Anda.</p>
                </div>
                <span class="text-xs bg-blue-50 text-blue-700 font-bold px-3 py-1 rounded-full">Tampil di Publik</span>
            </div>

            <div>
                <label for="description" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                    Ceritakan Pengalaman, Keahlian, dan Komitmen Kerja Anda
                </label>
                <textarea name="description" 
                          id="description" 
                          rows="4" 
                          class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-medium text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" 
                          placeholder="Contoh: Saya memiliki pengalaman 4 tahun sebagai asisten rumah tangga. Teliti dalam mencuci, menyetrika, memasak masakan rumahan, merapikan ruangan, dan jujur dalam bertugas. Siap bekerja sesuai jadwal.">{{ old('description', $worker->description) }}</textarea>
                <p class="text-[11px] text-slate-400 mt-1">Majikan menyukai profil dengan penjelasan keahlian yang jelas dan ramah.</p>
            </div>
        </div>

        <!-- CARD 3: PENGATURAN HARGA & TIPE LAYANAN (HARIAN & MINGGUAN MULTIPLE) -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8 space-y-6">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Pengaturan Tarif Layanan (Harian & Mingguan)</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Anda dapat mengaktifkan salah satu atau kedua opsi layanan sekaligus (multiple) dan mengatur tarif masing-masing.</p>
                </div>
                <span class="text-xs bg-emerald-50 text-emerald-700 font-bold px-3 py-1 rounded-full">Fleksibel</span>
            </div>

            @php
                $harianVal = isset($harianPrice) ? (int)$harianPrice->harga : 0;
                $mingguanVal = isset($mingguanPrice) ? (int)$mingguanPrice->harga : 0;
                $isHarianActive = $harianVal > 0;
                $isMingguanActive = $mingguanVal > 0;
            @endphp

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- OPSI 1: HARIAN -->
                <div id="cardHarian" class="border rounded-2xl p-5 space-y-4 {{ $isHarianActive ? 'border-blue-300 bg-blue-50/20' : 'border-slate-200 bg-slate-50/50' }}">
                    <div class="flex items-center justify-between">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="checkbox" 
                                   name="tipe_pekerjaan[]" 
                                   value="Harian" 
                                   id="checkHarian" 
                                   onchange="toggleRateInput('Harian')" 
                                   {{ $isHarianActive ? 'checked' : '' }} 
                                   class="w-5 h-5 rounded-md text-blue-600 focus:ring-blue-500 border-slate-300">
                            <span class="font-bold text-sm text-slate-900">Layanan Harian</span>
                        </label>
                        <span class="text-[11px] font-semibold text-blue-700 bg-blue-100 px-2 py-0.5 rounded-md">Per Hari</span>
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Pekerjaan dihitung per 1 hari kerja (cocok untuk kebutuhan singkat / panggilan).
                    </p>
                    <div>
                        <label for="harga_harian" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1">
                            Tarif Jasa Harian (Rp) <span id="reqHarianBadge" class="text-rose-500 {{ $isHarianActive ? '' : 'hidden' }}">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-xs font-bold text-slate-400">Rp</span>
                            <input type="number" 
                                   name="harga_harian" 
                                   id="harga_harian" 
                                   value="{{ old('harga_harian', $harianVal) }}" 
                                   min="0" 
                                   step="1000" 
                                   {{ $isHarianActive ? 'required' : '' }} 
                                   class="w-full bg-white border border-slate-300 rounded-xl pl-10 pr-4 py-2.5 text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                                   placeholder="0">
                        </div>
                        <span class="text-[10px] text-slate-400 mt-1 block">Wajib diisi (> 0) jika layanan Harian dicentang aktif.</span>
                    </div>
                </div>

                <!-- OPSI 2: MINGGUAN -->
                <div id="cardMingguan" class="border rounded-2xl p-5 space-y-4 {{ $isMingguanActive ? 'border-emerald-300 bg-emerald-50/20' : 'border-slate-200 bg-slate-50/50' }}">
                    <div class="flex items-center justify-between">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="checkbox" 
                                   name="tipe_pekerjaan[]" 
                                   value="Mingguan" 
                                   id="checkMingguan" 
                                   onchange="toggleRateInput('Mingguan')" 
                                   {{ $isMingguanActive ? 'checked' : '' }} 
                                   class="w-5 h-5 rounded-md text-emerald-600 focus:ring-emerald-500 border-slate-300">
                            <span class="font-bold text-sm text-slate-900">Layanan Mingguan</span>
                        </label>
                        <span class="text-[11px] font-semibold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-md">Per 7 Hari</span>
                    </div>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Pekerjaan dihitung per periode 1 minggu (7 hari kerja berkelanjutan).
                    </p>
                    <div>
                        <label for="harga_mingguan" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1">
                            Tarif Jasa Mingguan (Rp) <span id="reqMingguanBadge" class="text-rose-500 {{ $isMingguanActive ? '' : 'hidden' }}">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-xs font-bold text-slate-400">Rp</span>
                            <input type="number" 
                                   name="harga_mingguan" 
                                   id="harga_mingguan" 
                                   value="{{ old('harga_mingguan', $mingguanVal) }}" 
                                   min="0" 
                                   step="1000" 
                                   {{ $isMingguanActive ? 'required' : '' }} 
                                   class="w-full bg-white border border-slate-300 rounded-xl pl-10 pr-4 py-2.5 text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" 
                                   placeholder="0">
                        </div>
                        <span class="text-[10px] text-slate-400 mt-1 block">Wajib diisi (> 0) jika layanan Mingguan dicentang aktif.</span>
                    </div>
                </div>
            </div>
            <p class="text-[11px] text-slate-500 italic">
                * Minimal salah satu opsi layanan (Harian atau Mingguan) harus dicentang untuk menyimpan perubahan. Jika dicentang aktif, tarif wajib diisi nominalnya (> 0). Jika tidak dicentang, tarif akan bernilai 0.
            </p>
        </div>

        <!-- CARD 4: KONTAK & WILAYAH KERJA -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8 space-y-6">
            <h2 class="text-base font-extrabold text-slate-900 border-b border-slate-100 pb-3">
                Kontak & Jangkauan Lokasi Domisili
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        Alamat Email <span class="text-rose-500">*</span>
                    </label>
                    <input type="email" 
                           name="email" 
                           id="email" 
                           value="{{ old('email', $worker->email) }}" 
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" 
                           required>
                </div>

                <div>
                    <label for="phone_number" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        Nomor Handphone / WhatsApp <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="phone_number" 
                           id="phone_number" 
                           value="{{ old('phone_number', $worker->phone_number) }}" 
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" 
                           required>
                </div>
            </div>

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
                                class="w-full appearance-none bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm text-slate-800 font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" 
                                required>
                            <option value="">-- Pilih Provinsi --</option>
                            @foreach($provinces ?? [] as $p)
                                <option value="{{ $p->name }}" {{ old('provinsi', $worker->provinsi) == $p->name ? 'selected' : '' }}>
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
                        Kabupaten / Kota Penugasan <span class="text-rose-500">*</span>
                    </label>
                    <div class="relative">
                        <select name="kabupaten" 
                                id="selectKabupaten" 
                                class="w-full appearance-none bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm text-slate-800 font-semibold focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" 
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

            <div>
                <label for="alamat_lengkap" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                    Alamat Lengkap Tempat Tinggal <span class="text-rose-500">*</span>
                </label>
                <textarea name="alamat_lengkap" 
                          id="alamat_lengkap" 
                          rows="3" 
                          class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition" 
                          required>{{ old('alamat_lengkap', $worker->alamat_lengkap) }}</textarea>
            </div>
        </div>

        <!-- CARD 5: UBAH PASSWORD (OPTIONAL) -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8 space-y-6">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Ubah Kata Sandi Akun Worker</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Kosongkan jika Anda tetap menggunakan kata sandi saat ini.</p>
                </div>
                <span class="text-xs bg-slate-100 text-slate-600 px-3 py-1 rounded-full font-bold">Opsional</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        Kata Sandi Baru
                    </label>
                    <input type="password" 
                           name="password" 
                           id="password" 
                           placeholder="Minimal 6 karakter" 
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        Konfirmasi Kata Sandi Baru
                    </label>
                    <input type="password" 
                           name="password_confirmation" 
                           id="password_confirmation" 
                           placeholder="Ulangi kata sandi baru" 
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition">
                </div>
            </div>
        </div>

        <!-- SUBMIT BUTTON -->
        <div class="flex items-center justify-end space-x-3 pt-2">
            <a href="{{ route('worker.transaksi') }}" 
               class="px-5 py-3 rounded-xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-50 transition">
                Batal
            </a>
            <button type="submit" 
                    class="bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold px-6 py-3 rounded-xl text-xs transition duration-150 shadow-md shadow-emerald-600/25 flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>Simpan Perubahan Profil & Tarif</span>
            </button>
        </div>
    </form>

</div>

<script>
    function previewWorkerImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('previewWorkerFoto');
                const fallback = document.getElementById('previewWorkerFallback');
                img.src = e.target.result;
                img.classList.remove('hidden');
                if (fallback) fallback.classList.add('hidden');
            }
            reader.readAsDataURL(file);
        }
    }

    function toggleRateInput(tipe) {
        if (tipe === 'Harian') {
            const check = document.getElementById('checkHarian');
            const input = document.getElementById('harga_harian');
            const card = document.getElementById('cardHarian');
            const badge = document.getElementById('reqHarianBadge');
            if (check.checked) {
                card.className = "border rounded-2xl p-5 space-y-4 border-blue-300 bg-blue-50/20";
                input.required = true;
                if (badge) badge.classList.remove('hidden');
                // Tetap biarkan nilai saat ini atau 0 jika kosong (jangan set otomatis 120rb)
                if (!input.value) {
                    input.value = 0;
                }
                input.focus();
            } else {
                card.className = "border rounded-2xl p-5 space-y-4 border-slate-200 bg-slate-50/50";
                input.required = false;
                if (badge) badge.classList.add('hidden');
                input.value = 0;
            }
        } else if (tipe === 'Mingguan') {
            const check = document.getElementById('checkMingguan');
            const input = document.getElementById('harga_mingguan');
            const card = document.getElementById('cardMingguan');
            const badge = document.getElementById('reqMingguanBadge');
            if (check.checked) {
                card.className = "border rounded-2xl p-5 space-y-4 border-emerald-300 bg-emerald-50/20";
                input.required = true;
                if (badge) badge.classList.remove('hidden');
                // Tetap biarkan nilai saat ini atau 0 jika kosong (jangan set otomatis 750rb)
                if (!input.value) {
                    input.value = 0;
                }
                input.focus();
            } else {
                card.className = "border rounded-2xl p-5 space-y-4 border-slate-200 bg-slate-50/50";
                input.required = false;
                if (badge) badge.classList.add('hidden');
                input.value = 0;
            }
        }
    }

    function validateTarifForm(e) {
        const checkHarian = document.getElementById('checkHarian').checked;
        const checkMingguan = document.getElementById('checkMingguan').checked;
        const hargaHarian = parseFloat(document.getElementById('harga_harian').value) || 0;
        const hargaMingguan = parseFloat(document.getElementById('harga_mingguan').value) || 0;

        // 1. Salah satu harus dicentang
        if (!checkHarian && !checkMingguan) {
            e.preventDefault();
            alert('Minimal salah satu opsi layanan (Harian atau Mingguan) harus dicentang untuk menyimpan perubahan!');
            return false;
        }

        // 2. Mandatory diisi jika aktif
        if (checkHarian && hargaHarian <= 0) {
            e.preventDefault();
            alert('Layanan Harian aktif! Tarif Harian wajib diisi dengan nominal lebih dari Rp 0.');
            document.getElementById('harga_harian').focus();
            return false;
        }

        if (checkMingguan && hargaMingguan <= 0) {
            e.preventDefault();
            alert('Layanan Mingguan aktif! Tarif Mingguan wajib diisi dengan nominal lebih dari Rp 0.');
            document.getElementById('harga_mingguan').focus();
            return false;
        }

        return true;
    }

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
        const currentKab = @json(old('kabupaten', $worker->kabupaten));
        if (initialProv) {
            onProvinsiChange(initialProv, 'selectKabupaten', currentKab);
        }
    });
</script>
@endsection

