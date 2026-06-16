<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\TourLeader;
use App\Models\Muthawif;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $email = strtolower($request->email);

        // =========================
        // CEK TOURLEADER
        // =========================
        $user = TourLeader::with('kloter')
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        $role = 'tourleader';

        // =========================
        // CEK MUTHAWIF
        // =========================
        if (!$user) {
            $user = Muthawif::with('kloter')
                ->whereRaw('LOWER(email) = ?', [$email])
                ->first();

            $role = 'muthawif';
        }

        // =========================
        // VALIDASI LOGIN
        // =========================
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah',
            ], 401);
        }

        // ====================================
        // HAPUS TOKEN LAMA
        // ====================================
        $user->tokens()->delete();

        // ====================================
        // BUAT TOKEN BARU
        // ====================================
        $token = $user->createToken('auth_token')->plainTextToken;

        // ====================================
        // BUAT SESSION TOKEN
        // ====================================
        $sessionToken = Str::random(120);

        // simpan session token terbaru
        $user->session_token = $sessionToken;
        $user->save();

        // =========================
        // RESPONSE
        // =========================
        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'token'   => $token,
            'session_token' => $sessionToken,
            'user'    => [
                'id'              => $user->id,
                'name'            => $role === 'tourleader'
                    ? $user->name
                    : $user->nama,
                'email'           => $user->email,
                'role'            => $role,
                'kloter'          => $user->kloter?->nama,
                'kloter_tanggal'  => $user->kloter?->tanggal,
            ],
        ], 200);
    }
}
