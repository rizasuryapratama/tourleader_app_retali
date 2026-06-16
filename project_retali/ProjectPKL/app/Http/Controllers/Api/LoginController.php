<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; 

class LoginController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $email = strtolower($request->email);

        
        $dbUser = DB::table('tour_leaders')
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();

        $role = 'tourleader';

        
        if (!$dbUser) {
            $dbUser = DB::table('muthawifs')
                ->whereRaw('LOWER(email) = ?', [$email])
                ->first();
            $role = 'muthawif';
        }

        
        if (!$dbUser || !Hash::check($request->password, $dbUser->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah',
            ], 401);
        }

        
        if (!empty($dbUser->kloter_id)) {
            $kloter = DB::table('kloters')->where('id', $dbUser->kloter_id)->first();
        }

        
        $token = Str::random(60); 
        $sessionToken = Str::random(120);

        
        if ($role === 'tourleader') {
            DB::table('tour_leaders')->where('id', $dbUser->id)->update([
                'session_token' => $sessionToken,
                'fcm_token'     => $request->fcm_token ?? $dbUser->fcm_token
            ]);
        } else {
            DB::table('muthawifs')->where('id', $dbUser->id)->update([
                'session_token' => $sessionToken,
                'fcm_token'     => $request->fcm_token ?? $dbUser->fcm_token
            ]);
        }

       
        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'token'   => $token, // Flutter dapet token aman
            'session_token' => $sessionToken,
            'user'    => [
                'id'              => $dbUser->id,
                'name'            => $role === 'tourleader' ? $dbUser->name : ($dbUser->nama ?? $dbUser->name),
                'email'           => $dbUser->email,
                'role'            => $role,
                'kloter'          => $kloter?->nama ?? 'Belum Ada Kloter',
                'kloter_tanggal'  => $kloter?->tanggal ?? '-',
            ],
        ], 200);
    }
}