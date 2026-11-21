@extends('layouts.admin')

@section('title', 'Dashboard Administrator')
@section('page_title', 'Dashboard & Ringkasan Operasional')

@section('content')
<div class="space-y-6 pt-2">

    <!-- WELCOME BANNER -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-950 rounded-3xl p-6 sm:p-8 text-white relative overflow-hidden shadow-lg border border-slate-800">
        <div class="relative z-10 max-w-2xl space-y-2">
            <div class="inline-flex items-center space-x-2 bg-blue-500/20 text-blue-300 border border-blue-400/30 text-xs font-bold px-3 py-1 rounded-full">
                <span class="w-2 h-2 rounded-full bg-blue-400 animate-ping"></span>
                <span>Panel Kontrol Administrator Aktif</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight">
                Selamat Datang, {{ Auth::guard('admin')->user()->name }}!
            </h1>
            <p class="text-xs sm:text-sm text-slate-300 leading-relaxed">
                Kelola data majikan, verifikasi berkas identitas mitra ART, pantau transaksi rekening bersama, dan pantau performa worker & majikan terbaik MyBantu.
            </p>
        </div>
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-blue-500/10 rounded-full blur-2xl pointer-events-none"></div>
    </div>

    <!-- METRIC CARDS STATISTIK -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        
        <!-- Card 1: Total Majikan -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Majikan</span>
                <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                    👥
                </div>
            </div>
            <div class="flex items-baseline space-x-2">
                <span class="text-3xl font-black text-slate-900">{{ number_format($totalUsers) }}</span>
                <span class="text-xs text-slate-500 font-semibold">Pengguna terdaftar</span>
            </div>
            <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 inline-flex items-center space-x-1 pt-1">
                <span>Kelola Data Majikan</span>
                <span>&rarr;</span>
            </a>
        </div>

        <!-- Card 2: Total Worker -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Mitra ART</span>
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    🧹
                </div>
            </div>
            <div class="flex items-baseline space-x-2">
                <span class="text-3xl font-black text-slate-900">{{ number_format($totalWorkers) }}</span>
                <span class="text-xs font-bold text-emerald-600">({{ $workersActive }} Aktif)</span>
            </div>
            <a href="{{ route('admin.workers.index') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-800 inline-flex items-center space-x-1 pt-1">
                <span>Kelola Data Worker</span>
                <span>&rarr;</span>
            </a>
        </div>

        <!-- Card 3: Menunggu Verifikasi -->
        <div class="bg-white rounded-3xl p-6 border {{ $workersPendingVerify > 0 ? 'border-amber-300 ring-2 ring-amber-400/20 bg-amber-50/20' : 'border-slate-200/80' }} shadow-xs space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Menunggu Verifikasi</span>
                <div class="w-10 h-10 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center font-bold">
                    🛡️
                </div>
            </div>
            <div class="flex items-baseline space-x-2">
                <span class="text-3xl font-black {{ $workersPendingVerify > 0 ? 'text-amber-600' : 'text-slate-900' }}">
                    {{ number_format($workersPendingVerify) }}
                </span>
                <span class="text-xs font-semibold text-slate-500">Mitra ART</span>
            </div>
            <a href="{{ route('admin.workers.verify') }}" class="text-xs font-bold text-amber-700 hover:text-amber-900 inline-flex items-center space-x-1 pt-1">
                <span>Buka Antrean Verifikasi</span>
                <span>&rarr;</span>
            </a>
        </div>

        <!-- Card 4: Transaksi Selesai & Fee Platform -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Transaksi Selesai</span>
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold">
                    💳
                </div>
            </div>
            <div class="flex items-baseline space-x-2">
                <span class="text-3xl font-black text-slate-900">{{ number_format($completedTransactions) }}</span>
                <span class="text-xs text-slate-500 font-semibold">dari {{ $totalTransactions }} total order</span>
            </div>
            <p class="text-[11px] font-bold text-indigo-700">
                Fee Platform: Rp {{ number_format($totalFeePlatform, 0, ',', '.') }}
            </p>
        </div>

    </div>

    <!-- QUICK ACTIONS BAR -->
    <div class="bg-white rounded-3xl p-4 sm:p-5 border border-slate-200/80 shadow-xs flex flex-wrap items-center justify-between gap-3">
        <div class="flex items-center space-x-2 text-xs font-bold text-slate-700">
            <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
            <span>Aksi Cepat Administrator:</span>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.workers.verify') }}" class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 font-bold text-xs transition shadow-xs">
                <span>🛡️ Verifikasi Worker ({{ $workersPendingVerify }})</span>
            </a>
            <a href="{{ route('admin.best.index') }}" class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs transition shadow-xs">
                <span>🏆 Worker & Majikan Terbaik</span>
            </a>
            <a href="{{ route('admin.admins.create') }}" class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition shadow-xs">
                <span>➕ Register Admin Baru</span>
            </a>
        </div>
    </div>

    <!-- TWO COLUMN: PENDING VERIFICATION & TOP PERFORMERS -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- KIRI: ANTREAN VERIFIKASI MITRA ART TERBARU -->
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <div class="flex items-center space-x-2">
                    <span class="w-3 h-3 rounded-md bg-amber-500"></span>
                    <h2 class="text-sm font-extrabold text-slate-900 uppercase tracking-wide">
                        Antrean Verifikasi Worker Terbaru
                    </h2>
                </div>
                <a href="{{ route('admin.workers.verify') }}" class="text-xs font-bold text-blue-600 hover:underline">
                    Lihat Semua &rarr;
                </a>
            </div>

            @if($pendingWorkers->isEmpty())
                <div class="text-center py-10 bg-slate-50/50 rounded-2xl border border-dashed border-slate-200 space-y-2">
                    <span class="text-3xl">🎉</span>
                    <p class="text-xs font-bold text-slate-700">Semua Data Mitra ART Sudah Terverifikasi!</p>
                    <p class="text-[11px] text-slate-400">Tidak ada worker yang membutuhkan verifikasi saat ini.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($pendingWorkers as $pw)
                        <div class="p-3.5 rounded-2xl border border-slate-200 bg-slate-50/60 flex items-center justify-between gap-3 hover:bg-slate-50 transition">
                            <div class="flex items-center space-x-3 min-w-0">
                                @if(!empty($pw->foto_path))
                                    <img src="{{ asset('storage/' . $pw->foto_path) }}" alt="{{ $pw->fullname }}" class="w-10 h-10 rounded-xl object-cover border border-slate-200 flex-shrink-0">
                                @else
                                    <div class="w-10 h-10 rounded-xl bg-slate-200 text-slate-600 font-extrabold text-sm flex items-center justify-center flex-shrink-0">
                                        {{ strtoupper(substr($pw->fullname, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <div class="flex items-center space-x-2">
                                        <p class="text-xs font-extrabold text-slate-900 truncate">{{ $pw->fullname }}</p>
                                        <span class="font-mono text-[10px] text-slate-500 bg-slate-200 px-1.5 py-0.2 rounded">
                                            #WRK-{{ str_pad($pw->id, 5, '0', STR_PAD_LEFT) }}
                                        </span>
                                    </div>
                                    <p class="text-[11px] text-slate-500 truncate">NIK: {{ $pw->nik }} • {{ $pw->kabupaten ?? 'Lokasi belum diatur' }}</p>
                                    <div class="flex items-center space-x-2 pt-1">
                                        @if(!empty($pw->skck_path))
                                            <span class="text-[10px] font-bold text-emerald-700 bg-emerald-100 px-2 py-0.2 rounded-md">✓ SKCK Ada</span>
                                        @else
                                            <span class="text-[10px] font-bold text-amber-700 bg-amber-100 px-2 py-0.2 rounded-md">Tanpa SKCK</span>
                                        @endif
                                        <span class="text-[10px] text-slate-400">{{ $pw->created_at ? $pw->created_at->diffForHumans() : '-' }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center space-x-1.5 flex-shrink-0">
                                <!-- Tombol Verifikasi Cepat -->
                                <form action="{{ route('admin.workers.updateVerification', $pw->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status_data" value="sudah terverifikasi">
                                    <button type="submit" 
                                            title="Setujui Verifikasi (Ubah jadi Sudah Terverifikasi)"
                                            class="px-2.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs transition shadow-2xs">
                                        ✓ Setujui
                                    </button>
                                </form>
                                <a href="{{ route('admin.workers.show', $pw->id) }}" 
                                   class="px-2.5 py-1.5 rounded-xl bg-white border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-100 transition shadow-2xs">
                                    Detail
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- KANAN: TOP PERFORMERS PREVIEW (WORKER & MAJIKAN) -->
        <div class="space-y-6">
            
            <!-- TOP 3 WORKER -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center space-x-2">
                        <span class="text-base">🏆</span>
                        <h2 class="text-sm font-extrabold text-slate-900 uppercase tracking-wide">
                            Mitra Worker Rating Tertinggi
                        </h2>
                    </div>
                    <a href="{{ route('admin.best.index') }}?tab=worker" class="text-xs font-bold text-blue-600 hover:underline">
                        Lihat Leaderboard &rarr;
                    </a>
                </div>

                <div class="space-y-2.5">
                    @forelse($topWorkers as $idx => $tw)
                        <div class="flex items-center justify-between p-3 rounded-2xl {{ $idx === 0 ? 'bg-amber-50/60 border border-amber-200/80' : 'bg-slate-50/70 border border-slate-200/70' }}">
                            <div class="flex items-center space-x-3 min-w-0">
                                <span class="w-6 h-6 rounded-full flex items-center justify-center font-black text-xs {{ $idx === 0 ? 'bg-amber-400 text-amber-950 shadow-xs' : ($idx === 1 ? 'bg-slate-300 text-slate-800' : 'bg-amber-700/30 text-amber-900') }}">
                                    {{ $idx + 1 }}
                                </span>
                                @if(!empty($tw->foto_path))
                                    <img src="{{ asset('storage/' . $tw->foto_path) }}" alt="{{ $tw->fullname }}" class="w-9 h-9 rounded-xl object-cover border border-slate-200">
                                @else
                                    <div class="w-9 h-9 rounded-xl bg-slate-200 text-slate-600 font-bold text-xs flex items-center justify-center">
                                        {{ strtoupper(substr($tw->fullname, 0, 1)) }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="text-xs font-extrabold text-slate-900 truncate">{{ $tw->fullname }}</p>
                                    <p class="text-[11px] text-slate-500 truncate">{{ $tw->kabupaten ?? 'Indonesia' }}</p>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <div class="flex items-center space-x-1 text-xs font-extrabold text-amber-600">
                                    <span>★</span>
                                    <span>{{ number_format((float)$tw->avg_rating, 1) }}</span>
                                </div>
                                <p class="text-[10px] text-slate-400 font-medium">({{ $tw->total_reviews }} ulasan)</p>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">Belum ada data ulasan worker.</p>
                    @endforelse
                </div>
            </div>

            <!-- TOP 3 MAJIKAN -->
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center space-x-2">
                        <span class="text-base">🌟</span>
                        <h2 class="text-sm font-extrabold text-slate-900 uppercase tracking-wide">
                            Majikan Paling Aktif (Top Bookers)
                        </h2>
                    </div>
                    <a href="{{ route('admin.best.index') }}?tab=user" class="text-xs font-bold text-blue-600 hover:underline">
                        Lihat Leaderboard &rarr;
                    </a>
                </div>

                <div class="space-y-2.5">
                    @forelse($topUsers as $idx => $tu)
                        <div class="flex items-center justify-between p-3 rounded-2xl bg-slate-50/70 border border-slate-200/70">
                            <div class="flex items-center space-x-3 min-w-0">
                                <span class="w-6 h-6 rounded-full flex items-center justify-center font-black text-xs bg-blue-100 text-blue-800">
                                    {{ $idx + 1 }}
                                </span>
                                <div class="min-w-0">
                                    <p class="text-xs font-extrabold text-slate-900 truncate">{{ $tu->name }}</p>
                                    <p class="text-[11px] text-slate-500 truncate">{{ $tu->email }}</p>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-extrabold bg-blue-50 text-blue-700 border border-blue-200">
                                    {{ $tu->total_orders }} Transaksi
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 text-center py-4">Belum ada data transaksi majikan.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

    <!-- TRANSAKSI TERAKHIR TABEL -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <h2 class="text-sm font-extrabold text-slate-900 uppercase tracking-wide">
                    Transaksi & Pesanan Terkini
                </h2>
                <p class="text-xs text-slate-400">Pantau pergerakan transaksi pesanan mitra ART dan pembayaran rekening bersama.</p>
            </div>
            <span class="text-xs bg-slate-100 text-slate-600 font-bold px-3 py-1 rounded-full">
                Live Data
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 uppercase tracking-wider border-b border-slate-100 text-[10px]">
                        <th class="py-3 px-3">Kode Order</th>
                        <th class="py-3 px-3">Majikan</th>
                        <th class="py-3 px-3">Mitra ART</th>
                        <th class="py-3 px-3">Layanan</th>
                        <th class="py-3 px-3">Status Bayar</th>
                        <th class="py-3 px-3">Status Kerja</th>
                        <th class="py-3 px-3">Selesai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentTransactions as $rt)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-3 font-mono font-bold text-blue-600">
                                #{{ $rt->id_transaksi }}
                            </td>
                            <td class="py-3 px-3 font-bold text-slate-800">
                                {{ $rt->user->name ?? 'User #' . $rt->user_id }}
                            </td>
                            <td class="py-3 px-3 font-bold text-slate-800">
                                {{ $rt->worker->fullname ?? 'Worker #' . $rt->worker_id }}
                            </td>
                            <td class="py-3 px-3">
                                <span class="bg-slate-100 text-slate-700 font-semibold px-2 py-0.5 rounded">
                                    {{ $rt->tipe_pekerjaan }}
                                </span>
                            </td>
                            <td class="py-3 px-3">
                                @if(strtolower($rt->status_pembayaran) === 'sudah bayar')
                                    <span class="bg-emerald-50 text-emerald-700 font-bold px-2 py-0.5 rounded-full border border-emerald-200">
                                        ✓ Sudah Bayar
                                    </span>
                                @else
                                    <span class="bg-amber-50 text-amber-700 font-bold px-2 py-0.5 rounded-full border border-amber-200">
                                        Menunggu Bayar
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-3">
                                @if($rt->status_bekerja === 'sedang bekerja')
                                    <span class="text-blue-700 font-bold bg-blue-50 px-2 py-0.5 rounded-full">Sedang Bekerja</span>
                                @elseif($rt->status_bekerja === 'menunggu bekerja')
                                    <span class="text-amber-700 font-bold bg-amber-50 px-2 py-0.5 rounded-full">Menunggu</span>
                                @else
                                    <span class="text-slate-400 font-medium">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-3">
                                @if(strtolower($rt->status_selesai) === 'selesai')
                                    <span class="text-emerald-700 font-bold">✓ Selesai</span>
                                @else
                                    <span class="text-slate-400 font-medium">Dalam Proses</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-slate-400">
                                Belum ada riwayat transaksi.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

