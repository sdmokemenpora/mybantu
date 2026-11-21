<?php

namespace App\Services;

use App\Models\SecurityLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class BruteForceMonitor
{
    public const MAX_ATTEMPTS_PER_ACCOUNT = 5;
    public const MAX_ATTEMPTS_PER_IP = 15;
    public const DECAY_SECONDS = 60;

    /**
     * Check whether the current request exceeds account or IP rate limits.
     * Logs a rate_limited event and returns a 429 redirect response if blocked.
     */
    public function ensureIsNotRateLimited(Request $request, string $email, string $guard = 'web'): ?RedirectResponse
    {
        $accountKey = $this->accountKey($email, $request);
        $ipKey = $this->ipKey($request);

        $accountLimited = RateLimiter::tooManyAttempts($accountKey, self::MAX_ATTEMPTS_PER_ACCOUNT);
        $ipLimited = RateLimiter::tooManyAttempts($ipKey, self::MAX_ATTEMPTS_PER_IP);

        if (!$accountLimited && !$ipLimited) {
            return null;
        }

        $activeKey = $ipLimited ? $ipKey : $accountKey;
        $seconds = max(1, RateLimiter::availableIn($activeKey));
        $reason = $ipLimited ? 'ip_rate_limit_exceeded' : 'account_rate_limit_exceeded';
        $attempts = $ipLimited
            ? RateLimiter::attempts($ipKey)
            : RateLimiter::attempts($accountKey);

        $this->writeLog(
            $request,
            eventType: 'rate_limited',
            guard: $guard,
            email: $email,
            reason: $reason,
            attemptsInWindow: $attempts,
            level: 'warning',
        );

        return back()
            ->withErrors([
                'email' => "Terlalu banyak percobaan login. Silakan coba lagi dalam {$seconds} detik.",
            ])
            ->onlyInput('email')
            ->setStatusCode(429);
    }

    /**
     * Record a failed authentication attempt and increment rate limit counters.
     */
    public function recordFailedAttempt(
        Request $request,
        string $email,
        string $guard = 'web',
        string $reason = 'invalid_credentials',
        string $eventType = 'failed_login',
    ): void {
        $accountKey = $this->accountKey($email, $request);
        $ipKey = $this->ipKey($request);

        RateLimiter::hit($accountKey, self::DECAY_SECONDS);
        RateLimiter::hit($ipKey, self::DECAY_SECONDS);

        $attempts = max(
            RateLimiter::attempts($accountKey),
            RateLimiter::attempts($ipKey),
        );

        $this->writeLog(
            $request,
            eventType: $eventType,
            guard: $guard,
            email: $email,
            reason: $reason,
            attemptsInWindow: $attempts,
            level: 'warning',
        );
    }

    /**
     * Clear account rate limit on successful login and record audit trail.
     */
    public function recordSuccess(Request $request, string $email, string $guard = 'web'): void
    {
        RateLimiter::clear($this->accountKey($email, $request));

        $this->writeLog(
            $request,
            eventType: 'login_success',
            guard: $guard,
            email: $email,
            reason: 'authenticated',
            attemptsInWindow: 0,
            level: 'info',
        );
    }

    private function accountKey(string $email, Request $request): string
    {
        return 'login:account:' . Str::transliterate(Str::lower(trim($email))) . '|' . $request->ip();
    }

    private function ipKey(Request $request): string
    {
        return 'login:ip:' . $request->ip();
    }

    private function writeLog(
        Request $request,
        string $eventType,
        string $guard,
        string $email,
        string $reason,
        int $attemptsInWindow,
        string $level,
    ): void {
        $normalizedEmail = Str::lower(trim($email));
        $endpoint = '/' . ltrim($request->path(), '/');
        $ip = (string) $request->ip();
        $userAgent = Str::limit((string) $request->userAgent(), 500, '');

        SecurityLog::create([
            'event_type' => $eventType,
            'endpoint' => $endpoint,
            'guard' => $guard,
            'email' => $normalizedEmail,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'reason' => $reason,
            'attempts_in_window' => $attemptsInWindow,
            'created_at' => now(),
        ]);

        Log::channel('security')->{$level}("Auth event: {$eventType}", [
            'event_type' => $eventType,
            'endpoint' => $endpoint,
            'guard' => $guard,
            'email' => $normalizedEmail,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'reason' => $reason,
            'attempts_in_window' => $attemptsInWindow,
        ]);
    }
}
