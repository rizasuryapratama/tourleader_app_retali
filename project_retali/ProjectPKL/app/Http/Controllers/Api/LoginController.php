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

        $email = strtolower($request->email);

        // =========================
        // CEK TOURLEADER
        // =========================
        $user = Tourleader::with('kloter')
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

        // =========================
        // TOKEN
        // =========================
        $token = $user->createToken('auth_token')->plainTextToken;

        // =========================
        // RESPONSE
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