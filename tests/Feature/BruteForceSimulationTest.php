<?php

namespace Tests\Feature;

use App\Models\SecurityLog;
use App\Services\BruteForceMonitor;
use Illuminate\Foundation\Testing\TestCase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\RateLimiter;

class BruteForceSimulationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        foreach ([
            'database/migrations/0001_01_01_000000_create_users_table.php',
            'database/migrations/2026_10_17_075708_create_workers_table.php',
            'database/migrations/2026_11_02_000001_create_admins_table.php',
            'database/migrations/2026_11_18_000001_create_security_logs_table.php',
        ] as $migrationPath) {
            Artisan::call('migrate', ['--path' => $migrationPath]);
        }

        RateLimiter::clear('login:account:victim@example.com|127.0.0.1');
        RateLimiter::clear('login:account:admin-target@example.com|127.0.0.1');
        RateLimiter::clear('login:ip:127.0.0.1');
    }

    public function test_single_target_brute_force_on_user_login_is_logged_and_rate_limited(): void
    {
        $targetEmail = 'victim@example.com';

        for ($i = 1; $i <= BruteForceMonitor::MAX_ATTEMPTS_PER_ACCOUNT; $i++) {
            $response = $this->post('/login', [
                'email' => $targetEmail,
                'password' => "wrong-pass-{$i}",
                'role' => 'user',
            ]);

            $response->assertStatus(302);
        }

        // Attempt #6 and #7 should be blocked with 429 and logged as rate_limited
        for ($j = 1; $j <= 2; $j++) {
            $blocked = $this->post('/login', [
                'email' => $targetEmail,
                'password' => "blocked-pass-{$j}",
                'role' => 'user',
            ]);

            $blocked->assertStatus(429);
        }

        $this->assertEquals(
            BruteForceMonitor::MAX_ATTEMPTS_PER_ACCOUNT,
            SecurityLog::where('email', $targetEmail)->where('event_type', 'failed_login')->count()
        );

        $this->assertEquals(
            2,
            SecurityLog::where('email', $targetEmail)->where('event_type', 'rate_limited')->count()
        );
    }

    public function test_brute_force_on_admin_login_is_logged_and_rate_limited(): void
    {
        $adminEmail = 'admin-target@example.com';

        for ($i = 1; $i <= BruteForceMonitor::MAX_ATTEMPTS_PER_ACCOUNT; $i++) {
            $this->post('/admin/login', [
                'email' => $adminEmail,
                'password' => "guess-{$i}",
            ])->assertStatus(302);
        }

        $this->post('/admin/login', [
            'email' => $adminEmail,
            'password' => 'guess-final',
        ])->assertStatus(429);

        $this->assertDatabaseHas('security_logs', [
            'endpoint' => '/admin/login',
            'guard' => 'admin',
            'email' => $adminEmail,
            'event_type' => 'rate_limited',
            'reason' => 'account_rate_limit_exceeded',
        ]);
    }

    public function test_password_spraying_across_multiple_emails_triggers_ip_rate_limit(): void
    {
        for ($i = 1; $i <= BruteForceMonitor::MAX_ATTEMPTS_PER_IP; $i++) {
            $this->post('/login', [
                'email' => "spray{$i}@example.com",
                'password' => '123456',
                'role' => 'user',
            ])->assertStatus(302);
        }

        $this->post('/login', [
            'email' => 'spray-extra@example.com',
            'password' => '123456',
            'role' => 'user',
        ])->assertStatus(429);

        $this->assertDatabaseHas('security_logs', [
            'ip_address' => '127.0.0.1',
            'event_type' => 'rate_limited',
            'reason' => 'ip_rate_limit_exceeded',
        ]);
    }
}
