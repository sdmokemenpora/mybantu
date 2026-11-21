@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- BREADCRUMB -->
    <div class="flex items-center space-x-2 text-xs font-semibold text-slate-500 mb-6">
        <a href="{{ route('home') }}" class="hover:text-blue-600 transition">Beranda</a>
        <span>/</span>
        <a href="{{ route('product') }}" class="hover:text-blue-600 transition">Daftar ART</a>
        <span>/</span>
        <span class="text-slate-900 font-bold">{{ $worker->fullname }}</span>
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
        <!-- BANNER PERINGATAN MITRA NONAKTIF & OPSI HUBUNGI ADMIN -->
        <div class="bg-gradient-to-br from-amber-500/10 via-rose-50 to-amber-50 border-2 border-amber-400 rounded-3xl p-6 sm:p-7 shadow-sm mb-8 space-y-4">
            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 rounded-2xl bg-rose-600 text-white flex items-center justify-center text-2xl flex-shrink-0 shadow-md shadow-rose-600/20">
                    ⚠️
                </div>
                <div class="flex-1 space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center text-[10px] font-extrabold uppercase tracking-wider bg-rose-600 text-white px-2.5 py-0.5 rounded-full">
                            Status: NONAKTIF
                        </span>
                        <span class="text-xs font-semibold text-rose-800">Informasi Status Mitra</span>
                    </div>
                    <h3 class="text-base sm:text-lg font-black text-slate-900">
                        Mitra ART Ini Saat Ini Sedang Nonaktif
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Profil asisten rumah tangga ini saat ini dinonaktifkan dari sistem dan <strong>tidak menerima pemesanan baru</strong>. Jika ini adalah akun Anda, silakan hubungi Administrator MyBantu untuk melakukan pengaktifan status kembali melalui salah satu opsi berikut:
                    </p>
                </div>
            </div>

            <!-- PILIHAN KE WHATSAPP ATAU KE EMAIL ADMIN -->
            <div class="pt-3 border-t border-amber-200 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <a href="https://wa.me/{{ $cleanWa }}?text={{ urlencode($waMessage) }}" 
                   target="_blank" 
                   class="flex-1 inline-flex items-center justify-center space-x-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs sm:text-sm font-extrabold px-5 py-3 rounded-2xl shadow-sm shadow-emerald-600/20 transition duration-150 group">
                    <span class="text-base group-hover:scale-110 transition-transform">💬</span>
                    <span>Hubungi via WhatsApp Admin ({{ $adminPhone }})</span>
                </a>
                <a href="mailto:{{ $adminEmail }}?subject={{ rawurlencode($mailSubject) }}&body={{ $mailBody }}" 
                   class="flex-1 inline-flex items-center justify-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white text-xs sm:text-sm font-extrabold px-5 py-3 rounded-2xl shadow-sm shadow-blue-600/20 transition duration-150 group">
                    <span class="text-base group-hover:scale-110 transition-transform">✉️</span>
                    <span>Hubungi via Email Admin ({{ $adminEmail }})</span>
                </a>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- KOLOM KIRI: Foto & Ulasan (7 Kolom di Desktop) -->
        <div class="lg:col-span-7 space-y-8">
            
            <!-- Foto Worker Card -->
            <div class="bg-white rounded-3xl p-4 sm:p-6 border border-slate-200/80 shadow-xs space-y-4">
                <div class="relative bg-slate-100 rounded-2xl overflow-hidden h-80 sm:h-96 flex items-center justify-center border border-slate-100">
                    @if(!empty($worker->foto_path))
                        <img src="{{ asset('storage/' . $worker->foto_path) }}" 
                             alt="{{ $worker->fullname }}" 
                             class="w-full h-full object-cover">
                    @else
                        <div class="flex flex-col items-center justify-center text-slate-400">
                            <svg class="w-20 h-20 mb-2 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span class="text-xs font-semibold">Foto Profil Resmi</span>
                        </div>
                    @endif
                    
                    <div class="absolute top-4 left-4 flex flex-col gap-1.5">
                        <span class="inline-flex items-center text-xs font-bold bg-slate-950/80 backdrop-blur-xs text-white px-3 py-1 rounded-full shadow-sm">
                            ID Mitra #{{ str_pad($worker->id, 4, '0', STR_PAD_LEFT) }}
                        </span>
                        @if(($worker->status_data ?? '') === 'sudah terverifikasi')
                            <span class="inline-flex items-center text-xs font-bold bg-emerald-600/90 backdrop-blur-xs text-white px-3 py-1 rounded-full shadow-sm">
                                ✓ KTP & SKCK Terverifikasi
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Bio & Kredensial Worker -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2 text-xs">
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/60 text-center">
                        <span class="text-slate-400 block text-[10px] uppercase font-semibold">Jenis Kelamin</span>
                        <span class="font-bold text-slate-800 mt-0.5 block">{{ $worker->sex ?? 'Perempuan' }}</span>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/60 text-center">
                        <span class="text-slate-400 block text-[10px] uppercase font-semibold">Domisili</span>
                        <span class="font-bold text-slate-800 mt-0.5 block">{{ $worker->kabupaten }}</span>
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/60 text-center">
                        <span class="text-slate-400 block text-[10px] uppercase font-semibold">Status Mitra</span>
                        @if($isWorkerInactive)
                            <span class="font-bold text-rose-600 mt-0.5 block">NONAKTIF</span>
                        @else
                            <span class="font-bold text-emerald-700 mt-0.5 block">AKTIF</span>
                        @endif
                    </div>
                    <div class="bg-slate-50 p-3 rounded-xl border border-slate-200/60 text-center">
                        <span class="text-slate-400 block text-[10px] uppercase font-semibold">Rating Rata-rata</span>
                        <span class="font-bold text-amber-600 mt-0.5 block">★ {{ $avgRating > 0 ? $avgRating : '5.0' }}</span>
                    </div>
                </div>
            </div>

            <!-- Bagian Deskripsi & Profil Pekerja -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-3">
                <h3 class="text-base font-extrabold text-slate-900">Tentang Asisten Rumah Tangga</h3>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed whitespace-pre-line">
                    {{ $worker->description ?? $worker->deskripsi ?? 'Halo! Saya ' . $worker->fullname . ', mitra Asisten Rumah Tangga resmi di MyBantu untuk area ' . $worker->kabupaten . ', ' . $worker->provinsi . '. Saya siap membantu menyelesaikan pekerjaan harian rumah tangga Anda mulai dari kebersihan lantai, merapikan ruangan, mencuci, hingga kebutuhan pendukung lainnya dengan teliti, jujur, dan bertanggung jawab.' }}
                </p>
                
                <div class="pt-4 border-t border-slate-100 flex flex-wrap gap-2 text-xs font-semibold text-slate-600">
                    <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-lg">🛡️ Rekber Platform</span>
                    <span class="bg-emerald-50 text-emerald-700 px-3 py-1 rounded-lg">✓ Lolos Uji Berkas</span>
                    <span class="bg-purple-50 text-purple-700 px-3 py-1 rounded-lg">⚡ Siap Kerja Sesuai Jadwal</span>
                </div>
            </div>

            <!-- Bagian Ulasan Pelanggan -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900">Ulasan & Penilaian Majikan</h3>
                        <p class="text-xs text-slate-500">Penilaian dari majikan yang telah menggunakan jasa {{ $worker->fullname }}</p>
                    </div>
                    <div class="flex items-center space-x-1.5 bg-amber-50 border border-amber-200 px-3 py-1 rounded-xl">
                        <span class="text-amber-500 text-sm">★</span>
                        <span class="font-black text-amber-800 text-xs">{{ $avgRating > 0 ? $avgRating : '5.0' }}</span>
                        <span class="text-[10px] text-amber-600">({{ $reviews->count() }} ulasan)</span>
                    </div>
                </div>

                <div class="space-y-3">
                    @forelse($reviews ?? [] as $rev)
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-2">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2.5">
                                    <div class="w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center font-bold text-xs">
                                        {{ strtoupper(substr($rev->user->name ?? 'User', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-800">{{ $rev->user->name ?? 'Pengguna MyBantu' }}</p>
                                        <p class="text-[10px] text-slate-400">{{ optional($rev->created_at)->format('d F Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex text-amber-400 text-xs">
                                    @for($i = 1; $i <= 5; $i++)
                                        <span>{{ $i <= ($rev->rating ?? 5) ? '★' : '☆' }}</span>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-xs text-slate-600 leading-relaxed italic">
                                "{{ $rev->review ?? $rev->ulasan ?? 'Pekerja sangat rapi dan ramah. Pekerjaan rumah beres tepat waktu.' }}"
                            </p>
                        </div>
                    @empty
                        <div class="text-center py-8 text-xs text-slate-400 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                            Belum ada ulasan untuk pekerja ini. Jadilah majikan pertama yang memberikan testimoni!
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- KOLOM KANAN: Form Pemesanan (5 Kolom di Desktop, Sticky) -->
        <div class="lg:col-span-5 lg:sticky lg:top-24">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-lg shadow-slate-200/40 space-y-6">
                
                <!-- Header Card Info -->
                <div class="space-y-1">
                    <div class="flex items-center space-x-1 text-amber-400 text-sm mb-1">
                        @for($i = 1; $i <= 5; $i++)
                            <span>{{ $i <= round($avgRating ?? 5) ? '★' : '☆' }}</span>
                        @endfor
                        <span class="text-xs font-bold text-slate-700 ml-1.5">({{ $avgRating > 0 ? $avgRating : '5.0' }})</span>
                    </div>

                    <h1 class="text-2xl font-extrabold text-slate-900">{{ $worker->fullname }}</h1>
                    <p class="text-xs text-slate-500">{{ $worker->kabupaten }}, {{ $worker->provinsi }}</p>
                </div>

                <!-- Price Highlight Box -->
                <div class="bg-blue-50/70 border border-blue-200/70 rounded-2xl p-4 flex items-center justify-between">
                    <div>
                        <span class="text-[11px] font-bold text-blue-700 uppercase tracking-wide">Tarif Layanan</span>
                        <div class="text-2xl font-black text-slate-900 mt-0.5" id="display_harga">
                            Rp {{ number_format($defaultPrice, 0, ',', '.') }}
                        </div>
                    </div>
                    <span class="text-xs text-slate-500 font-semibold bg-white px-2.5 py-1 rounded-lg border border-slate-200">
                        Garansi Rekber
                    </span>
                </div>

                @php
                    $buyer = $buyer ?? (Auth::guard('web')->user() ?? Auth::user());
                @endphp

                @if($isWorkerInactive)
                    <!-- NOTICE ORDER NONAKTIF & OPSI HUBUNGI ADMIN -->
                    <div class="bg-rose-50 border-2 border-rose-200 rounded-3xl p-6 space-y-4 text-center">
                        <div class="w-12 h-12 mx-auto rounded-2xl bg-rose-100 text-rose-700 flex items-center justify-center text-2xl">
                            🚫
                        </div>
                        <div>
                            <h4 class="font-extrabold text-slate-900 text-base">Pemesanan Tidak Tersedia</h4>
                            <p class="text-xs text-slate-600 leading-relaxed mt-1">
                                Mitra ART ini sedang berstatus <strong>NONAKTIF</strong> sehingga pemesanan tidak dapat dilakukan saat ini.
                            </p>
                        </div>

                        <div class="pt-3 border-t border-rose-200/80 space-y-3 text-left">
                            <span class="text-xs font-bold text-slate-800 block text-center">Hubungi Administrator untuk Pengaktifan Kembali:</span>
                            <div class="flex flex-col gap-2.5">
                                <a href="https://wa.me/{{ $cleanWa }}?text={{ urlencode($waMessage) }}" 
                                   target="_blank" 
                                   class="inline-flex items-center justify-center space-x-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-extrabold px-4 py-3 rounded-xl transition shadow-xs">
                                    <span>💬 WhatsApp Admin ({{ $adminPhone }})</span>
                                </a>
                                <a href="mailto:{{ $adminEmail }}?subject={{ rawurlencode($mailSubject) }}&body={{ $mailBody }}" 
                                   class="inline-flex items-center justify-center space-x-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-extrabold px-4 py-3 rounded-xl transition shadow-xs">
                                    <span>✉️ Email Admin ({{ $adminEmail }})</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                <!-- Booking Form -->
                <form id="orderForm" action="{{ route('order.store', $worker->id) }}" method="POST" class="space-y-4" onkeydown="if(event.key === 'Enter' && event.target.tagName !== 'TEXTAREA') { event.preventDefault(); return false; }">
                    @csrf
                    <input type="hidden" name="harga_satuan" id="harga_satuan" value="{{ $defaultPrice }}">

                    <!-- Tipe Pekerjaan -->
                    <div>
                        <label for="tipe_pekerjaan" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">
                            Pilihan Tipe Pekerjaan
                        </label>
                        <select name="tipe_pekerjaan" 
                                id="tipe_pekerjaan" 
                                onchange="onTipePekerjaanChange()" 
                                class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                            @if(isset($prices) && $prices->count() > 0)
                                @foreach($prices as $price)
                                    <option value="{{ $price->tipe_pekerjaan }}" 
                                            data-harga="{{ $price->harga }}" 
                                            {{ ($firstPriceRow && $firstPriceRow->id == $price->id) ? 'selected' : '' }}>
                                        {{ $price->tipe_pekerjaan }} - Rp {{ number_format($price->harga, 0, ',', '.') }}
                                    </option>
                                @endforeach
                            @else
                                <option value="Harian" data-harga="120000">Harian - Rp 120.000</option>
                                <option value="Mingguan" data-harga="750000">Mingguan - Rp 750.000</option>
                            @endif
                        </select>
                    </div>

                    <!-- Jumlah Qty (Durasi) -->
                    <div>
                        <label for="lama_pekerjaan" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">
                            Jumlah Durasi (Qty)
                        </label>
                        <div class="relative">
                            <input type="number" 
                                   name="jumlah_qty" 
                                   id="lama_pekerjaan" 
                                   value="1" 
                                   min="1" 
                                   oninput="updateKalkulasiTanggal()" 
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                                   required>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-xs font-semibold text-slate-400 pointer-events-none" id="qty_unit_label">
                                Hari / Minggu
                            </span>
                        </div>
                    </div>

                    <!-- Tanggal Mulai Bekerja -->
                    <div>
                        <label for="mulai_bekerja_iso" class="block text-xs font-bold text-slate-700 mb-1.5 uppercase tracking-wide">
                            Mulai Bekerja
                        </label>
                        <input type="date" 
                               name="tanggal_mulai" 
                               id="mulai_bekerja_iso" 
                               onchange="updateKalkulasiTanggal()" 
                               min="{{ date('Y-m-d') }}"
                               value="{{ date('Y-m-d') }}"
                               class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                               required>
                    </div>

                    <!-- Selesai Bekerja (Kalkulasi Otomatis) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-500 mb-1.5 uppercase tracking-wide">
                            Perkiraan Selesai Bekerja
                        </label>
                        <input type="text" 
                               id="selesai_bekerja_display" 
                               readonly 
                               class="w-full bg-slate-100 border border-slate-200 text-slate-700 font-bold rounded-xl px-4 py-3 text-sm cursor-not-allowed" 
                               placeholder="Menghitung estimasi selesai...">
                        <input type="hidden" name="selesai_bekerja" id="selesai_bekerja">
                    </div>

                    <!-- Informasi Alamat & Kontak Penugasan (Disimpan ke Transaksi) -->
                    <div class="pt-3 border-t border-slate-100 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-slate-700 block">
                                Lokasi & Kontak Penugasan
                            </span>
                            @if($buyer)
                                <button type="button" 
                                        onclick="resetToProfileAddress()" 
                                        class="text-[11px] text-blue-600 hover:text-blue-800 font-bold inline-flex items-center space-x-1 transition group"
                                        title="Kembalikan ke data alamat profil akun">
                                    <svg class="w-3.5 h-3.5 mr-0.5 group-hover:rotate-180 transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                    <span>Reset ke Profil</span>
                                </button>
                            @endif
                        </div>

                        @if($buyer)
                            <div class="p-2.5 bg-blue-50/80 rounded-xl border border-blue-200/70 flex items-start space-x-2 text-[11px] text-blue-800">
                                <svg class="w-4 h-4 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>Secara default terisi dari profil Anda. Anda dapat langsung mengedit nomor HP & lokasi jika penugasan di alamat lain.</span>
                            </div>
                        @endif

                        <div>
                            <label for="no_hp" class="block text-xs font-semibold text-slate-600 mb-1">
                                No. Handphone / WhatsApp Pemesan
                            </label>
                            <input type="text" 
                                   name="no_hp" 
                                   id="no_hp" 
                                   value="{{ old('no_hp', $buyer?->phone_number ?? '') }}" 
                                   placeholder="0812xxxxxxxx" 
                                   class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                                   required>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label for="provinsi" class="block text-xs font-semibold text-slate-600 mb-1">Provinsi</label>
                                <input type="text" 
                                       name="provinsi" 
                                       id="provinsi" 
                                       value="{{ old('provinsi', $buyer?->provinsi ?? '') }}" 
                                       placeholder="Provinsi" 
                                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                                       required>
                            </div>
                            <div>
                                <label for="kabupaten" class="block text-xs font-semibold text-slate-600 mb-1">Kabupaten/Kota</label>
                                <input type="text" 
                                       name="kabupaten" 
                                       id="kabupaten" 
                                       value="{{ old('kabupaten', $buyer?->kabupaten ?? '') }}" 
                                       placeholder="Kabupaten/Kota" 
                                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                                       required>
                            </div>
                        </div>

                        <div>
                            <label for="alamat_pemesan" class="block text-xs font-semibold text-slate-600 mb-1">
                                Alamat Lengkap Penugasan
                            </label>
                            <textarea name="alamat_pemesan" 
                                      id="alamat_pemesan" 
                                      rows="2" 
                                      placeholder="Nama jalan, nomor rumah, RT/RW, patokan" 
                                      class="w-full bg-slate-50 border border-slate-300 rounded-xl px-3 py-2 text-xs font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                                      required>{{ old('alamat_pemesan', $buyer?->alamat_lengkap ?? '') }}</textarea>
                        </div>
                    </div>

                    <!-- Ringkasan Total Biaya -->
                    <div class="pt-4 border-t border-slate-100 space-y-2 text-xs">
                        <div class="flex justify-between text-slate-500">
                            <span>Subtotal (<span id="summary_calc_text">1 x Tarif</span>)</span>
                            <span class="font-bold text-slate-800" id="summary_subtotal">Rp 0</span>
                        </div>
                        <div class="flex justify-between text-slate-500">
                            <span>Biaya Perlindungan Rekber</span>
                            <span class="font-bold text-emerald-600">GRATIS</span>
                        </div>
                        <div class="flex justify-between text-sm font-extrabold text-slate-900 pt-2 border-t border-slate-100">
                            <span>Estimasi Total Pembayaran</span>
                            <span class="text-blue-600 text-base" id="summary_total">Rp 0</span>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-2">
                        @auth
                            <button type="submit" 
                                    id="btnOrderSubmit"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-extrabold py-3.5 px-4 rounded-xl text-sm transition duration-150 shadow-md shadow-blue-600/25 flex items-center justify-center space-x-2">
                                <span>Order &rarr;</span>
                            </button>
                        @else
                            <a href="{{ route('login') }}" 
                               class="block w-full text-center bg-slate-900 hover:bg-slate-800 text-white font-extrabold py-3.5 px-4 rounded-xl text-sm transition duration-150 shadow-md">
                                Masuk untuk Memesan
                            </a>
                        @endauth
                    </div>
                </form>
                @endif

                <!-- Keamanan Pesanan Notes -->
                <div class="p-3 bg-slate-50 rounded-xl border border-slate-200/60 flex items-start space-x-2.5 text-[11px] text-slate-500">
                    <span class="text-emerald-600 font-bold">🛡️</span>
                    <span>Pembayaran Anda disimpan di Rekber MyBantu dan tidak diteruskan ke ART sampai Anda puas dengan layanan kerja.</span>
                </div>

            </div>
        </div>

    </div>
</div>

<!-- JavaScript Kalkulasi Dinamis -->
<script>
    function onTipePekerjaanChange() {
        const select = document.getElementById('tipe_pekerjaan');
        if (!select || select.selectedIndex === -1) return;
        
        const selectedOption = select.options[select.selectedIndex];
        const harga = parseFloat(selectedOption.getAttribute('data-harga')) || 0;

        document.getElementById('harga_satuan').value = harga;

        const formattedHarga = new Intl.NumberFormat('id-ID').format(harga);
        document.getElementById('display_harga').innerText = 'Rp ' + formattedHarga;

        const isMingguan = select.value.toLowerCase().includes('mingguan');
        document.getElementById('qty_unit_label').innerText = isMingguan ? 'Minggu' : 'Hari';

        updateKalkulasiTanggal();
    }

    function updateKalkulasiTanggal() {
        const select = document.getElementById('tipe_pekerjaan');
        const tipe = select ? select.value : 'Harian';
        const qty = parseInt(document.getElementById('lama_pekerjaan').value) || 1;
        const startDateInput = document.getElementById('mulai_bekerja_iso').value;
        const hargaSatuan = parseFloat(document.getElementById('harga_satuan').value) || 0;

        const displayInput = document.getElementById('selesai_bekerja_display');
        const hiddenInput = document.getElementById('selesai_bekerja');

        // Kalkulasi Total Biaya
        const totalHarga = hargaSatuan * qty;
        const formattedTotal = 'Rp ' + new Intl.NumberFormat('id-ID').format(totalHarga);
        
        document.getElementById('summary_calc_text').innerText = `${qty} x Rp ` + new Intl.NumberFormat('id-ID').format(hargaSatuan);
        document.getElementById('summary_subtotal').innerText = formattedTotal;
        document.getElementById('summary_total').innerText = formattedTotal;

        if (!startDateInput) {
            displayInput.value = 'Pilih tanggal mulai bekerja';
            hiddenInput.value = '';
            return;
        }

        const isMingguan = tipe.toLowerCase().includes('mingguan');
        const pengali = isMingguan ? 7 : 1;
        const totalHari = qty * pengali;

        const startDate = new Date(startDateInput);
        const endDate = new Date(startDate);
        endDate.setDate(startDate.getDate() + (totalHari - 1));

        const day = String(endDate.getDate()).padStart(2, '0');
        const month = String(endDate.getMonth() + 1).padStart(2, '0');
        const year = endDate.getFullYear();

        displayInput.value = `${day}/${month}/${year} (${totalHari} Hari Kerja)`;
        hiddenInput.value = endDate.toISOString().split('T')[0];
    }

    function resetToProfileAddress() {
        const defaultProfile = {
            no_hp: @json($buyer?->phone_number ?? ''),
            provinsi: @json($buyer?->provinsi ?? ''),
            kabupaten: @json($buyer?->kabupaten ?? ''),
            alamat: @json($buyer?->alamat_lengkap ?? '')
        };

        const hpEl = document.getElementById('no_hp');
        const provEl = document.getElementById('provinsi');
        const kabEl = document.getElementById('kabupaten');
        const alamatEl = document.getElementById('alamat_pemesan');

        if (hpEl) hpEl.value = defaultProfile.no_hp;
        if (provEl) provEl.value = defaultProfile.provinsi;
        if (kabEl) kabEl.value = defaultProfile.kabupaten;
        if (alamatEl) alamatEl.value = defaultProfile.alamat;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('orderForm');
        if (form) {
            form.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
                    e.preventDefault();
                    return false;
                }
            });
        }
        onTipePekerjaanChange();
    });
</script>
@endsection
