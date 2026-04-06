<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Scan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Database\QueryException;

class ScanController extends Controller
{
    // =============================
    // LIST SCAN MILIK TL LOGIN
    // =============================
    public function index()
    {
        $tourleaderId = auth('tourleader')->id();
        if (!$tourleaderId) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        return Scan::with('tourleader:id,name')
            ->where('tourleader_id', $tourleaderId)
            ->latest('scanned_at')
            ->get([
                'id',
                'koper_code',
                'owner_name',
                'owner_phone',
                'kloter',
                'scanned_at',
                'tourleader_id'
            ]);
    }

    // =============================
    // STORE SCAN (MOBILE / API)
    // =============================
    public function store(Request $request)
    {
        $tourleaderId = auth('tourleader')->id();
        if (!$tourleaderId) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        $payload = $request->all();

        if ($request->filled('qr_text')) {
            $payload = array_merge($payload, $this->parseQr($request->string('qr_text')));
        }

        $data = validator($payload, [
            'koper_code'  => 'required|string|max:255',
            'owner_name'  => 'nullable|string|max:255',
            'owner_phone' => 'nullable|string|max:30',
            'scanned_at'  => 'nullable|date',
            'kloter'      => 'nullable|string|max:255',
        ])->validate();

        // 🔒 CEK GLOBAL
        $existing = Scan::with('tourleader')
            ->where('koper_code', $data['koper_code'])
            ->first();

        if ($existing) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Koper ini sudah pernah discan.',
                'data'    => [
                    'scanned_at' => $existing->scanned_at,
                    'tourleader' => optional($existing->tourleader)->name,
                ]
            ], 409);
        }

        try {
            $scan = Scan::create([
                'tourleader_id' => $tourleaderId,
                'koper_code'    => $data['koper_code'],
                'owner_name'    => $data['owner_name'] ?? null,
                'owner_phone'   => $data['owner_phone'] ?? null,
                'kloter'        => $data['kloter'] ?? null,
                'scanned_at'    => isset($data['scanned_at'])
                    ? Carbon::parse($data['scanned_at'])->setTimezone('Asia/Jakarta')
                    : now('Asia/Jakarta'),
            ]);
        } catch (QueryException $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Koper ini sudah pernah discan.',
            ], 409);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Scan berhasil disimpan.',
            'data'    => $scan
        ], 201);
    }

    // =============================
    // PARSE QR
    // =============================
    private function parseQr(string $raw): array
    {
        $raw = trim($raw);
        if ($raw === '') return [];

        if ($this->isJson($raw)) {
            $j = json_decode($raw, true);
            return array_filter([
                'koper_code'  => $j['kode'] ?? $j['koper_code'] ?? null,
                'owner_name'  => $j['nama'] ?? null,
                'owner_phone' => $j['phone'] ?? null,
                'kloter'      => $j['kloter'] ?? null,
            ], fn($v) => filled($v));
        }

        $parts = array_map('trim', preg_split('/\||\r\n|\n|\r/', $raw));

        return [
            'koper_code'  => $parts[0] ?? null,
            'owner_name'  => $parts[1] ?? null,
            'owner_phone' => $parts[2] ?? null,
            'kloter'      => $parts[3] ?? null,
        ];
    }

    private function isJson(string $s): bool
    {
        json_decode($s);
        return json_last_error() === JSON_ERROR_NONE;
    }


    public function destroy(Scan $scan)
    {
        $tourleaderId = auth('tourleader')->id();

        if ($scan->tourleader_id !== $tourleaderId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden'
            ], 403);
        }

        $scan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Scan koper berhasil dihapus.'
        ]);
    }
}
