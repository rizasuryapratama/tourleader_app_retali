<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AttendanceMuthawif;
use Illuminate\Http\Request;

class AbsenMuthawifController extends Controller
{
    public function index()
    {
        $attendances = AttendanceMuthawif::with('muthawif.kloter')
            ->latest()
            ->get();

        return view('admin.absen_muthawif.index', compact('attendances'));
    }

    public function destroy($id)
    {
        $attendance = AttendanceMuthawif::findOrFail($id);

        // optional: hapus foto
        if ($attendance->foto && file_exists(public_path('storage/' . $attendance->foto))) {
            unlink(public_path('storage/' . $attendance->foto));
        }

        $attendance->delete();

        return back()->with('success', 'Data absensi muthawif berhasil dihapus');
    }
}
