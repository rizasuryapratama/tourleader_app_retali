<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Tourleader;
use App\Models\Muthawif;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // =========================
        // CEK TOURLEADER DULU
        // =========================
        $user = Tourleader::with('kloter')
            ->where('email', $request->email)
            ->first();

        $role = 'tourleader';

        // =========================
        // KALAU TIDAK ADA, CEK MUTHAWIF
        // =========================
        if (!$user) {
            $user = Muthawif::with('kloter')
                ->where('email', $request->email)
                ->first();

            $role = 'muthawif';
        }

        // =========================
        // KALAU TIDAK ADA SAMA SEKALI
        // =========================
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak ditemukan',
            ], 404);
        }

        // =========================
        // CEK PASSWORD
        // =========================
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Password salah',
            ], 401);
        }

        // =========================
        // BUAT TOKEN
        // =========================
        $token = $user->createToken('auth_token')->plainTextToken;

        // =========================
        // RESPONSE FINAL
        // =========================
        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'token'   => $token,
            'user'    => [
                'id'              => $user->id,
                'name'            => $role === 'tourleader' ? $user->name : $user->nama,
                'email'           => $user->email,
                'role'            => $role,
                'kloter'          => $user->kloter?->nama,
                'kloter_tanggal'  => $user->kloter?->tanggal,
            ],
        ], 200);
    }
}