<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use Illuminate\Support\Facades\Storage; // ⬅️ WAJIB TAMBAH INI

class AttendanceController extends Controller
{
    public function index()
    {
        $rows = Attendance::with(['tourleader.kloter'])
            ->latest()
            ->paginate(20);

        return view('admin.attendances.index', compact('rows'));
    }

    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);

        // Hapus foto jika ada
        if ($attendance->photo_path && Storage::exists('public/' . $attendance->photo_path)) {
            Storage::delete('public/' . $attendance->photo_path);
        }

        $attendance->delete();

        return redirect()
            ->route('admin.attendances.index')
            ->with('success', 'Data absensi berhasil dihapus');
    }
}
