@extends('layouts.admin')

@section('title', 'Detail Majikan - ' . $user->name)
@section('page_title', 'Detail Akun Majikan & Riwayat Order')

@section('content')
<div class="space-y-6 pt-2">

    <!-- BREADCRUMB & ACTIONS -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center space-x-1 text-xs font-bold text-slate-500 hover:text-slate-800 transition">
            <span>&larr; Kembali ke Daftar Majikan</span>
        </a>

        <div class="flex items-center space-x-2">
            <a href="{{ route('admin.users.edit', $user->id) }}" class="inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs transition shadow-xs">
                <span>✏️ Edit Data (ID Grey)</span>
            </a>

            <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('PERINGATAN: Hapus akun majikan ini beserta seluruh data transaksinya?');">
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
                @if(!empty($user->foto_path))
                    <img src="{{ asset('storage/' . $user->foto_path) }}" alt="{{ $user->name }}" class="w-20 h-20 rounded-3xl object-cover border-2 border-slate-200 shadow-md">
                @else
                    <div class="w-20 h-20 rounded-3xl bg-gradient-to-tr from-blue-600 to-indigo-600 text-white font-black text-3xl flex items-center justify-center shadow-md">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
                <div class="space-y-1">
                    <div class="flex items-center space-x-2">
                        <h1 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">{{ $user->name }}</h1>
                        <span class="font-mono text-xs font-bold text-slate-500 bg-slate-100 border border-slate-200 px-2 py-0.5 rounded-lg">
                            #USR-{{ str_pad($user->id, 5, '0', STR_PAD_LEFT) }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500">{{ $user->email }} • {{ $user->phone_number }}</p>
                    <div class="flex items-center space-x-2 pt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-700 border border-blue-200">
                            Akun Pelanggan Majikan
                        </span>
                        <span class="text-xs text-slate-400">Terdaftar sejak {{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- DETAILS & DOMISILI -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="space-y-4">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">Informasi Pribadi & Kontak</h3>
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-2.5 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-400">NIK KTP:</span>
                        <span class="font-mono font-bold text-slate-800">{{ $user->nik ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Nomor Telepon / WA:</span>
                        <span class="font-bold text-slate-800">{{ $user->phone_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Email:</span>
                        <span class="font-medium text-slate-700">{{ $user->email }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Jenis Kelamin:</span>
                        <span class="font-bold text-slate-800">{{ $user->sex === 'Female' ? 'Perempuan' : ($user->sex === 'Male' ? 'Laki-laki' : '-') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Tanggal Lahir:</span>
                        <span class="font-bold text-slate-800">{{ $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->format('d F Y') : '-' }}</span>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <h3 class="text-xs font-black uppercase tracking-wider text-slate-400">Alamat Tempat Tinggal</h3>
                <div class="bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-2.5 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-400">Provinsi:</span>
                        <span class="font-bold text-slate-800">{{ $user->provinsi ?? '-' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Kabupaten / Kota:</span>
                        <span class="font-bold text-slate-800">{{ $user->kabupaten ?? '-' }}</span>
                    </div>
                    <div class="pt-2 border-t border-slate-200">
                        <span class="text-slate-400 block mb-1">Alamat Lengkap Hunian:</span>
                        <p class="font-semibold text-slate-800">{{ $user->alamat_lengkap ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- RIWAYAT TRANSAKSI MAJIKAN -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs p-6 sm:p-8 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <h2 class="text-sm font-extrabold text-slate-900 uppercase tracking-wide">
                Riwayat Transaksi Pesanan ({{ $transactions->count() }})
            </h2>
            <span class="text-xs bg-blue-50 text-blue-700 font-bold px-3 py-1 rounded-full">
                Sistem Rekber
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="text-slate-400 uppercase tracking-wider border-b border-slate-100 text-[10px]">
                        <th class="py-3 px-3">Kode Order</th>
                        <th class="py-3 px-3">Mitra ART Dipesan</th>
                        <th class="py-3 px-3">Layanan</th>
                        <th class="py-3 px-3">Status Bayar</th>
                        <th class="py-3 px-3">Status Kerja</th>
                        <th class="py-3 px-3">Selesai</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="py-3 px-3 font-mono font-bold text-blue-600">
                                #{{ $trx->id_transaksi }}
                            </td>
                            <td class="py-3 px-3 font-bold text-slate-800">
                                @if($trx->worker)
                                    <a href="{{ route('admin.workers.show', $trx->worker->id) }}" class="hover:text-blue-600">
                                        {{ $trx->worker->fullname }}
                                    </a>
                                @else
                                    Worker #{{ $trx->worker_id }}
                                @endif
                            </td>
                            <td class="py-3 px-3">
                                <span class="bg-slate-100 text-slate-700 font-semibold px-2 py-0.5 rounded">
                                    {{ $trx->tipe_pekerjaan }}
                                </span>
                            </td>
                            <td class="py-3 px-3">
                                @if(strtolower($trx->status_pembayaran) === 'sudah bayar')
                                    <span class="bg-emerald-50 text-emerald-700 font-bold px-2 py-0.5 rounded-full border border-emerald-200">
                                        ✓ Sudah Bayar
                                    </span>
                                @else
                                    <span class="bg-amber-50 text-amber-700 font-bold px-2 py-0.5 rounded-full border border-amber-200">
                                        Belum Bayar
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-3">
                                {{ $trx->status_bekerja ?? '-' }}
                            </td>
                            <td class="py-3 px-3">
                                @if(strtolower($trx->status_selesai) === 'selesai')
                                    <span class="text-emerald-700 font-bold">✓ Selesai</span>
                                @else
                                    <span class="text-slate-400 font-medium">Dalam Proses</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-6 text-center text-slate-400">
                                Belum ada riwayat pesanan yang dibuat oleh majikan ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

