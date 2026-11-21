<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AdminManagementController extends Controller
{
    /**
     * List Semua Admin
     */
    public function index()
    {
        $admins = Admin::orderBy('id', 'asc')->get();
        return view('admin.admins.index', compact('admins'));
    }

    /**
     * Form Register Admin Baru
     */
    public function create()
    {
        return view('admin.admins.create');
    }

    /**
     * Proses Simpan Admin Baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255|unique:admins,email',
            'password'     => 'required|string|min:6|confirmed',
            'phone_number' => 'nullable|string|max:30',
            'role'         => 'required|in:Super Admin,Admin Verifikator,Admin Operasional',
            'status'       => 'required|in:AKTIF,NONAKTIF',
        ], [
            'email.unique'       => 'Email administrator sudah digunakan.',
            'password.min'       => 'Password minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $admin = Admin::create([
            'name'         => $validated['name'],
            'email'        => $validated['email'],
            'password'     => Hash::make($validated['password']),
            'phone_number' => $validated['phone_number'],
            'role'         => $validated['role'],
            'status'       => $validated['status'],
        ]);

        return redirect()->route('admin.admins.index')
            ->with('success', "Administrator baru '{$admin->name}' ({$admin->role}) berhasil didaftarkan!");
    }

    /**
     * Form Edit Admin (ID Admin Grey / Disabled)
     */
    public function edit($id)
    {
        $admin = Admin::findOrFail($id);
        return view('admin.admins.edit', compact('admin'));
    }

    /**
     * Update Data Admin
     */
    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'email'        => 'required|email|max:255|unique:admins,email,' . $admin->id,
            'password'     => 'nullable|string|min:6|confirmed',
            'phone_number' => 'nullable|string|max:30',
            'role'         => 'required|in:Super Admin,Admin Verifikator,Admin Operasional',
            'status'       => 'required|in:AKTIF,NONAKTIF',
        ], [
            'email.unique'       => 'Email administrator sudah digunakan.',
            'password.min'       => 'Password minimal harus 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        // Cegah admin menonaktifkan akunnya sendiri saat sedang login
        if (Auth::guard('admin')->id() === $admin->id && $validated['status'] === 'NONAKTIF') {
            return back()->withErrors(['status' => 'Anda tidak dapat menonaktifkan akun admin Anda sendiri saat sedang login.']);
        }

        $admin->name         = $validated['name'];
        $admin->email        = $validated['email'];
        $admin->phone_number = $validated['phone_number'];
        $admin->role         = $validated['role'];
        $admin->status       = $validated['status'];

        if (!empty($validated['password'])) {
            $admin->password = Hash::make($validated['password']);
        }

        $admin->save();

        return redirect()->route('admin.admins.index')
            ->with('success', "Data administrator {$admin->name} berhasil diperbarui.");
    }

    /**
     * Hapus Admin
     */
    public function destroy($id)
    {
        $admin = Admin::findOrFail($id);

        // Jangan izinkan admin menghapus akunnya sendiri
        if (Auth::guard('admin')->id() === $admin->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun administrator Anda sendiri yang sedang aktif digunakan.');
        }

        // Jangan izinkan jika hanya tersisa 1 admin di sistem
        if (Admin::count() <= 1) {
            return back()->with('error', 'Tidak dapat menghapus administrator terakhir di sistem.');
        }

        $name = $admin->name;
        $admin->delete();

        return redirect()->route('admin.admins.index')
            ->with('success', "Administrator {$name} berhasil dihapus.");
    }
}

