<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Worker;
use App\Models\ContactMessage;

class HomeController extends Controller
{
    public function index()
    {
        // Hanya mengambil worker aktif yang sudah diset harga di tabel price_worker (harga > 0)
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
            ->latest()
            ->take(4)
            ->get();

        // Mengambil daftar provinsi yang pekerjanya aktif dan sudah memiliki harga
        $provinsis = Worker::where('status_worker', 'AKTIF')
            ->whereHas('prices', function ($q) {
                $q->where('harga', '>', 0);
            })
            ->select('provinsi')
            ->distinct()
            ->whereNotNull('provinsi')
            ->orderBy('provinsi')
            ->pluck('provinsi');

        return view('home', compact('workers', 'provinsis'));
    }

    public function contact()
    {
        return view('contact');
    }

    public function storeContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string|min:5'
        ]);

        ContactMessage::create($request->only('name', 'email', 'message'));
        return redirect()->back()->with('success', 'Terima kasih! Pesan Anda berhasil dikirim ke tim MyBantu.');
    }
}
