<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserProfileController extends Controller
{
    /**
     * Tampilkan Halaman Profil User / Majikan
     */
    public function show()
    {
        $user = Auth::user();
        return view('user.profile', compact('user'));
    }

    /**
     * Proses Pembaruan Profil User / Majikan
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'email'          => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone_number'   => 'required|string|max:20',
            'provinsi'       => 'required|string|max:100',
            'kabupaten'      => 'required|string|max:100',
            'alamat_lengkap' => 'required|string',
            'password'       => 'nullable|string|min:6|confirmed',
            'foto'           => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $user->email          = $validated['email'];
        $user->phone_number   = $validated['phone_number'];
        $user->provinsi       = $validated['provinsi'];
        $user->kabupaten      = $validated['kabupaten'];
        $user->alamat_lengkap = $validated['alamat_lengkap'];

        // Jika password diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        // Jika foto profil diunggah
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($user->foto_path && Storage::disk('public')->exists($user->foto_path)) {
                Storage::disk('public')->delete($user->foto_path);
            }

            $cleanName = Str::slug($user->name, '_');
            $extension = $request->file('foto')->getClientOriginalExtension();
            $filename = 'user_' . $cleanName . '_' . time() . '.' . $extension;

            $fotoPath = $request->file('foto')->storeAs('uploads/user_foto', $filename, 'public');
            $user->foto_path = $fotoPath;
        }

        $user->save();

        return redirect()->route('user.profile')->with('success', 'Profil Anda berhasil diperbarui!');
    }
}

