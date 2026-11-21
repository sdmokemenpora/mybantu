@extends('layouts.app')

@section('content')
<div class="space-y-16 pb-20">
    
    <!-- HERO SECTION ABOUT -->
    <section class="bg-gradient-to-b from-slate-900 to-slate-800 text-white py-16 sm:py-20 px-4 sm:px-6 lg:px-8 border-b border-slate-800">
        <div class="max-w-4xl mx-auto text-center space-y-4">
            <span class="inline-block bg-blue-500/20 text-blue-300 border border-blue-400/30 text-xs font-bold px-3.5 py-1.5 rounded-full uppercase tracking-wider">
                Misi & Nilai Platform MyBantu
            </span>
            <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight leading-tight">
                Menciptakan Ekosistem Jasa ART yang Amanah, Transparan, & Adil
            </h1>
            <p class="text-slate-300 text-sm sm:text-base leading-relaxed max-w-2xl mx-auto font-normal">
                MyBantu hadir untuk menyelesaikan permasalahan ketidakpastian dan risiko keamanan dalam mencari Asisten Rumah Tangga, melalui standardisasi verifikasi berkas dan sistem Rekening Bersama bergaransi.
            </p>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
        
        <!-- BAGIAN 1: CARA KERJA REKBER MYBANTU -->
        <div class="bg-white rounded-3xl p-8 sm:p-12 shadow-xs border border-slate-200/80 space-y-10">
            <div class="text-center max-w-2xl mx-auto space-y-2">
                <span class="text-xs font-bold text-blue-600 uppercase tracking-wider bg-blue-50 px-3 py-1 rounded-md">Proteksi Finansial</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900">Sistem Rekening Bersama (Rekber) MyBantu</h2>
                <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                    Aman bagi Majikan, Adil bagi Pekerja. Seluruh pembayaran ditahan dalam rekening penampung resmi hingga pekerjaan terkonfirmasi selesai.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Step 1 -->
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200/80 space-y-3 relative group hover:border-blue-300 transition duration-150">
                    <div class="w-10 h-10 bg-blue-600 text-white font-black rounded-xl flex items-center justify-center shadow-md shadow-blue-600/20 text-sm">
                        1
                    </div>
                    <h3 class="font-extrabold text-sm text-slate-900">Pemesanan & Transfer Rekber</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Majikan menentukan jadwal harian atau mingguan, lalu mentransfer biaya ke Virtual Account Rekber MyBantu.
                    </p>
                </div>

                <!-- Step 2 -->
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200/80 space-y-3 relative group hover:border-blue-300 transition duration-150">
                    <div class="w-10 h-10 bg-blue-600 text-white font-black rounded-xl flex items-center justify-center shadow-md shadow-blue-600/20 text-sm">
                        2
                    </div>
                    <h3 class="font-extrabold text-sm text-slate-900">Verifikasi & Penugasan</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Sistem memverifikasi dana secara otomatis. Mitra ART menerima notifikasi jadwal dan bersiap hadir ke lokasi Anda.
                    </p>
                </div>

                <!-- Step 3 -->
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200/80 space-y-3 relative group hover:border-blue-300 transition duration-150">
                    <div class="w-10 h-10 bg-blue-600 text-white font-black rounded-xl flex items-center justify-center shadow-md shadow-blue-600/20 text-sm">
                        3
                    </div>
                    <h3 class="font-extrabold text-sm text-slate-900">Pekerja Bertugas & Evaluasi</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Mitra ART menyelesaikan seluruh kewajiban pekerjaan kerumahtanggaan. Majikan mengevaluasi kualitas hasil kerja.
                    </p>
                </div>

                <!-- Step 4 -->
                <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200/80 space-y-3 relative group hover:border-emerald-300 transition duration-150">
                    <div class="w-10 h-10 bg-emerald-600 text-white font-black rounded-xl flex items-center justify-center shadow-md shadow-emerald-600/20 text-sm">
                        4
                    </div>
                    <h3 class="font-extrabold text-sm text-slate-900">Konfirmasi & Pencairan Hak</h3>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        Setelah majikan menekan konfirmasi selesai, dana rekber langsung diteruskan ke saldo mitra ART tanpa potongan komisi tersembunyi.
                    </p>
                </div>
            </div>
        </div>

        <!-- BAGIAN 2: TIGA PILAR UTAMA MYBANTU -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-black">
                    🛡️
                </div>
                <h3 class="text-base font-extrabold text-slate-900">Garansi Penggantian Mitra</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Apabila dalam 24 jam pertama kerja Anda merasa mitra kurang cocok atau berhalangan hadir mendesak, tim MyBantu siap mencarikan pengganti tanpa biaya tambahan.
                </p>
            </div>

            <div class="bg-white p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-black">
                    📄
                </div>
                <h3 class="text-base font-extrabold text-slate-900">Verifikasi Dokumen Resmi</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Setiap pekerja diwajibkan mengunggah data NIK, KTP, dan surat keterangan catatan kepolisian (SKCK) untuk menjamin rasa aman hunian Anda dan keluarga.
                </p>
            </div>

            <div class="bg-white p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-3">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-xl font-black">
                    💰
                </div>
                <h3 class="text-base font-extrabold text-slate-900">Transparansi Tarif Jasa</h3>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Semua harga tercantum dengan jelas sebelum Anda memesan. Tidak ada biaya perantara liar atau markup sepihak.
                </p>
            </div>
        </div>

        <!-- BAGIAN 3: CALL TO ACTION GABUNG MITRA ART -->
        <div class="bg-gradient-to-br from-blue-900 via-indigo-950 to-slate-900 rounded-3xl p-8 sm:p-12 text-white shadow-xl relative overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <div class="lg:col-span-7 space-y-4">
                    <span class="bg-emerald-500/20 text-emerald-300 border border-emerald-400/30 text-xs font-bold px-3 py-1 rounded-full uppercase">
                        Karir & Kemitraan
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold leading-tight">
                        Punya Keahlian Mengelola Rumah? Bergabung Menjadi Mitra Resmi MyBantu
                    </h2>
                    <p class="text-slate-300 text-xs sm:text-sm leading-relaxed max-w-xl">
                        Dapatkan jam kerja yang fleksibel, perlindungan kepastian bayaran tepat waktu lewat Rekber, serta perlindungan hukum dan keamanan selama bertugas.
                    </p>

                    <div class="pt-2">
                        <a href="{{ route('register.seller') }}" 
                           class="inline-block bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-extrabold px-6 py-3.5 rounded-xl text-sm transition duration-150 shadow-lg shadow-emerald-500/25">
                            Daftar Mitra ART Sekarang &rarr;
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-5 grid grid-cols-1 sm:grid-cols-2 gap-3 text-slate-900">
                    <div class="bg-white/95 backdrop-blur-xs p-4 rounded-2xl shadow-xs">
                        <span class="text-emerald-600 font-black text-sm block mb-1">✓ Jaminan Gaji Cair</span>
                        <p class="text-[11px] text-slate-600">Upah kerja Anda pasti aman dan tersimpan di Rekber resmi.</p>
                    </div>

                    <div class="bg-white/95 backdrop-blur-xs p-4 rounded-2xl shadow-xs">
                        <span class="text-emerald-600 font-black text-sm block mb-1">✓ Jadwal Fleksibel</span>
                        <p class="text-[11px] text-slate-600">Pilih sendiri apakah ingin mengambil kerja harian atau mingguan.</p>
                    </div>

                    <div class="bg-white/95 backdrop-blur-xs p-4 rounded-2xl shadow-xs">
                        <span class="text-emerald-600 font-black text-sm block mb-1">✓ Bebas Potongan Liar</span>
                        <p class="text-[11px] text-slate-600">Hasil keringat Anda 100% menjadi hak Anda.</p>
                    </div>

                    <div class="bg-white/95 backdrop-blur-xs p-4 rounded-2xl shadow-xs">
                        <span class="text-emerald-600 font-black text-sm block mb-1">✓ Dukungan CS 24/7</span>
                        <p class="text-[11px] text-slate-600">Tim kami siap mendampingi jika ada kendala di lapangan.</p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
