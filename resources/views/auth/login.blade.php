@extends('layouts.app')

@section('content')
<div class="min-h-[70vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full bg-white p-8 sm:p-10 rounded-3xl shadow-xl shadow-slate-200/50 border border-slate-200/80 space-y-6">
        
        <!-- HEADER -->
        <div class="text-center space-y-2">
            <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center font-extrabold text-xl mx-auto shadow-md shadow-blue-500/20">
                B
            </div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Selamat Datang Kembali</h1>
            <p class="text-xs text-slate-500">Pilih jenis akun untuk masuk ke platform MyBantu</p>
        </div>

        <!-- ROLE TAB SELECTOR (MAJIKAN vs MITRA ART) -->
        <div class="grid grid-cols-2 p-1 bg-slate-100 rounded-2xl border border-slate-200 text-xs font-bold text-slate-600" id="roleTabContainer">
            <button type="button" 
                    id="tabUser" 
                    onclick="switchRole('user')" 
                    class="py-2.5 rounded-xl transition duration-150 flex items-center justify-center space-x-1.5 {{ ($role ?? 'user') !== 'worker' ? 'bg-white text-blue-700 shadow-sm font-extrabold' : 'hover:text-slate-900' }}">
                <span>👨‍💼 Masuk Majikan</span>
            </button>
            <button type="button" 
                    id="tabWorker" 
                    onclick="switchRole('worker')" 
                    class="py-2.5 rounded-xl transition duration-150 flex items-center justify-center space-x-1.5 {{ ($role ?? 'user') === 'worker' ? 'bg-white text-emerald-700 shadow-sm font-extrabold' : 'hover:text-slate-900' }}">
                <span>🧹 Masuk Mitra ART</span>
            </button>
        </div>

        <!-- ERROR ALERT -->
        @if(isset($errors) && $errors->any())
            <div class="bg-rose-50 border border-rose-200 text-rose-700 px-4 py-3 rounded-2xl text-xs font-medium flex items-start space-x-2.5">
                <svg class="w-4 h-4 text-rose-500 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form action="{{ route('login') }}" method="POST" class="space-y-4">
            @csrf
            <input type="hidden" name="role" id="roleInput" value="{{ $role ?? 'user' }}">
            
            <!-- Email -->
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
                       required 
                       autofocus>
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wide">
                        Kata Sandi
                    </label>
                    <a href="{{ route('contact') }}" class="text-xs text-blue-600 hover:text-blue-700 font-semibold transition">
                        Bantuan Lupa Sandi?
                    </a>
                </div>
                <input type="password" 
                       name="password" 
                       id="password" 
                       placeholder="Masukkan kata sandi akun" 
                       class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition" 
                       required>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button type="submit" 
                        id="submitBtn" 
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white font-extrabold py-3.5 px-4 rounded-xl text-sm transition duration-150 shadow-md shadow-blue-600/25 flex items-center justify-center space-x-2">
                    <span id="btnText">Masuk sebagai Majikan &rarr;</span>
                </button>
            </div>
        </form>

        <!-- FOOTER LINKS (DUAL ROLE MOCKUP CARDS) -->
        <div class="pt-5 border-t border-slate-100 space-y-3">
            <p class="text-center text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                Belum Memiliki Akun di MyBantu?
            </p>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                <a href="{{ route('register') }}" 
                   class="flex items-center space-x-2.5 p-3 rounded-2xl bg-blue-50/70 hover:bg-blue-100/80 border border-blue-200/80 transition-all duration-150 group">
                    <div class="w-8 h-8 rounded-xl bg-blue-600 text-white flex items-center justify-center text-xs font-black shadow-xs group-hover:scale-105 transition-transform">
                        👨‍💼
                    </div>
                    <div class="text-left">
                        <span class="text-xs font-extrabold text-blue-900 block leading-tight">Daftar Majikan</span>
                        <span class="text-[10px] text-blue-600/90 block leading-tight">Pesan ART bergaransi</span>
                    </div>
                </a>

                <a href="{{ route('register.seller') }}" 
                   class="flex items-center space-x-2.5 p-3 rounded-2xl bg-emerald-50/70 hover:bg-emerald-100/80 border border-emerald-200/80 transition-all duration-150 group">
                    <div class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-xs font-black shadow-xs group-hover:scale-105 transition-transform">
                        🧹
                    </div>
                    <div class="text-left">
                        <span class="text-xs font-extrabold text-emerald-900 block leading-tight">Gabung Mitra ART</span>
                        <span class="text-[10px] text-emerald-600/90 block leading-tight">Raih upah bersih 85%</span>
                    </div>
                </a>
            </div>
            
            <div class="text-center pt-2">
                <a href="{{ route('admin.login') }}" class="inline-flex items-center space-x-1.5 text-[11px] font-bold text-slate-400 hover:text-slate-700 transition">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <span>Masuk Portal Administrator</span>
                </a>
            </div>
        </div>

    </div>
</div>

<script>
    function switchRole(role) {
        const roleInput = document.getElementById('roleInput');
        const tabUser = document.getElementById('tabUser');
        const tabWorker = document.getElementById('tabWorker');
        const submitBtn = document.getElementById('submitBtn');
        const btnText = document.getElementById('btnText');

        roleInput.value = role;

        if (role === 'worker') {
            tabWorker.className = "py-2.5 rounded-xl transition duration-150 flex items-center justify-center space-x-1.5 bg-white text-emerald-700 shadow-sm font-extrabold";
            tabUser.className = "py-2.5 rounded-xl transition duration-150 flex items-center justify-center space-x-1.5 text-slate-600 hover:text-slate-900";
            
            submitBtn.className = "w-full bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold py-3.5 px-4 rounded-xl text-sm transition duration-150 shadow-md shadow-emerald-600/25 flex items-center justify-center space-x-2";
            btnText.innerText = "Masuk sebagai Mitra ART →";
        } else {
            tabUser.className = "py-2.5 rounded-xl transition duration-150 flex items-center justify-center space-x-1.5 bg-white text-blue-700 shadow-sm font-extrabold";
            tabWorker.className = "py-2.5 rounded-xl transition duration-150 flex items-center justify-center space-x-1.5 text-slate-600 hover:text-slate-900";
            
            submitBtn.className = "w-full bg-blue-600 hover:bg-blue-700 text-white font-extrabold py-3.5 px-4 rounded-xl text-sm transition duration-150 shadow-md shadow-blue-600/25 flex items-center justify-center space-x-2";
            btnText.innerText = "Masuk sebagai Majikan →";
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const currentRole = document.getElementById('roleInput').value;
        switchRole(currentRole || 'user');
    });
</script>
@endsection
