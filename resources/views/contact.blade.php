@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-10">
    
    <!-- PAGE HEADER -->
    <div class="text-center max-w-2xl mx-auto space-y-2">
        <span class="inline-block bg-blue-50 text-blue-700 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
            Pusat Bantuan & Layanan Pelanggan
        </span>
        <h1 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">Hubungi Tim Layanan MyBantu</h1>
        <p class="text-xs sm:text-sm text-slate-500">
            Punya pertanyaan mengenai sistem pemesanan, verifikasi berkas, atau pengembalian dana Rekber? Kami siap membantu Anda.
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- KOLOM KIRI: Saluran Bantuan Langsung (5 Kolom) -->
        <div class="lg:col-span-5 space-y-6">
            
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-xs space-y-6">
                <h2 class="text-base font-extrabold text-slate-900">Saluran Kontak Resmi</h2>

                <div class="space-y-4 text-xs">
                    <div class="flex items-start space-x-3.5 p-3.5 rounded-2xl bg-slate-50 border border-slate-200/60">
                        <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-sm flex-shrink-0">
                            ✉️
                        </div>
                        <div>
                            <span class="font-bold text-slate-800 block text-xs">Email Customer Service</span>
                            <span class="text-slate-500 block mt-0.5">bantuan@mybantu.id</span>
                            <span class="text-[10px] text-slate-400">Respon dalam 1x24 jam kerja</span>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3.5 p-3.5 rounded-2xl bg-slate-50 border border-slate-200/60">
                        <div class="w-9 h-9 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm flex-shrink-0">
                            💬
                        </div>
                        <div>
                            <span class="font-bold text-slate-800 block text-xs">WhatsApp CS Hotline</span>
                            <span class="text-slate-500 block mt-0.5">+62 812-9900-1122</span>
                            <span class="text-[10px] text-emerald-600 font-semibold">Tersedia Setiap Hari: 08.00 - 21.00 WIB</span>
                        </div>
                    </div>

                    <div class="flex items-start space-x-3.5 p-3.5 rounded-2xl bg-slate-50 border border-slate-200/60">
                        <div class="w-9 h-9 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-sm flex-shrink-0">
                            🏢
                        </div>
                        <div>
                            <span class="font-bold text-slate-800 block text-xs">Kantor Operasional MyBantu</span>
                            <span class="text-slate-500 block mt-0.5">Gedung MyBantu Tower Lt. 5, Jl. Sudirman No. 45, Jakarta Selatan</span>
                        </div>
                    </div>
                </div>

                <div class="p-4 rounded-2xl bg-blue-50 border border-blue-200/80 text-xs text-blue-900 space-y-1">
                    <span class="font-bold block">🛡️ Bantuan Mediasi Rekber</span>
                    <p class="text-[11px] text-blue-700 leading-relaxed">
                        Jika Anda memiliki kendala terkait pesanan aktif atau butuh pengajuan pergantian mitra ART, cantumkan <strong>ID Transaksi</strong> Anda pada pesan agar dapat diprioritaskan.
                    </p>
                </div>
            </div>

        </div>

        <!-- KOLOM KANAN: Form Kirim Pesan (7 Kolom) -->
        <div class="lg:col-span-7">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-md space-y-6">
                
                <div>
                    <h2 class="text-lg font-extrabold text-slate-900">Kirimkan Pertanyaan atau Masukan</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Silakan isi formulir di bawah ini dan tim kami akan segera menghubungi Anda kembali.</p>
                </div>

                <!-- VALIDATION ERROR ALERT -->
                @if ($errors->any())
                    <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-2xl text-xs font-medium space-y-1">
                        <p class="font-bold">Periksa kembali data formulir:</p>
                        <ul class="list-disc pl-5 space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('contact.store') }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                            Nama Lengkap Anda
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               value="{{ old('name', Auth::check() ? Auth::user()->name : '') }}" 
                               placeholder="Contoh: Ahmad Fauzi" 
                               class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                               required>
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                            Alamat Email Aktif
                        </label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               value="{{ old('email', Auth::check() ? Auth::user()->email : '') }}" 
                               placeholder="nama@email.com" 
                               class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                               required>
                    </div>

                    <div>
                        <label for="message" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                            Pesan atau Kendala yang Dihadapi
                        </label>
                        <textarea name="message" 
                                  id="message" 
                                  rows="5" 
                                  placeholder="Jelaskan pertanyaan atau kendala Anda secara rinci (misal: terkait konfirmasi pembayaran transaksi #TRX...)" 
                                  class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                                  required>{{ old('message') }}</textarea>
                    </div>

                    <div class="pt-2">
                        <button type="submit" 
                                class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white font-extrabold py-3.5 px-8 rounded-xl text-sm transition duration-150 shadow-md shadow-blue-600/25 flex items-center justify-center space-x-2">
                            <span>Kirim Pesan Sekarang &rarr;</span>
                        </button>
                    </div>
                </form>

            </div>
        </div>

    </div>

</div>
@endsection
