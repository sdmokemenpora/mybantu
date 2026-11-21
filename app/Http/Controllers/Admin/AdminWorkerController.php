<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Worker;
use App\Models\Province;
use App\Models\Rating;
use App\Models\Transaction;
use App\Models\PriceWorker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AdminWorkerController extends Controller
{
    /**
     * List Semua Worker (Mitra ART)
     */
    public function index(Request $request)
    {
        $query = Worker::withCount([
            'ratings',
            'prices',
        ])->withAvg('ratings', 'rating');

        // Pencarian teks
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('fullname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // Filter status verifikasi data
        if ($statusData = $request->input('status_data')) {
            if (in_array(strtolower($statusData), ['sudah terverifikasi', 'terverifikasi'])) {
                $query->whereIn('status_data', ['sudah terverifikasi', 'terverifikasi']);
            } elseif (strtolower($statusData) === 'belum terverifikasi') {
                $query->where(function ($q) {
                    $q->where('status_data', 'belum terverifikasi')
                      ->orWhereNull('status_data')
                      ->orWhere('status_data', '');
                });
            } else {
                $query->where('status_data', $statusData);
            }
        }

        // Filter status keaktifan worker
        if ($statusWorker = $request->input('status_worker')) {
            $query->where('status_worker', $statusWorker);
        }

        // Filter provinsi
        if ($provinsi = $request->input('provinsi')) {
            $query->where('provinsi', $provinsi);
        }

        $workers = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();
        $provinces = Province::orderBy('name')->get();

        // Hitung total ringkasan
        $totalCount = Worker::count();
        $activeCount = Worker::where('status_worker', 'AKTIF')->count();
        $pendingCount = Worker::where(function ($q) {
            $q->where('status_data', 'belum terverifikasi')
              ->orWhereNull('status_data')
              ->orWhere('status_data', '');
        })->count();
        $verifiedCount = Worker::whereIn('status_data', ['sudah terverifikasi', 'terverifikasi'])->count();

        return view('admin.workers.index', compact(
            'workers',
            'provinces',
            'totalCount',
            'activeCount',
            'pendingCount',
            'verifiedCount'
        ));
    }

    /**
     * Halaman Khusus Verifikasi Data Worker
     */
    public function verifyIndex(Request $request)
    {
        $query = Worker::query();

        $filter = $request->input('filter', 'pending');
        if ($filter === 'pending' || $filter === 'belum_terverifikasi') {
            $query->where(function ($q) {
                $q->where('status_data', 'belum terverifikasi')
                  ->orWhereNull('status_data')
                  ->orWhere('status_data', '');
            });
        } elseif ($filter === 'ditolak') {
            $query->where('status_data', 'ditolak');
        } elseif ($filter === 'terverifikasi' || $filter === 'sudah_terverifikasi') {
            $query->whereIn('status_data', ['sudah terverifikasi', 'terverifikasi']);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('fullname', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $workers = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();

        $countPending = Worker::where(function ($q) {
            $q->where('status_data', 'belum terverifikasi')
              ->orWhereNull('status_data')
              ->orWhere('status_data', '');
        })->count();
        $countVerified = Worker::whereIn('status_data', ['sudah terverifikasi', 'terverifikasi'])->count();
        $countRejected = Worker::where('status_data', 'ditolak')->count();

        return view('admin.workers.verify', compact(
            'workers',
            'filter',
            'countPending',
            'countVerified',
            'countRejected'
        ));
    }

    /**
     * Proses Verifikasi / Penolakan Data Worker (Aksi Cepat)
     */
    public function updateVerification(Request $request, $id)
    {
        $request->validate([
            'status_data' => 'required|in:sudah terverifikasi,terverifikasi,belum terverifikasi,ditolak',
        ]);

        $worker = Worker::findOrFail($id);
        $statusData = $request->status_data;
        if ($statusData === 'terverifikasi') {
            $statusData = 'sudah terverifikasi';
        }
        $worker->status_data = $statusData;
        $worker->save();

        $label = match ($statusData) {
            'sudah terverifikasi' => 'berhasil DIVERIFIKASI (SUDAH TERVERIFIKASI)',
            'ditolak'             => 'telah DITOLAK',
            default               => 'diubah menjadi BELUM TERVERIFIKASI',
        };

        return back()->with('success', "Status data mitra {$worker->fullname} {$label}.");
    }

    /**
     * Ubah Status Keaktifan Worker (AKTIF / NONAKTIF)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status_worker' => 'required|in:AKTIF,NONAKTIF',
        ]);

        $worker = Worker::findOrFail($id);
        $worker->status_worker = $request->status_worker;
        $worker->save();

        return back()->with('success', "Status keaktifan mitra {$worker->fullname} berhasil diubah menjadi {$request->status_worker}.");
    }

    /**
     * Tampilkan Detail Lengkap Worker
     */
    public function show($id)
    {
        $worker = Worker::with([
            'prices',
            'ratings.user',
        ])->findOrFail($id);

        $completedOrdersCount = Transaction::where('worker_id', $worker->id)
            ->where('status_selesai', 'selesai')
            ->distinct('id_transaksi')
            ->count('id_transaksi');

        $totalEarnings = Transaction::where('worker_id', $worker->id)
            ->where('status_dana_terkirim', 'Terkirim')
            ->sum('nominal_cair');

        $recentTransactions = Transaction::select(
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
            ->where('worker_id', $worker->id)
            ->with('user')
            ->groupBy('id_transaksi', 'user_id', 'worker_id', 'tipe_pekerjaan', 'konfirmasi', 'status_bekerja', 'status_selesai', 'status_pembayaran')
            ->orderByDesc('created_at')
            ->take(10)
            ->get();

        return view('admin.workers.show', compact(
            'worker',
            'completedOrdersCount',
            'totalEarnings',
            'recentTransactions'
        ));
    }

    /**
     * Form Edit Data Worker (ID Tidak Bisa Diubah / Grey)
     */
    public function edit($id)
    {
        $worker = Worker::with('prices')->findOrFail($id);
        $provinces = Province::with('regencies')->orderBy('name')->get();

        return view('admin.workers.edit', compact('worker', 'provinces'));
    }

    /**
     * Update Data Worker
     */
    public function update(Request $request, $id)
    {
        $worker = Worker::findOrFail($id);

        $validated = $request->validate([
            'fullname'       => 'required|string|max:255',
            'email'          => 'required|email|max:255|unique:workers,email,' . $worker->id,
            'password'       => 'nullable|string|min:6',
            'nik'            => 'required|string|max:16',
            'phone_number'   => 'required|string|max:30',
            'sex'            => 'nullable|string|in:Male,Female',
            'birth_date'     => 'nullable|date',
            'provinsi'       => 'required|string|exists:provinces,name',
            'kabupaten'      => 'required|string|exists:regencies,name',
            'alamat_lengkap' => 'required|string',
            'description'    => 'nullable|string',
            'status_data'    => 'required|in:sudah terverifikasi,terverifikasi,belum terverifikasi,ditolak',
            'status_worker'  => 'required|in:AKTIF,NONAKTIF',
            'skck'           => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'foto'           => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Upload SKCK jika ada
        if ($request->hasFile('skck')) {
            $file = $request->file('skck');
            $filename = 'SKCK_' . $worker->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads/skck', $filename, 'public');
            $worker->skck_path = $path;
        }

        // Upload Foto jika ada
        if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $filename = 'WORKER_' . $worker->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('uploads/workers', $filename, 'public');
            $worker->foto_path = $path;
        }

        // Update password hanya jika diisi
        if (!empty($validated['password'])) {
            $worker->password = Hash::make($validated['password']);
        }

        $worker->fullname       = $validated['fullname'];
        $worker->email          = $validated['email'];
        $worker->nik            = $validated['nik'];
        $worker->phone_number   = $validated['phone_number'];
        $worker->sex            = $validated['sex'];
        $worker->birth_date     = $validated['birth_date'];
        $worker->provinsi       = $validated['provinsi'];
        $worker->kabupaten      = $validated['kabupaten'];
        $worker->alamat_lengkap = $validated['alamat_lengkap'];
        $worker->description    = $validated['description'];
        $statusDataSave = $validated['status_data'];
        if ($statusDataSave === 'terverifikasi') {
            $statusDataSave = 'sudah terverifikasi';
        }
        $worker->status_data    = $statusDataSave;
        $worker->status_worker  = $validated['status_worker'];
        $worker->save();

        return redirect()->route('admin.workers.index')
            ->with('success', "Data mitra ART {$worker->fullname} berhasil diperbarui.");
    }

    /**
     * Hapus Worker (Admin bisa mendelete worker)
     */
    public function destroy($id)
    {
        $worker = Worker::findOrFail($id);
        $name = $worker->fullname;

        DB::transaction(function () use ($worker) {
            // Hapus ratings terkait worker
            Rating::where('worker_id', $worker->id)->delete();
            // PriceWorker dan Transactions memiliki onDelete cascade
            $worker->delete();
        });

        return redirect()->route('admin.workers.index')
            ->with('success', "Mitra ART {$name} (ID: #WRK-" . str_pad($id, 5, '0', STR_PAD_LEFT) . ") berhasil dihapus secara permanen.");
    }
}

