<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrator - MyBantu Platform</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="h-full bg-slate-950 text-slate-100 flex items-center justify-center p-4 relative overflow-hidden">
    
    <!-- Glow backgrounds -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">
        
        <!-- CARD LOGIN -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-8 sm:p-10 shadow-2xl backdrop-blur-xl space-y-6">
            
            <!-- HEADER -->
            <div class="text-center space-y-2">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-blue-600 to-indigo-600 flex items-center justify-center text-white font-black text-2xl mx-auto shadow-lg shadow-blue-500/30">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-black text-white tracking-tight">Portal Administrator</h1>
                    <p class="text-xs text-slate-400 mt-1">Sistem Kontrol & Verifikasi Operasional MyBantu</p>
                </div>
            </div>

            <!-- ALERT NOTIFICATIONS -->
            @if(session('success'))
                <div class="bg-emerald-950/80 border border-emerald-800 text-emerald-300 px-4 py-3 rounded-2xl text-xs font-semibold">
                    {{ session('success') }}
                </div>
            @endif

            @if(isset($errors) && $errors->any())
                <div class="bg-rose-950/80 border border-rose-800 text-rose-300 px-4 py-3 rounded-2xl text-xs font-semibold flex items-start space-x-2">
                    <svg class="w-4 h-4 text-rose-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <!-- FORM LOGIN -->
            <form action="{{ route('admin.login.post') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                        Email Administrator
                    </label>
                    <input type="email" 
                           name="email" 
                           id="email" 
                           value="{{ old('email', 'admin@mybantu.id') }}" 
                           placeholder="admin@mybantu.id" 
                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition font-medium" 
                           required 
                           autofocus>
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-slate-300 uppercase tracking-wider mb-2">
                        Password
                    </label>
                    <input type="password" 
                           name="password" 
                           id="password" 
                           placeholder="••••••••" 
                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition font-medium" 
                           required>
                </div>

                <div class="flex items-center justify-between text-xs text-slate-400 pt-1">
                    <label class="inline-flex items-center space-x-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded bg-slate-950 border-slate-700 text-blue-600 focus:ring-blue-500 focus:ring-offset-slate-900">
                        <span>Ingat sesi saya</span>
                    </label>
                    <span class="text-[11px] text-slate-500">Akses Terenkripsi 256-bit</span>
                </div>

                <div class="pt-2">
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500 text-white font-extrabold py-3.5 px-4 rounded-xl text-sm transition duration-150 shadow-lg shadow-blue-600/30 flex items-center justify-center space-x-2">
                        <span>Masuk ke Panel Kontrol</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </button>
                </div>
            </form>

            <!-- DEMO CREDENTIAL INFO -->
            <div class="p-3.5 rounded-2xl bg-slate-950/80 border border-slate-800 text-xs text-slate-400 space-y-1">
                <div class="flex items-center justify-between text-[11px] font-bold text-slate-300">
                    <span>🔑 Kredensial Akses Default:</span>
                    <span class="bg-blue-900/60 text-blue-300 px-1.5 py-0.5 rounded text-[10px] uppercase font-mono">Super Admin</span>
                </div>
                <div class="font-mono text-[11px] text-slate-300 pt-0.5">
                    Email: <span class="text-blue-400 font-semibold">admin@mybantu.id</span> <br>
                    Password: <span class="text-blue-400 font-semibold">admin123</span>
                </div>
            </div>

            <!-- BACK TO SITE LINK -->
            <div class="text-center pt-2 border-t border-slate-800/80">
                <a href="{{ route('home') }}" class="inline-flex items-center space-x-1.5 text-xs font-semibold text-slate-400 hover:text-white transition">
                    <span>&larr; Kembali ke Beranda MyBantu</span>
                </a>
            </div>

        </div>

    </div>

</body>
</html>

