<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BruteForceMonitor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function __construct(
        private readonly BruteForceMonitor $bruteForceMonitor,
    ) {
    }

    /**
     * Tampilkan form login khusus Administrator
     */
    public function showLogin()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    /**
     * Proses autentikasi admin
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->boolean('remember');
        $email = strtolower(trim($credentials['email']));

        if ($blocked = $this->bruteForceMonitor->ensureIsNotRateLimited($request, $email, 'admin')) {
            return $blocked;
        }

        if (Auth::guard('admin')->attempt(['email' => $email, 'password' => $credentials['password']], $remember)) {
            $admin = Auth::guard('admin')->user();

            // Cek status aktif
            if (!$admin->isActive()) {
                Auth::guard('admin')->logout();
                $this->bruteForceMonitor->recordFailedAttempt($request, $email, 'admin', 'inactive_admin', 'inactive_account');
                return back()->withErrors([
                    'email' => 'Akun admin Anda berstatus NONAKTIF. Hubungi Super Admin untuk mengaktifkan kembali.',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();
            $this->bruteForceMonitor->recordSuccess($request, $email, 'admin');

            // Cek jika ada URL intended yang memang mengarah ke area admin
            $intended = $request->session()->get('url.intended');
            if ($intended && str_contains($intended, '/admin')) {
                return redirect()->intended(route('admin.dashboard'))
                    ->with('success', 'Selamat datang kembali, ' . $admin->name . ' (' . $admin->role . ')!');
            }

            return redirect()->route('admin.dashboard')
                ->with('success', 'Selamat datang kembali, ' . $admin->name . ' (' . $admin->role . ')!');
        }

        $this->bruteForceMonitor->recordFailedAttempt($request, $email, 'admin');

        return back()->withErrors([
            'email' => 'Kombinasi email atau password administrator tidak valid.',
        ])->onlyInput('email');
    }

    /**
     * Logout admin
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('success', 'Anda telah berhasil keluar dari Panel Administrator.');
    }
}

