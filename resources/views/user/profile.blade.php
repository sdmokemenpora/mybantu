@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-8">

    <!-- PAGE HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-200/80 pb-6 gap-4">
        <div>
            <div class="inline-flex items-center space-x-2 bg-blue-50 text-blue-700 text-xs font-bold px-3 py-1 rounded-full mb-2">
                <span>👤 Akun Majikan</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">
                Pengaturan Profil Majikan
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 mt-1">
                Kelola informasi akun, kontak, dan alamat hunian Anda untuk kemudahan pemesanan asisten rumah tangga.
            </p>
        </div>

        <a href="{{ route('transaksi') }}" 
           class="inline-flex items-center text-xs font-bold text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 px-4 py-2.5 rounded-xl shadow-xs transition">
            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            Pesanan Saya
        </a>
    </div>

    <!-- USER SALDO CARD -->
    <div class="bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-800 rounded-3xl p-6 sm:p-7 text-white shadow-md flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center space-x-4">
            <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center text-3xl font-bold flex-shrink-0 border border-white/20 shadow-inner">
                💰
            </div>
            <div>
                <span class="text-xs font-bold text-emerald-200 uppercase tracking-wider">Saldo Akun MyBantu</span>
                <div class="text-2xl sm:text-3xl font-black text-white tracking-tight mt-0.5">
                    Rp {{ number_format($user->saldo ?? 0, 0, ',', '.') }}
                </div>
                <p class="text-xs text-emerald-100 mt-0.5">
                    Dana refund otomatis tersimpan di sini dan dapat Anda gunakan untuk memesan Mitra ART lainnya secara instan.
                </p>
            </div>
        </div>
        <a href="{{ route('product') }}" class="inline-flex items-center self-start sm:self-center text-xs font-extrabold text-emerald-950 bg-white hover:bg-emerald-50 px-4 py-2.5 rounded-xl shadow-xs transition">
            Gunakan Saldo untuk Pesan ART &rarr;
        </a>
    </div>

    <!-- ERROR ALERT -->
    @if ($errors->any())
        <div class="bg-rose-50 border border-rose-200 text-rose-700 p-4 rounded-2xl text-xs font-medium space-y-1">
            <p class="font-bold">Terdapat beberapa kesalahan pengisian formulir:</p>
            <ul class="list-disc pl-5 space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('user.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf

        <!-- CARD 1: FOTO & DATA IDENTITAS UTAMA (LOCKED / GREY) -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8 space-y-6">
            <h2 class="text-base font-extrabold text-slate-900 border-b border-slate-100 pb-3 flex items-center justify-between">
                <span>Foto Profil & Identitas Diri</span>
                <span class="text-[11px] font-normal text-slate-400">Identitas KTP terlindungi</span>
            </h2>

            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                <!-- Foto Profil Preview -->
                <div class="flex flex-col items-center space-y-2">
                    <div class="w-28 h-28 rounded-3xl overflow-hidden bg-slate-100 border-2 border-slate-200 flex items-center justify-center relative shadow-xs group">
                        @if(!empty($user->foto_path))
                            <img id="previewFoto" src="{{ asset('storage/' . $user->foto_path) }}" alt="{{ $user->name }}" class="w-full h-full object-cover">
                        @else
                            <div id="previewFallback" class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-black text-3xl">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                            <img id="previewFoto" src="" alt="Preview" class="w-full h-full object-cover hidden">
                        @endif
                    </div>
                    <span class="text-[10px] text-slate-400 font-medium">Foto Profil Majikan</span>
                </div>

                <div class="flex-1 w-full space-y-4">
                    <div>
                        <label for="fotoInput" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                            Ganti Foto Profil
                        </label>
                        <input type="file" 
                               name="foto" 
                               id="fotoInput" 
                               accept="image/png,image/jpeg,image/jpg" 
                               onchange="previewImage(event)" 
                               class="w-full text-xs text-slate-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-slate-300 rounded-xl p-1 bg-slate-50">
                        <p class="text-[11px] text-slate-400 mt-1">Format: JPG, JPEG, PNG (Maksimal 2MB).</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <!-- NAMA LENGKAP (GREY / READONLY) -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide">
                                    Nama Lengkap (Sesuai KTP)
                                </label>
                                <span class="text-[10px] bg-slate-200 text-slate-600 px-2 py-0.5 rounded font-bold">Terkunci</span>
                            </div>
                            <input type="text" 
                                   value="{{ $user->name }}" 
                                   disabled 
                                   readonly 
                                   class="w-full bg-slate-100 border border-slate-200 rounded-xl px-4 py-3 text-sm font-semibold text-slate-500 cursor-not-allowed shadow-inner" 
                                   title="Nama lengkap terdaftar sesuai KTP dan tidak dapat diubah">
                            <p class="text-[10px] text-slate-400 mt-1">Sesuai data verifikasi akun majikan</p>
                        </div>

                        <!-- NIK (GREY / READONLY) -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wide">
                                    Nomor Induk Kependudukan (NIK)
                                </label>
                                <span class="text-[10px] bg-slate-200 text-slate-600 px-2 py-0.5 rounded font-bold">Terkunci</span>
                            </div>
                            <input type="text" 
                                   value="{{ $user->nik ?? '-' }}" 
                                   disabled 
                                   readonly 
                                   class="w-full bg-slate-100 border border-slate-200 rounded-xl px-4 py-3 text-sm font-semibold text-slate-500 cursor-not-allowed shadow-inner" 
                                   title="NIK telah terdaftar resmi dan tidak dapat diubah">
                            <p class="text-[10px] text-slate-400 mt-1">Identitas resmi terverifikasi sistem</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD 2: KONTAK & ALAMAT DOMISILI (EDITABLE) -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8 space-y-6">
            <h2 class="text-base font-extrabold text-slate-900 border-b border-slate-100 pb-3">
                Kontak & Alamat Penugasan ART
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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

                <!-- No HP / WhatsApp -->
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
                <!-- Provinsi -->
                <div>
                    <label for="provinsi" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        Provinsi Domisili <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="provinsi" 
                           id="provinsi" 
                           value="{{ old('provinsi', $user->provinsi) }}" 
                           placeholder="Contoh: Jawa Timur" 
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                           required>
                </div>

                <!-- Kabupaten / Kota -->
                <div>
                    <label for="kabupaten" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        Kabupaten / Kota <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" 
                           name="kabupaten" 
                           id="kabupaten" 
                           value="{{ old('kabupaten', $user->kabupaten) }}" 
                           placeholder="Contoh: Surabaya" 
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                           required>
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
                          class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                          placeholder="Jalan, Nomor Rumah, RT/RW, Kecamatan, Patokan Lokasi" 
                          required>{{ old('alamat_lengkap', $user->alamat_lengkap) }}</textarea>
                <p class="text-[11px] text-slate-400 mt-1">Alamat ini akan otomatis digunakan sebagai lokasi kerja saat melakukan pemesanan asisten.</p>
            </div>
        </div>

        <!-- CARD 3: KEAMANAN / GANTI PASSWORD (OPTIONAL) -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8 space-y-6">
            <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900">Ubah Kata Sandi (Password)</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Kosongkan jika Anda tidak ingin mengubah kata sandi.</p>
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
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-700 uppercase tracking-wide mb-1.5">
                        Konfirmasi Kata Sandi Baru
                    </label>
                    <input type="password" 
                           name="password_confirmation" 
                           id="password_confirmation" 
                           placeholder="Ulangi kata sandi baru" 
                           class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
                </div>
            </div>
        </div>

        <!-- SUBMIT BUTTON -->
        <div class="flex items-center justify-end space-x-3 pt-2">
            <a href="{{ route('home') }}" 
               class="px-5 py-3 rounded-xl border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-50 transition">
                Batal
            </a>
            <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white font-extrabold px-6 py-3 rounded-xl text-xs transition duration-150 shadow-md shadow-blue-600/25 flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <span>Simpan Perubahan Profil</span>
            </button>
        </div>
    </form>

</div>

<script>
    function previewImage(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('previewFoto');
                const fallback = document.getElementById('previewFallback');
                img.src = e.target.result;
                img.classList.remove('hidden');
                if (fallback) fallback.classList.add('hidden');
            }
            reader.readAsDataURL(file);
        }
    }
</script>
@endsection

