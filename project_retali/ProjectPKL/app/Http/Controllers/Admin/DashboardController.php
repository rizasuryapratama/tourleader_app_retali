<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Scan;
use App\Models\Tourleader;
use App\Models\Notification;

class DashboardController extends Controller
{
    public function index()
    {
        // Hitungan utama
        $totalScans = Scan::count();
        $totalTourLeaders = Tourleader::count();
        $totalNotifikasi = Notification::count(); // sudah cocok dengan model kamu

        // Scan terbaru untuk tabel
        $latestScans = Scan::with('tourleader')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalScans',
            'totalTourLeaders',
            'totalNotifikasi',
            'latestScans'
        ));
    }

    public function dashboard() 
    {
        $scanToday = Scan::whereDate('created_at', today())->count();
        $totalScan = Scan::count();

        $lastScan = Scan::latest()->first();
        $lastScanDate = $lastScan 
            ? $lastScan->created_at->format('d M Y H:i') 
            : null;

        return view('dashboard', compact(
            'scanToday',
            'totalScan',
            'lastScanDate'
        ));
    }
}
