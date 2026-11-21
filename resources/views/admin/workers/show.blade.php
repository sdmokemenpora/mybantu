@extends('layouts.admin')

@section('title', 'Detail Profil Worker - ' . $worker->fullname)
@section('page_title', 'Detail Profil & Riwayat Mitra Worker')

@section('content')
<div class="space-y-6 pt-2">

    <!-- BREADCRUMB & ACTIONS -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <a href="{{ route('admin.workers.index') }}" class="inline-flex items-center space-x-1 text-xs font-bold text-slate-500 hover:text-slate-800 transition">
            <span>&larr; Kembali ke Daftar Worker</span>
        </a>

        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.workers.edit', $worker->id) }}" class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition shadow-xs">
                <span>✏️ Edit Data (ID Grey)</span>
            </a>

            <form action="{{ route('admin.workers.destroy', $worker->id) }}" method="POST" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin menghapus mitra ART {{ $worker->fullname }} secara permanen?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold text-xs transition">
                    Hapus
                </button>
            </form>
        </div>
    </div>

    <!-- MAIN PROFILE HEADER CARD -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8 space-y-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-slate-100">
            <div class="flex items-center space-x-4">
                @if(!empty($worker->foto_path))
                    <img src="{{ asset('storage/' . $worker->foto_path) }}" alt="{{ $worker->fullname }}" class="w-20 h-20 rounded-3xl object-cover border-2 border-slate-200 shadow-md">
                @else
                    <div class="w-20 h-20 rounded-3xl bg-gradient-to-tr from-emerald-600 to-teal-500 text-white font-black text-3xl flex items-center justify-center shadow-md">
                        {{ strtoupper(substr($worker->fullname, 0, 1)) }}
                    </div>
                @endif
                <div class="space-y-1">
                    <div class="flex items-center space-x-2">
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">{{ $worker->fullname }}</h1>
                        <span class="font-mono text-xs font-bold text-slate-500 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-lg">
                            #WRK-{{ str_pad($worker->id, 5, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500">{{ $worker->email }} • {{ $worker->phone_number }}</p>
                    <div class="flex flex-wrap items-center gap-2 pt-1">
                        <!-- Status Keaktifan -->
                        <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold border {{ strtoupper($worker->status_worker) === 'AKTIF' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-slate-100 text-slate-600 border-slate-300' }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ strtoupper($worker->status_worker) === 'AKTIF' ? 'bg-emerald-500' : 'bg-slate-400' }}"></span>
                            <span>{{ strtoupper($worker->status_worker) }}</span>
                        </span>

                        <!-- Status Verifikasi Data -->
                        @if(in_array(strtolower($worker->status_data ?? ''), ['sudah terverifikasi', 'terverifikasi']))
                            <span class="text-[10px] font-extrabold text-blue-700 bg-blue-50 border border-blue-200 px-2.5 py-0.5 rounded-full">
                                ✓ Sudah Terverifikasi
                            </span>
                        @elseif(strtolower($worker->status_data ?? '') === 'ditolak')
                            <span class="text-[10px] font-extrabold text-rose-700 bg-rose-50 border border-rose-200 px-2.5 py-0.5 rounded-full">
                                ✗ Ditolak
                            </span>
                        @else
                            <span class="text-[10px] font-extrabold text-amber-800 bg-amber-50 border border-amber-200 px-2.5 py-0.5 rounded-full">
                                ⏳ Belum Terverifikasi
                            </span>
                        @endif

                        <span class="text-xs text-slate-400">Terdaftar sejak {{ $worker->created_at ? $worker->created_at->format('d M Y') : '-' }}</span>
                    </div>
                </div>
            </div>

            <!-- FAST VERIFY / TOGGLE BUTTONS -->
            <div class="flex items-center space-x-2">
                @if(!in_array(strtolower($worker->status_data ?? ''), ['sudah terverifikasi', 'terverifikasi']))
                    <form action="{{ route('admin.workers.updateVerification', $worker->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="status_data" value="sudah terverifikasi">
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs rounded-xl shadow-xs transition">
                            ✓ Setujui Verifikasi
                        </button>
                    </form>
                @endif

                <form action="{{ route('admin.workers.updateStatus', $worker->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="status_worker" value="{{ strtoupper($worker->status_worker) === 'AKTIF' ? 'NONAKTIF' : 'AKTIF' }}">
                    <button type="submit" class="px-4 py-2 border border-slate-300 hover:bg-slate-50 font-bold text-xs text-slate-700 rounded-xl transition">
                        Ubah Status Jadi {{ strtoupper($worker->status_worker) === 'AKTIF' ? 'NONAKTIF' : 'AKTIF' }}
                    </button>
                </form>
            </div>
        </div>

        <!-- STATS STRIP -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Pesanan Selesai</span>
                <p class="text-2xl font-black text-slate-900 mt-1">{{ $completedOrdersCount }}</p>
                <span class="text-[11px] text-slate-500">Transaksi berstatus selesai</span>
            </div>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Dana Bersih Dicairkan</span>
                <p class="text-2xl font-black text-emerald-700 mt-1">Rp {{ number_format($totalEarnings, 0, ',', '.') }}</p>
                <span class="text-[11px] text-emerald-600 font-semibold">Telah diterima mitra (85%)</span>
            </div>
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Ulasan & Rating</span>
                <div class="flex items-center space-x-1.5 mt-1">
                    <span class="text-2xl font-black text-amber-500">★ {{ number_format((float)$worker->ratings()->avg('rating'), 1) }}</span>
                    <span class="text-xs font-semibold text-slate-500">({{ $worker->ratings()->count() }} ulasan)</span>
                </div>
                <span class="text-[11px] text-slate-500">Dari majikan terdaftar</span>
            </div>
        </div>

        <!-- DETAILS GRID -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pt-2">
            
            <!-- BIODATA & LOKASI -->
            <div class="space-y-4">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">Data Identitas & Domisili</h3>
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-2.5 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-400">NIK KTP:</span>
                        <span class="font-mono font-bold text-slate-800">{{ $worker->nik }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Jenis Kelamin:</span>
                        <span class="font-bold text-slate-800">{{ $worker->sex === 'Female' ? 'Perempuan' : ($worker->sex === 'Male' ? 'Laki-laki' : '-') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Tanggal Lahir:</span>
                        <span class="font-bold text-slate-800">{{ $worker->birth_date ? \Carbon\Carbon::parse($worker->birth_date)->format('d F Y') : '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Provinsi Penugasan:</span>
                        <span class="font-bold text-slate-800">{{ $worker->provinsi }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Kabupaten / Kota:</span>
                        <span class="font-bold text-slate-800">{{ $worker->kabupaten }}</span>
                    </div>
                    <div class="pt-2 border-t border-slate-200">
                        <span class="text-slate-400 block mb-1">Alamat Lengkap:</span>
                        <p class="font-semibold text-slate-800">{{ $worker->alamat_lengkap }}</p>
                    </div>
                </div>

                @if(!empty($worker->description))
                    <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-1">
                        <span class="text-slate-400 text-xs font-bold">Deskripsi Profil Mitra:</span>
                        <p class="text-xs text-slate-700 leading-relaxed">{{ $worker->description }}</p>
                    </div>
                @endif
            </div>

            <!-- DOKUMEN & PENGATURAN HARGA -->
            <div class="space-y-4">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">Dokumen Berkas & Tarif Jasa</h3>

                <!-- Dokumen SKCK Card -->
                <div class="border rounded-2xl p-4 {{ !empty($worker->skck_path) ? 'border-emerald-200 bg-emerald-50/30' : 'border-amber-200 bg-amber-50/30' }} space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-800">Berkas SKCK Kepolisian:</span>
                        @if(!empty($worker->skck_path))
                            <span class="text-[10px] font-extrabold text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">✓ Ada Dokumen</span>
                        @else
                            <span class="text-[10px] font-extrabold text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full">Belum Diunggah</span>
                        @endif
                    </div>
                    @if(!empty($worker->skck_path))
                        <a href="{{ asset('storage/' . $worker->skck_path) }}" target="_blank" class="inline-flex items-center space-x-1.5 text-xs font-extrabold text-blue-600 hover:text-blue-800 bg-white border border-blue-200 px-3 py-2 rounded-xl shadow-2xs w-full justify-center">
                            <span>📄 Buka Dokumen SKCK Kepolisian (Tab Baru) &rarr;</span>
                        </a>
                    @else
                        <p class="text-[11px] text-amber-800">Mitra ART ini belum mengunggah berkas SKCK dari kepolisian.</p>
                    @endif
                </div>

                <!-- Tarif Jasa Layanan -->
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-3">
                    <span class="text-xs font-bold text-slate-800 block">Tarif Layanan Terdaftar:</span>
                    <div class="grid grid-cols-2 gap-3">
                        @forelse($worker->prices as $price)
                            <div class="bg-white p-3 rounded-xl border border-slate-200">
                                <span class="text-[10px] font-bold text-blue-600 uppercase">{{ $price->tipe_pekerjaan }}</span>
                                <p class="text-sm font-black text-slate-900 mt-0.5">Rp {{ number_format($price->harga, 0, ',', '.') }}</p>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 col-span-2">Belum ada pengaturan tarif.</p>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>

    </div>

    <!-- REVIEWS DARI MAJIKAN -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8 space-y-4">
        <h2 class="text-sm font-extrabold text-slate-900 uppercase tracking-wide border-b border-slate-100 pb-3">
            Ulasan & Penilaian Majikan ({{ $worker->ratings->count() }})
        </h2>

        <div class="space-y-3">
            @forelse($worker->ratings as $rating)
                <div class="p-3.5 rounded-2xl bg-slate-50/70 border border-slate-200/70 space-y-1 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="font-extrabold text-slate-900">{{ $rating->user->name ?? 'Majikan #' . $rating->user_id }}</span>
                        <div class="flex items-center space-x-1 text-amber-500 font-extrabold">
                            <span>★</span>
                            <span>{{ $rating->rating }}.0</span>
                        </div>
                    </div>
                    @if(!empty($rating->review))
                        <p class="text-slate-600 italic">"{{ $rating->review }}"</p>
                    @endif
                    <span class="text-[10px] text-slate-400 block pt-1">
                        {{ $rating->created_at ? $rating->created_at->format('d M Y, H:i') : '-' }} • Transaksi #{{ $rating->id_transaksi }}
                    </span>
                </div>
            @empty
                <p class="text-xs text-slate-400 text-center py-4">Belum ada ulasan untuk mitra ini.</p>
            @endforelse
        </div>
    </div>

</div>
@endsection

