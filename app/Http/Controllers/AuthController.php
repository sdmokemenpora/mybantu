<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Worker;
use App\Models\Province;
use App\Models\Regency;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Services\BruteForceMonitor;

class AuthController extends Controller
{
    public function __construct(
        private readonly BruteForceMonitor $bruteForceMonitor,
    ) {
    }

    // Form Login
    public function showLogin(Request $request)
    {
        $role = $request->query('role', 'user');
        return view('auth.login', compact('role'));
    }

    // Proses Login (Bisa sebagai Majikan, Mitra Worker, atau Admin)
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
            'role'     => 'nullable|string|in:user,worker,admin',
        ]);

        $role = $request->input('role', 'user');
        $email = strtolower(trim($credentials['email']));

        if ($blocked = $this->bruteForceMonitor->ensureIsNotRateLimited($request, $email, $role)) {
            return $blocked;
        }

        // 1. Cek Login Administrator
        if ($role === 'admin') {
            if (Auth::guard('admin')->attempt(['email' => $email, 'password' => $credentials['password']])) {
                $admin = Auth::guard('admin')->user();
                if (!$admin->isActive()) {
                    Auth::guard('admin')->logout();
                    $this->bruteForceMonitor->recordFailedAttempt($request, $email, 'admin', 'inactive_admin', 'inactive_account');
                    return back()->withErrors(['email' => 'Akun admin Anda berstatus NONAKTIF.'])->onlyInput('email');
                }
                $request->session()->regenerate();
                $this->bruteForceMonitor->recordSuccess($request, $email, 'admin');
                return redirect()->route('admin.dashboard')->with('success', 'Selamat datang kembali, ' . $admin->name . ' (Admin)!');
            }
            $this->bruteForceMonitor->recordFailedAttempt($request, $email, 'admin');
            return back()->withErrors(['email' => 'Email atau password administrator tidak sesuai.'])->onlyInput('email');
        }

        // Fallback cerdas: jika kredensial cocok dengan akun Admin
        if (Auth::guard('admin')->attempt(['email' => $email, 'password' => $credentials['password']])) {
            $admin = Auth::guard('admin')->user();
            if (!$admin->isActive()) {
                Auth::guard('admin')->logout();
                $this->bruteForceMonitor->recordFailedAttempt($request, $email, 'admin', 'inactive_admin', 'inactive_account');
                return back()->withErrors(['email' => 'Akun admin Anda berstatus NONAKTIF.'])->onlyInput('email');
            }
            $request->session()->regenerate();
            $this->bruteForceMonitor->recordSuccess($request, $email, 'admin');
            return redirect()->route('admin.dashboard')->with('success', 'Selamat datang kembali, ' . $admin->name . ' (Admin)!');
        }

        // 2. Jika user memilih login sebagai Worker
        if ($role === 'worker') {
            if (Auth::guard('worker')->attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
                $request->session()->regenerate();
                $this->bruteForceMonitor->recordSuccess($request, $email, 'worker');
                return redirect()->intended(route('worker.transaksi'))->with('success', 'Selamat datang kembali, Mitra ART ' . Auth::guard('worker')->user()->fullname . '!');
            }
        } else {
            // Default login sebagai Majikan (User)
            if (Auth::guard('web')->attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
                $request->session()->regenerate();
                $this->bruteForceMonitor->recordSuccess($request, $email, 'web');
                return redirect()->intended(route('home'))->with('success', 'Selamat datang kembali, ' . Auth::user()->name . '!');
            }

            // Fallback cerdas: cek apakah akun terdaftar di worker jika user lupa ganti tab
            if (Auth::guard('worker')->attempt(['email' => $credentials['email'], 'password' => $credentials['password']])) {
                $request->session()->regenerate();
                $this->bruteForceMonitor->recordSuccess($request, $email, 'worker');
                return redirect()->intended(route('worker.transaksi'))->with('success', 'Selamat datang kembali, Mitra ART ' . Auth::guard('worker')->user()->fullname . '!');
            }
        }

        $this->bruteForceMonitor->recordFailedAttempt($request, $email, $role === 'worker' ? 'worker' : 'web');

        return back()->withErrors([
            'email' => 'Email atau password yang Anda masukkan tidak sesuai.',
        ])->onlyInput('email');
    }

    // Form Register Pelanggan (Majikan)
    public function showRegister()
    {
        $provinces = Province::with('regencies')->orderBy('name')->get();
        return view('auth.register', compact('provinces'));
    }

    // Proses Register Pelanggan
    public function register(Request $request)
    {
        $validated = $request->validate([
            'fullname'       => 'required|string|max:255',
            'email'          => 'required|string|email|max:255|unique:users',
            'password'       => 'required|string|min:6',
            'nik'            => 'required|string|max:16',
            'phone_number'   => 'required|string',
            'provinsi'       => 'required|string|exists:provinces,name',
            'kabupaten'      => 'required|string|exists:regencies,name',
            'alamat_lengkap' => 'required|string',
        ], [
            'provinsi.exists'  => 'Provinsi yang dipilih tidak terdaftar di sistem.',
            'kabupaten.exists' => 'Kabupaten/Kota yang dipilih tidak terdaftar di sistem.',
        ]);

        $birthDate = null;
        if ($request->birth_year && $request->birth_month && $request->birth_day) {
            $birthDate = "{$request->birth_year}-{$request->birth_month}-{$request->birth_day}";
        }

        $sex = is_array($request->sex) ? implode(', ', $request->sex) : $request->sex;

        $user = User::create([
            'name'           => $validated['fullname'],
            'email'          => $validated['email'],
            'password'       => Hash::make($validated['password']),
            'nik'            => $validated['nik'],
            'phone_number'   => $validated['phone_number'],
            'sex'            => $sex,
            'birth_date'     => $birthDate,
            'provinsi'       => $validated['provinsi'],
            'kabupaten'      => $validated['kabupaten'],
            'alamat_lengkap' => $validated['alamat_lengkap'],
        ]);

        Auth::guard('web')->login($user);

        return redirect()->route('home')->with('success', 'Pendaftaran akun majikan berhasil! Selamat datang di MyBantu.');
    }

    // Proses Logout Majikan
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home')->with('success', 'Anda telah berhasil logout.');
    }

    // Proses Logout Mitra ART (Worker)
    public function logoutWorker(Request $request)
    {
        Auth::guard('worker')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home')->with('success', 'Anda telah berhasil logout sebagai Mitra ART.');
    }
}
