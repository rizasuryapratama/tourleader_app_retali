<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AttendanceMuthawif;
use Illuminate\Http\Request;

class AbsenMuthawifController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'photo' => 'required|image',
            'lat' => 'required',
            'lng' => 'required',
        ]);

        $user = $request->user();

        $path = $request->file('photo')->store('absensi_muthawif', 'public');

        $attendance = AttendanceMuthawif::create([
            'muthawif_id' => $user->id,
            'foto' => $path,
            'latitude' => $request->lat,
            'longitude' => $request->lng,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil',
            'data' => $attendance
        ]);
    }


    public function history(Request $request)
    {
        $user = $request->user();

        $data = AttendanceMuthawif::where('muthawif_id', $user->id)
            ->latest()
            ->get();

        return response()->json($data);
    }
}
