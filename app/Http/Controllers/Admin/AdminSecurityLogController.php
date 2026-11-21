<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SecurityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminSecurityLogController extends Controller
{
    /**
     * Display brute force & authentication security logs for administrators.
     */
    public function index(Request $request)
    {
        $since = now()->subHours(24);

        $totalFailed = SecurityLog::whereIn('event_type', ['failed_login', 'inactive_account'])
            ->where('created_at', '>=', $since)
            ->count();

        $totalBlocked = SecurityLog::where('event_type', 'rate_limited')
            ->where('created_at', '>=', $since)
            ->count();

        $suspiciousIps = SecurityLog::select(
            'ip_address',
            DB::raw('COUNT(*) as total_events'),
            DB::raw("SUM(CASE WHEN event_type IN ('failed_login', 'inactive_account') THEN 1 ELSE 0 END) as failed_count"),
            DB::raw("SUM(CASE WHEN event_type = 'rate_limited' THEN 1 ELSE 0 END) as blocked_count"),
            DB::raw('COUNT(DISTINCT email) as targeted_emails'),
            DB::raw('MAX(created_at) as last_seen_at')
        )
            ->where('created_at', '>=', $since)
            ->whereIn('event_type', ['failed_login', 'rate_limited', 'inactive_account'])
            ->groupBy('ip_address')
            ->havingRaw("SUM(CASE WHEN event_type IN ('failed_login', 'rate_limited', 'inactive_account') THEN 1 ELSE 0 END) >= 5")
            ->orderByDesc('total_events')
            ->limit(10)
            ->get();

        $query = SecurityLog::query()->latest('created_at')->latest('id');

        if ($request->filled('event_type')) {
            $query->where('event_type', $request->string('event_type')->toString());
        }

        if ($request->filled('ip_address')) {
            $query->where('ip_address', 'like', '%' . trim($request->string('ip_address')->toString()) . '%');
        }

        if ($request->filled('email')) {
            $query->where('email', 'like', '%' . strtolower(trim($request->string('email')->toString())) . '%');
        }

        $logs = $query->paginate(25)->withQueryString();

        return view('admin.security-logs.index', compact(
            'totalFailed',
            'totalBlocked',
            'suspiciousIps',
            'logs'
        ));
    }
}
