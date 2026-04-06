<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PassportScan;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PassportScanExport;

class ScanPasporController extends Controller
{
    // =============================
    // INDEX
    // =============================
   public function index(Request $request)
{
    // ambil daftar kloter unik yang pernah discan
    $kloters = PassportScan::query()
        ->whereNotNull('kloter')
        ->where('kloter', '!=', '')
        ->select('kloter')
        ->distinct()
        ->orderBy('kloter')
        ->pluck('kloter');

    $query = PassportScan::with('tourleader:id,name')
        ->orderByDesc('scanned_at');

    if ($request->filled('tanggal')) {
        $query->whereDate('scanned_at', $request->tanggal);
    }

    if ($request->filled('kloter')) {
        $query->where('kloter', $request->kloter);
    }

    $scans = $query->paginate(20)->withQueryString();

    return view(
        'admin.scan_paspor.index',
        compact('scans', 'kloters')
    );
}


    // =============================
    // SHOW (DETAIL JSON) ✅ WAJIB
    // =============================
    public function show(PassportScan $passportScan)
{
    return response()->json([
        'passport_number' => $passportScan->passport_number,
        'owner_name'      => $passportScan->owner_name,
        'owner_phone'     => $passportScan->owner_phone,
        'kloter'          => $passportScan->kloter,
        'scanned_at'      => $passportScan->scanned_at->format('d-m-Y H:i'),
        'tourleader'      => [
            'name' => optional($passportScan->tourleader)->name
        ],
    ]);
}


    // =============================
    // EXPORT
    // =============================
    public function export(Request $request)
    {
        return Excel::download(
            new PassportScanExport($request->input('tanggal')),
            'riwayat-scan-paspor.xlsx'
        );
    }

    // =============================
    // DELETE
    // =============================
    public function destroy(PassportScan $passportScan)
    {
        $passportScan->delete();

        return back()->with('success', 'Data scan paspor berhasil dihapus.');
    }
}
