<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Worker;
use App\Models\PriceWorker;
use App\Models\Rating;
use App\Models\Province;
use App\Models\Regency;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class WorkerController extends Controller
{
    /**
     * Tampilkan Halaman Daftar Pekerja / ART (`/product`)
     * Membaca list worker yang sudah ada harga dari table price_worker (harga > 0)
     */
    public function index(Request $request)
    {
        // Query seluruh worker yang aktif dan sudah memiliki harga aktif (> 0) di tabel price_worker
        $query = Worker::with([
                'prices' => function ($q) {
                    $q->where('harga', '>', 0);
                },
                'ratings.user'
            ])
            ->where('status_worker', 'AKTIF')
            ->whereHas('prices', function ($q) {
                $q->where('harga', '>', 0);
            });

        // Filter berdasarkan provinsi jika dipilih
        if ($request->filled('provinsi')) {
            $query->where('provinsi', $request->provinsi);
        }

        // Filter pencarian berdasarkan nama, kabupaten, atau provinsi
        if ($request->filled('q')) {
            $keyword = trim($request->q);
            $query->where(function ($sub) use ($keyword) {
                $sub->where('fullname', 'like', "%{$keyword}%")
                    ->orWhere('kabupaten', 'like', "%{$keyword}%")
                    ->orWhere('provinsi', 'like', "%{$keyword}%");
            });
        }

        $workers = $query->latest()->get();

        // Ambil daftar provinsi unik dari worker aktif yang sudah memiliki harga di table price_worker
        $provinsis = Worker::where('status_worker', 'AKTIF')
            ->whereHas('prices', function ($q) {
                $q->where('harga', '>', 0);
            })
            ->select('provinsi')
            ->distinct()
            ->whereNotNull('provinsi')
            ->orderBy('provinsi')
            ->pluck('provinsi');

        $topWorkersByProvinsi = [];
        foreach ($provinsis as $provinsiName) {
            $slug = Str::slug($provinsiName);
            $provWorkers = $workers->filter(function ($item) use ($provinsiName) {
                return $item->provinsi === $provinsiName;
            });

            if ($provWorkers->isNotEmpty()) {
                $topWorkersByProvinsi[$slug] = [
                    'nama_provinsi' => $provinsiName,
                    'workers' => $provWorkers
                ];
            }
        }

        return view('product.index', compact('workers', 'provinsis', 'topWorkersByProvinsi'));
    }

    /**
     * Tampilkan Pekerja Berdasarkan Provinsi
     * Membaca worker aktif yang sudah diset harga di tabel price_worker
     */
    public function showByProvinsi($slug)
    {
        $workers = Worker::with([
                'prices' => function ($q) {
                    $q->where('harga', '>', 0);
                },
                'ratings.user'
            ])
            ->where('status_worker', 'AKTIF')
            ->whereHas('prices', function ($q) {
                $q->where('harga', '>', 0);
            })
            ->get()->filter(function ($item) use ($slug) {
                return Str::slug($item->provinsi) === $slug;
            });

        $provinsiName = $workers->isNotEmpty() 
            ? $workers->first()->provinsi 
            : ucwords(str_replace('-', ' ', $slug));

        return view('product.provinsi', compact('workers', 'provinsiName', 'slug'));
    }

    /**
     * Tampilkan Detail Spesifik Pekerja
     */
    public function show($id)
    {
        $worker = Worker::findOrFail($id);

        $prices = PriceWorker::where('worker_id', $id)->get();
        $firstPriceRow = $prices->first();
        $defaultPrice = $firstPriceRow ? $firstPriceRow->harga : 0;

        // Eager load relasi user agar ulasan tidak N+1 query
        $reviews = Rating::with('user')->where('worker_id', $id)->latest()->get();
        $avgRating = $reviews->avg('rating') ? round($reviews->avg('rating'), 1) : 0;

        // Ambil data profil pemesan yang sedang login (User / Majikan)
        $buyer = Auth::guard('web')->user() ?? Auth::user();

        $adminContact = \App\Models\Admin::where('status', 'AKTIF')->first() ?? \App\Models\Admin::first();

        return view('product.detail', compact('worker', 'prices', 'firstPriceRow', 'defaultPrice', 'reviews', 'avgRating', 'buyer', 'adminContact'));
    }

    /**
     * Tampilkan Form Pendaftaran Seller / Pekerja
     */
    public function showRegisterSeller()
    {
        $provinces = Province::with('regencies')->orderBy('name')->get();
        return view('auth.register_seller', compact('provinces'));
    }

    /**
     * Helper Function untuk generate nama file dengan format: Fullname_Jenis_Sequence.ext
     */
    private function generateCustomFilename($file, $fullname, $jenis, $folder)
    {
        $cleanName = Str::slug($fullname, '_');
        $extension = $file->getClientOriginalExtension();

        $existingFiles = Storage::disk('public')->files($folder);
        $pattern = strtolower("{$cleanName}_{$jenis}_");

        $count = 0;
        foreach ($existingFiles as $existingFile) {
            $filenameOnly = strtolower(basename($existingFile));
            if (Str::startsWith($filenameOnly, $pattern)) {
                $count++;
            }
        }

        $nextSequence = str_pad($count + 1, 3, '0', STR_PAD_LEFT);
        $newFilename = "{$cleanName}_{$jenis}_{$nextSequence}.{$extension}";

        return $file->storeAs($folder, $newFilename, 'public');
    }

    /**
     * Proses Simpan Pendaftaran Seller + Custom Naming Foto, SKCK, Validasi Syarat & Ketentuan & Auto Insert price_worker
     */
    public function storeSeller(Request $request)
    {
        $validated = $request->validate([
            'fullname'         => 'required|string|max:255',
            'email'            => 'required|string|email|max:255|unique:workers',
            'password'         => 'required|string|min:6',
            'nik'              => 'required|string|max:16',
            'phone_number'     => 'required|string',
            'sex'              => 'required|string|in:Female,Male',
            'provinsi'         => 'required|string|exists:provinces,name',
            'kabupaten'        => 'required|string|exists:regencies,name',
            'alamat_lengkap'   => 'required|string',
            'tipe_pekerjaan'   => 'required|array',
            'tipe_pekerjaan.*' => 'string|in:Harian,Mingguan',
            'foto'             => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'skck'             => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'terms'            => 'accepted',
        ], [
            'provinsi.exists'  => 'Provinsi yang dipilih tidak terdaftar di sistem.',
            'kabupaten.exists' => 'Kabupaten/Kota yang dipilih tidak terdaftar di sistem.',
            'terms.accepted'   => 'Anda wajib membaca dan menyetujui seluruh Syarat & Ketentuan Kemitraan Mitra ART untuk dapat mendaftar.',
            'terms.required'   => 'Anda wajib menyetujui Syarat & Ketentuan Kemitraan untuk melanjutkan pendaftaran.',
        ]);

        $birthDate = null;
        if ($request->birth_year && $request->birth_month && $request->birth_day) {
            $birthDate = "{$request->birth_year}-{$request->birth_month}-{$request->birth_day}";
        }

        $fullname = $validated['fullname'];

        $fotoPath = null;
        if ($request->hasFile('foto')) {
            $fotoPath = $this->generateCustomFilename($request->file('foto'), $fullname, 'Foto', 'uploads/foto');
        }

        $skckPath = null;
        if ($request->hasFile('skck')) {
            $skckPath = $this->generateCustomFilename($request->file('skck'), $fullname, 'SKCK', 'uploads/skck');
        }

        $stringTipePekerjaan = implode(', ', $validated['tipe_pekerjaan']);

        // 1. Simpan Data Worker Baru (Tanpa upload KTP)
        $worker = Worker::create([
            'fullname'       => $fullname,
            'email'          => $validated['email'],
            'password'       => Hash::make($validated['password']),
            'nik'            => $validated['nik'],
            'phone_number'   => $validated['phone_number'],
            'sex'            => $validated['sex'],
            'birth_date'     => $birthDate,
            'provinsi'       => $validated['provinsi'],
            'kabupaten'      => $validated['kabupaten'],
            'alamat_lengkap' => $validated['alamat_lengkap'],
            'foto_path'      => $fotoPath,
            'ktp_path'       => null,
            'skck_path'      => $skckPath,
            'tipe_pekerjaan' => $stringTipePekerjaan,
            'status_data'    => 'belum terverifikasi',
            'status_worker'  => 'AKTIF',
        ]);

        // 2. Otomatis buat baris data di tabel price_worker sesuai pilihan tipe pekerjaan
        foreach ($validated['tipe_pekerjaan'] as $tipe) {
            $tipeTrimmed = trim($tipe);
            // Default rate: Harian 120.000, Mingguan 750.000 jika belum diatur
            $defaultRate = (stripos($tipeTrimmed, 'mingguan') !== false) ? 750000 : 120000;

            PriceWorker::create([
                'worker_id'      => $worker->id,
                'tipe_pekerjaan' => $tipeTrimmed,
                'harga'          => $defaultRate,
            ]);
        }

        return redirect()->route('product')->with('success', 'Pendaftaran Mitra ART berhasil! Tim kami akan melakukan verifikasi berkas dalam 1x24 jam.');
    }
}
