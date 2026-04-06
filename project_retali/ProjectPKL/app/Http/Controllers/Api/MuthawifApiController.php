<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Muthawif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class MuthawifApiController extends Controller
{
    /**
     * ==========================
     * LOGIN MUTHAWIF
     * POST /api/muthawif/login
     * ==========================
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        
        $muthawif = Muthawif::where('email', $request->email)->first();

        if (!$muthawif) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak ditemukan',
            ], 401);
        }

        // cek password
        if (!Hash::check($request->password, $muthawif->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password salah',
            ], 401);
        }

        // buat token (Laravel Sanctum)
        $token = $muthawif->createToken('muthawif-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'token'   => $token, 
            'user'    => [
                'id'              => $muthawif->id,
                'name'            => $muthawif->nama,
                'email'           => $muthawif->email,
                'role'            => 'muthawif',
                'kloter'          => optional($muthawif->kloter)->nama,
                'kloter_tanggal' => optional($muthawif->kloter)->tanggal,

            ],
        ], 200);
    }

    /**
     * ==========================
     * PROFILE MUTHAWIF (AUTH)
     * GET /api/muthawif/profile
     * ==========================
     */
    public function profile(Request $request)
    {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'user' => [
                'id'              => $user->id,
                'name'            => $user->nama,
                'email'           => $user->email,
                'role'            => 'muthawif',
                'kloter'          => optional($user->kloter)->nama,
                'kloter_tanggal' => optional($user->kloter)->tanggal,

            ],
        ]);
    }

    /**
     * ==========================
     * LOGOUT
     * POST /api/muthawif/logout
     * ==========================
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil',
        ]);
    }
}
