@extends('layouts.admin')

@section('title', 'Verifikasi Data Mitra ART')
@section('page_title', 'Verifikasi & Validasi Dokumen Mitra ART')

@section('content')
<div class="space-y-6 pt-2">

    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-black text-slate-900 tracking-tight">Antrean Verifikasi Berkas Mitra ART</h1>
            <p class="text-xs text-slate-500">Periksa keabsahan identitas KTP/NIK, foto, dan dokumen SKCK Kepolisian sebelum memberikan status terverifikasi.</p>
        </div>

        <div class="flex items-center space-x-2">
            <span class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                <span>{{ $countPending }} Menunggu Tindakan</span>
            </span>
        </div>
    </div>

    <!-- TABS STATUS VERIFIKASI -->
    <div class="flex items-center space-x-2 border-b border-slate-200/80 pb-2">
        <a href="{{ route('admin.workers.verify', ['filter' => 'pending']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-extrabold transition flex items-center space-x-2 {{ ($filter === 'pending' || $filter === 'belum_terverifikasi') ? 'bg-blue-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-200/60' }}">
            <span>⏳ Belum Terverifikasi</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ ($filter === 'pending' || $filter === 'belum_terverifikasi') ? 'bg-blue-800 text-white' : 'bg-slate-200 text-slate-700' }}">
                {{ $countPending }}
            </span>
        </a>

        <a href="{{ route('admin.workers.verify', ['filter' => 'sudah_terverifikasi']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-extrabold transition flex items-center space-x-2 {{ ($filter === 'sudah_terverifikasi' || $filter === 'terverifikasi') ? 'bg-emerald-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-200/60' }}">
            <span>✓ Sudah Terverifikasi</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ ($filter === 'sudah_terverifikasi' || $filter === 'terverifikasi') ? 'bg-emerald-800 text-white' : 'bg-slate-200 text-slate-700' }}">
                {{ $countVerified }}
            </span>
        </a>

        <a href="{{ route('admin.workers.verify', ['filter' => 'ditolak']) }}" 
           class="px-4 py-2 rounded-xl text-xs font-extrabold transition flex items-center space-x-2 {{ $filter === 'ditolak' ? 'bg-rose-600 text-white shadow-xs' : 'text-slate-600 hover:bg-slate-200/60' }}">
            <span>✗ Ditolak</span>
            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $filter === 'ditolak' ? 'bg-rose-800 text-white' : 'bg-slate-200 text-slate-700' }}">
                {{ $countRejected }}
            </span>
        </a>
    </div>

    <!-- SEARCH IN VERIFY -->
    <div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs flex items-center justify-between">
        <form method="GET" action="{{ route('admin.workers.verify') }}" class="flex items-center space-x-2 w-full max-w-md">
            <input type="hidden" name="filter" value="{{ $filter }}">
            <input type="text" 
                   name="search" 
                   value="{{ request('search') }}" 
                   placeholder="Cari nama, NIK, atau email..." 
                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 transition">
            <button type="submit" class="bg-blue-600 text-white font-bold text-xs px-4 py-2 rounded-xl hover:bg-blue-700 transition">
                Cari
            </button>
        </form>
    </div>

    <!-- WORKERS QUEUE CARDS -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @forelse($workers as $w)
            <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xs p-5 flex flex-col justify-between space-y-4 hover:border-slate-300 transition">
                
                <!-- TOP HEADER CARD -->
                <div class="space-y-3">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center space-x-3">
                            @if(!empty($w->foto_path))
                                <img src="{{ asset('storage/' . $w->foto_path) }}" alt="{{ $w->fullname }}" class="w-12 h-12 rounded-2xl object-cover border border-slate-200 flex-shrink-0 shadow-2xs">
                            @else
                                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-800 font-black text-base flex items-center justify-center flex-shrink-0 shadow-2xs">
                                    {{ strtoupper(substr($w->fullname, 0, 1)) }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <h3 class="text-sm font-extrabold text-slate-900 truncate">{{ $w->fullname }}</h3>
                                <span class="font-mono text-[10px] text-slate-500 bg-slate-100 border border-slate-200 px-1.5 py-0.2 rounded font-semibold">
                                    #WRK-{{ str_pad($w->id, 5, '0', STR_PAD_LEFT) }}
                                </span>
                            </div>
                        </div>

                        <!-- Status badge -->
                        @if(in_array(strtolower($w->status_data ?? ''), ['sudah terverifikasi', 'terverifikasi']))
                            <span class="text-[10px] font-extrabold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">✓ Sudah Terverifikasi</span>
                        @elseif(strtolower($w->status_data ?? '') === 'ditolak')
                            <span class="text-[10px] font-extrabold text-rose-700 bg-rose-100 px-2 py-0.5 rounded-full">✗ Ditolak</span>
                        @else
                            <span class="text-[10px] font-extrabold text-amber-800 bg-amber-100 px-2 py-0.5 rounded-full">⏳ Belum Terverifikasi</span>
                        @endif
                    </div>

                    <!-- DATA RINCIAN IDENTITAS -->
                    <div class="bg-slate-50 p-3.5 rounded-2xl border border-slate-200/70 space-y-1.5 text-xs">
                        <div class="flex justify-between">
                            <span class="text-slate-400">NIK:</span>
                            <span class="font-mono font-bold text-slate-800">{{ $w->nik }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">WhatsApp / HP:</span>
                            <span class="font-bold text-slate-800">{{ $w->phone_number }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Email:</span>
                            <span class="font-medium text-slate-700 truncate max-w-[170px]">{{ $w->email }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Domisili:</span>
                            <span class="font-semibold text-slate-700 truncate max-w-[170px]">{{ $w->kabupaten }}, {{ $w->provinsi }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-400">Layanan:</span>
                            <span class="font-bold text-blue-700">{{ $w->tipe_pekerjaan ?? 'Belum disetel' }}</span>
                        </div>
                    </div>

                    <!-- BERKAS SKCK KEPOLISIAN -->
                    <div class="border rounded-2xl p-3 {{ !empty($w->skck_path) ? 'border-emerald-200 bg-emerald-50/40' : 'border-amber-200 bg-amber-50/40' }}">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-slate-800">Dokumen SKCK Kepolisian:</span>
                            @if(!empty($w->skck_path))
                                <span class="text-[10px] font-extrabold text-emerald-700 bg-emerald-100 px-2 py-0.2 rounded-full">✓ Ada Berkas</span>
                            @else
                                <span class="text-[10px] font-extrabold text-amber-700 bg-amber-100 px-2 py-0.2 rounded-full">Belum Upload</span>
                            @endif
                        </div>

                        @if(!empty($w->skck_path))
                            <div class="pt-2">
                                <a href="{{ asset('storage/' . $w->skck_path) }}" 
                                   target="_blank" 
                                   class="inline-flex items-center space-x-1.5 text-xs font-extrabold text-blue-600 hover:text-blue-800 bg-white border border-blue-200 px-3 py-1.5 rounded-xl shadow-2xs w-full justify-center">
                                    <span>📄 Buka & Periksa Berkas SKCK ↗</span>
                                </a>
                            </div>
                        @else
                            <p class="text-[11px] text-amber-800 pt-1">
                                Mitra belum mengunggah dokumen SKCK pada formulir profil.
                            </p>
                        @endif
                    </div>

                    @if(!empty($w->description))
                        <p class="text-xs text-slate-500 line-clamp-2 italic">
                            "{{ $w->description }}"
                        </p>
                    @endif
                </div>

                <!-- FOOTER ACTIONS -->
                <div class="pt-3 border-t border-slate-100 space-y-2">
                    <div class="grid grid-cols-2 gap-2">
                        <!-- Setujui Verifikasi -->
                        <form action="{{ route('admin.workers.updateVerification', $w->id) }}" method="POST">
                            @csrf
                            <input type="hidden" name="status_data" value="sudah terverifikasi">
                            <button type="submit" 
                                    class="w-full py-2.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs transition shadow-xs flex items-center justify-center space-x-1">
                                <span>✓ Setujui (Terverifikasi)</span>
                            </button>
                        </form>

                        <!-- Tolak Verifikasi -->
                        <form action="{{ route('admin.workers.updateVerification', $w->id) }}" method="POST" onsubmit="return confirm('Tolak verifikasi berkas mitra ini?');">
                            @csrf
                            <input type="hidden" name="status_data" value="ditolak">
                            <button type="submit" 
                                    class="w-full py-2.5 px-3 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-extrabold text-xs transition flex items-center justify-center space-x-1">
                                <span>✗ Tolak</span>
                            </button>
                        </form>
                    </div>

                    <a href="{{ route('admin.workers.edit', $w->id) }}" 
                       class="block text-center py-2 text-xs font-bold text-slate-600 hover:text-blue-600 bg-slate-50 hover:bg-slate-100 rounded-xl transition border border-slate-200">
                        Edit Data Worker (ID Grey) &rarr;
                    </a>
                </div>

            </div>
        @empty
            <div class="col-span-full py-16 text-center bg-white rounded-3xl border border-slate-200 p-8 space-y-3">
                <span class="text-4xl">📭</span>
                <h3 class="text-sm font-extrabold text-slate-800">Tidak ada antrean dalam filter ini</h3>
                <p class="text-xs text-slate-500">Semua data mitra telah diproses atau tidak ditemukan hasil pencarian.</p>
            </div>
        @endforelse
    </div>

    @if($workers->hasPages())
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-xs">
            {{ $workers->links() }}
        </div>
    @endif

</div>
@endsection

