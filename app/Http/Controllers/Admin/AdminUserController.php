<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Province;
use App\Models\Rating;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminUserController extends Controller
{
    /**
     * List Semua User / Majikan
     */
    public function index(Request $request)
    {
        $query = User::query();

        // Pencarian teks
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // Filter provinsi
        if ($provinsi = $request->input('provinsi')) {
            $query->where('provinsi', $provinsi);
        }

        $users = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();

        // Hitung transaksi tiap user
        foreach ($users as $u) {
            $u->total_orders = Transaction::where('user_id', $u->id)
                ->distinct('id_transaksi')
                ->count('id_transaksi');
            $u->completed_orders = Transaction::where('user_id', $u->id)
                ->where('status_selesai', 'selesai')
                ->distinct('id_transaksi')
                ->count('id_transaksi');
        }

        $totalUsers = User::count();
        $provinces = Province::orderBy('name')->get();

        return view('admin.users.index', compact('users', 'totalUsers', 'provinces'));
    }

    /**
     * Tampilkan Detail User / Majikan
     */
    public function show($id)
    {
        $user = User::findOrFail($id);

        $transactions = Transaction::select(
                'id_transaksi',
                'user_id',
                'worker_id',
                'tipe_pekerjaan',
                'konfirmasi',
                'status_bekerja',
                'status_selesai',
                'status_pembayaran',
                DB::raw('MIN(tanggal_kerja) as date_from'),
                DB::raw('MAX(tanggal_kerja) as date_to'),
                DB::raw('MAX(created_at) as created_at')
            )
            ->where('user_id', $user->id)
            ->with('worker')
            ->groupBy('id_transaksi', 'user_id', 'worker_id', 'tipe_pekerjaan', 'konfirmasi', 'status_bekerja', 'status_selesai', 'status_pembayaran')
            ->orderByDesc('created_at')
            ->get();

        $reviews = Rating::where('user_id', $user->id)->with('worker')->get();

        return view('admin.users.show', compact('user', 'transactions', 'reviews'));
    }

    /**
     * Form Edit Data User (ID Tidak Bisa Diubah / Grey)
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        $provinces = Province::with('regencies')->orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'provinces'));
    }

    /**
     * Update Data User
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email|max:255|unique:users,email,' . $user->id,
            'password'       => 'nullable|string|min:6',
            'nik'            => 'required|string|max:16',
            'phone_number'   => 'required|string|max:30',
            'sex'            => 'nullable|string|in:Male,Female',
            'birth_date'     => 'nullable|date',
            'provinsi'       => 'required|string|exists:provinces,name',
            'kabupaten'      => 'required|string|exists:regencies,name',
            'alamat_lengkap' => 'required|string',
        ]);

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->name           = $validated['name'];
        $user->email          = $validated['email'];
        $user->nik            = $validated['nik'];
        $user->phone_number   = $validated['phone_number'];
        $user->sex            = $validated['sex'];
        $user->birth_date     = $validated['birth_date'];
        $user->provinsi       = $validated['provinsi'];
        $user->kabupaten      = $validated['kabupaten'];
        $user->alamat_lengkap = $validated['alamat_lengkap'];
        $user->save();

        return redirect()->route('admin.users.index')
            ->with('success', "Data majikan {$user->name} berhasil diperbarui.");
    }

    /**
     * Hapus User (Admin bisa mendelete user)
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $name = $user->name;

        DB::transaction(function () use ($user) {
            // Hapus ratings yang dibuat oleh user
            Rating::where('user_id', $user->id)->delete();
            // Transaksi user akan dihapus melalui cascade foreignId
            $user->delete();
        });

        return redirect()->route('admin.users.index')
            ->with('success', "Akun majikan {$name} (ID: #USR-" . str_pad($id, 5, '0', STR_PAD_LEFT) . ") berhasil dihapus secara permanen.");
    }
}

