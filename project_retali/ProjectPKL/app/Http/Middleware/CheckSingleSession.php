<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckSingleSession
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // user belum login
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        // ambil session token dari header flutter
        $sessionToken = $request->header('Session-Token');

        // token kosong
        if (!$sessionToken) {
            return response()->json([
                'success' => false,
                'message' => 'Session token missing'
            ], 401);
        }

        // token beda = akun login di device lain
        if ($sessionToken !== $user->session_token) {

            // hapus token sekarang
            $user->currentAccessToken()?->delete();

            return response()->json([
                'success' => false,
                'force_logout' => true,
                'message' => 'Akun digunakan di device lain'
            ], 401);
        }

        return $next($request);
    }
}