<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $email = strtolower($request->email);

       
        $modelTourLeader = '\\App\\Models\\TourLeader';
        $modelMuthawif   = '\\App\\Models\\Muthawif';

       
        $user = $modelTourLeader::with('kloter')
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        $role = 'tourleader';

       
        if (!$user) {
            $user = $modelMuthawif::with('kloter')
                ->whereRaw('LOWER(email) = ?', [$email])
                ->first();

            $role = 'muthawif';
        }

        
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah',
            ], 401);
        }

        
        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

       
        $sessionToken = Str::random(120);
        $user->session_token = $sessionToken;
        $user->save();

        
        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'token'   => $token,
            'session_token' => $sessionToken,
            'user'    => [
                'id'              => $user->id,
                'name'            => $role === 'tourleader' ? $user->name : $user->nama,
                'email'           => $user->email,
                'role'            => $role,
                'kloter'          => $user->kloter?->nama ?? 'Belum Ada Kloter',
                'kloter_tanggal'  => $user->kloter?->tanggal ?? '-',
            ],
        ], 200);
    }
}