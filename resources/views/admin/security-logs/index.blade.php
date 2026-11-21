@extends('layouts.admin')

@section('title', 'Monitoring Keamanan & Brute Force')
@section('page_title', 'Monitoring Keamanan & Log Brute Force')

@section('content')
<div class="space-y-6 pt-2">

    <!-- METRIC CARDS -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6">
        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Gagal Login (24 Jam)</span>
                <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                    !
                </div>
            </div>
            <div class="flex items-baseline space-x-2">
                <span class="text-3xl font-black text-slate-900">{{ number_format($totalFailed) }}</span>
                <span class="text-xs text-slate-500 font-semibold">percobaan gagal</span>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Diblokir Rate Limit (24 Jam)</span>
                <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                    429
                </div>
            </div>
            <div class="flex items-baseline space-x-2">
                <span class="text-3xl font-black text-rose-600">{{ number_format($totalBlocked) }}</span>
                <span class="text-xs text-slate-500 font-semibold">request ditahan</span>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">IP Terindikasi Brute Force</span>
                <div class="w-10 h-10 rounded-2xl bg-slate-900 text-white flex items-center justify-center font-bold">
                    IP
                </div>
            </div>
            <div class="flex items-baseline space-x-2">
                <span class="text-3xl font-black text-slate-900">{{ $suspiciousIps->count() }}</span>
                <span class="text-xs text-slate-500 font-semibold">IP (>= 5x gagal / 24j)</span>
            </div>
        </div>
    </div>

    <!-- TOP SUSPICIOUS IPS -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h2 class="text-lg font-black text-slate-900">IP Terindikasi Serangan Brute Force (24 Jam Terakhir)</h2>
                <p class="text-xs text-slate-500">Daftar IP dengan minimal 5 percobaan gagal atau pemblokiran rate limit.</p>
            </div>
        </div>

        @if($suspiciousIps->isEmpty())
            <div class="rounded-2xl bg-slate-50 border border-slate-200/70 p-4 text-sm text-slate-500">
                Belum ada IP yang melewati ambang batas indikasi brute force dalam 24 jam terakhir.
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-[11px] uppercase tracking-wider text-slate-400">
                            <th class="py-3 pr-4">IP Address</th>
                            <th class="py-3 pr-4">Status</th>
                            <th class="py-3 pr-4">Gagal</th>
                            <th class="py-3 pr-4">Diblokir (429)</th>
                            <th class="py-3 pr-4">Target Email Unik</th>
                            <th class="py-3">Aktivitas Terakhir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($suspiciousIps as $ip)
                            <tr>
                                <td class="py-3 pr-4 font-mono font-bold text-slate-900">{{ $ip->ip_address }}</td>
                                <td class="py-3 pr-4">
                                    @if($ip->blocked_count > 0)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-100 text-rose-700">
                                            BRUTE FORCE BLOCKED
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                            SUSPICIOUS
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 pr-4 font-semibold text-amber-700">{{ $ip->failed_count }}</td>
                                <td class="py-3 pr-4 font-semibold text-rose-700">{{ $ip->blocked_count }}</td>
                                <td class="py-3 pr-4 text-slate-600">{{ $ip->targeted_emails }} email</td>
                                <td class="py-3 text-xs text-slate-500">{{ \Carbon\Carbon::parse($ip->last_seen_at)->format('d M Y H:i:s') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- FILTER & DETAIL LOGS -->
    <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-xs space-y-4">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h2 class="text-lg font-black text-slate-900">Audit Trail Autentikasi</h2>
                <p class="text-xs text-slate-500">Log mentah juga tersimpan di <code class="px-1.5 py-0.5 bg-slate-100 rounded">storage/logs/security-*.log</code>.</p>
            </div>

            <form method="GET" action="{{ route('admin.security-logs.index') }}" class="flex flex-wrap items-center gap-2">
                <select name="event_type" class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 bg-white">
                    <option value="">Semua Event</option>
                    <option value="failed_login" @selected(request('event_type') === 'failed_login')>failed_login</option>
                    <option value="rate_limited" @selected(request('event_type') === 'rate_limited')>rate_limited (429)</option>
                    <option value="inactive_account" @selected(request('event_type') === 'inactive_account')>inactive_account</option>
                    <option value="login_success" @selected(request('event_type') === 'login_success')>login_success</option>
                </select>
                <input type="text" name="ip_address" value="{{ request('ip_address') }}" placeholder="Filter IP..." class="rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-700">
                <input type="text" name="email" value="{{ request('email') }}" placeholder="Filter email..." class="rounded-xl border border-slate-200 px-3 py-2 text-xs text-slate-700">
                <button type="submit" class="rounded-xl bg-slate-900 text-white px-4 py-2 text-xs font-bold hover:bg-slate-800 transition">Filter</button>
                @if(request()->hasAny(['event_type', 'ip_address', 'email']))
                    <a href="{{ route('admin.security-logs.index') }}" class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 hover:bg-slate-50">Reset</a>
                @endif
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs sm:text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-[11px] uppercase tracking-wider text-slate-400">
                        <th class="py-3 pr-4">Waktu</th>
                        <th class="py-3 pr-4">Event</th>
                        <th class="py-3 pr-4">IP Address</th>
                        <th class="py-3 pr-4">Endpoint / Guard</th>
                        <th class="py-3 pr-4">Target Email</th>
                        <th class="py-3 pr-4">Percobaan</th>
                        <th class="py-3 pr-4">Alasan</th>
                        <th class="py-3">User-Agent</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr>
                            <td class="py-3 pr-4 whitespace-nowrap font-mono text-xs text-slate-500">
                                {{ $log->created_at?->format('Y-m-d H:i:s') }}
                            </td>
                            <td class="py-3 pr-4 whitespace-nowrap">
                                @php
                                    $badgeClass = match($log->event_type) {
                                        'rate_limited' => 'bg-rose-100 text-rose-700',
                                        'failed_login', 'inactive_account' => 'bg-amber-100 text-amber-800',
                                        'login_success' => 'bg-emerald-100 text-emerald-700',
                                        default => 'bg-slate-100 text-slate-700',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $badgeClass }}">
                                    {{ $log->event_type }}
                                </span>
                            </td>
                            <td class="py-3 pr-4 font-mono text-xs font-semibold text-slate-800">{{ $log->ip_address }}</td>
                            <td class="py-3 pr-4 whitespace-nowrap text-xs text-slate-600">
                                <span class="font-mono">{{ $log->endpoint }}</span>
                                <span class="text-slate-400">({{ $log->guard }})</span>
                            </td>
                            <td class="py-3 pr-4 text-xs font-medium text-slate-800">{{ $log->email ?? '-' }}</td>
                            <td class="py-3 pr-4 text-xs font-bold text-slate-700">{{ $log->attempts_in_window }}</td>
                            <td class="py-3 pr-4 text-xs text-slate-600">{{ $log->reason ?? '-' }}</td>
                            <td class="py-3 text-xs text-slate-500 max-w-xs truncate" title="{{ $log->user_agent }}">
                                {{ $log->user_agent ?: '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-sm text-slate-400">
                                Belum ada log keamanan yang tercatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            {{ $logs->links() }}
        </div>
    </div>

</div>
@endsection
