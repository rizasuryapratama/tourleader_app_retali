<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PassportScan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PassportScanController extends Controller
{
    /**
     * =============================
     * STORE PASSPORT SCAN
     * =============================
     */

    public function index()
    {
        $tourleaderId = auth('tourleader')->id();

        if (!$tourleaderId) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Unauthorized'
            ], 401);
        }

        $scans = PassportScan::where('tourleader_id', $tourleaderId)
            ->latest('scanned_at')
            ->get([
                'id',
                'passport_number',
                'owner_name',
                'owner_phone',
                'kloter',
                'scanned_at'
            ]);

        return response()->json($scans);
    }

    public function store(Request $request)
    {
        // =============================
        // AUTH (TOURLEADER)
        // =============================
        $user = auth('tourleader')->user();
        if (!$user) {
            return response()->json([
                'status'  => 'error',
                'code'    => 401,
                'message' => 'Unauthorized',
            ], 401);
        }

        // =============================
        // NORMALISASI PAYLOAD
        // =============================
        $payload = $request->all();

        // =============================
        // PARSE QR PASPOR (FORMAT KOPER)
        // =============================
        if ($request->filled('qr_text')) {
            $payload = array_merge(
                $payload,
                $this->parsePassportText($request->string('qr_text'))
            );
        }

        // Support Flutter camelCase
        if (!empty($payload['passportNumber']) && empty($payload['passport_number'])) {
            $payload['passport_number'] = $payload['passportNumber'];
        }

        // =============================
        // VALIDATION
        // =============================
        $data = validator($payload, [
            'passport_number' => 'required|string|max:255',
            'owner_name'      => 'nullable|string|max:255',
            'owner_phone'     => 'nullable|string|max:30',
            'kloter'          => 'nullable|string|max:255',
            'scanned_at'      => 'nullable|date',
        ])->validate();

        // =============================
        // NORMALISASI PASPOR (KUNCI)
        // =============================
        $passport = strtoupper(
            preg_replace('/[^A-Z0-9]/', '', trim($data['passport_number']))
        );

        // =============================
        // DUPLICATE CHECK (PER HARI)
        // =============================
        $exists = PassportScan::where('passport_number', $passport)
            ->whereDate('scanned_at', today('Asia/Jakarta'))
            ->first();

        if ($exists) {
            return response()->json([
                'status'  => 'error',
                'code'    => 409,
                'message' => 'Paspor ini sudah discan hari ini.',
                'data'    => [
                    'passport_number' => $passport,
                    'scanned_at'      => $exists->scanned_at->format('Y-m-d H:i:s'),
                ],
            ], 409);
        }

        // =============================
        // SAVE SCAN (MANDIRI, TANPA JAMAAH)
        // =============================
        $scan = PassportScan::create([
            'passport_number' => $passport,

            // DATA LANGSUNG DARI QR
            'owner_name'  => $data['owner_name'] ?? 'UNKNOWN',
            'owner_phone' => $data['owner_phone'] ?? null,
            'kloter'      => $data['kloter'] ?? null,

            // AUDIT
            'tourleader_id' => $user->id,
            'scanned_at'    => isset($data['scanned_at'])
                ? Carbon::parse($data['scanned_at'])->setTimezone('Asia/Jakarta')
                : now('Asia/Jakarta'),
        ]);

        // =============================
        // RESPONSE SUCCESS
        // =============================
        return response()->json([
            'status'  => 'success',
            'message' => 'Scan paspor berhasil disimpan.',
            'data'    => [
                'passport_number' => $scan->passport_number,
                'owner_name'      => $scan->owner_name,
                'owner_phone'     => $scan->owner_phone,
                'kloter'          => $scan->kloter,
                'scanned_at'      => $scan->scanned_at->format('Y-m-d H:i:s'),
            ],
        ], 201);
    }

    /**
     * =============================
     * PARSE QR PASPOR (FORMAT:
     * NO_PASPOR | NAMA | HP | KLOTER)
     * =============================
     */
    private function parsePassportText(string $raw): array
    {
        $raw = trim($raw);
        if ($raw === '') return [];

        // JSON (kalau suatu saat dipakai)
        if ($this->isJson($raw)) {
            $j = json_decode($raw, true);
            return [
                'passport_number' => $j['passport']
                    ?? $j['passport_number']
                    ?? null,
                'owner_name'  => $j['name'] ?? null,
                'owner_phone' => $j['phone'] ?? null,
                'kloter'      => $j['kloter'] ?? null,
            ];
        }

        // PLAIN TEXT (FORMAT KOPER)
        $parts = array_map('trim', explode('|', $raw));

        return [
            'passport_number' => $parts[0] ?? null,
            'owner_name'      => $parts[1] ?? null,
            'owner_phone'     => $parts[2] ?? null,
            'kloter'          => $parts[3] ?? null,
        ];
    }

    /**
     * =============================
     * JSON CHECK
     * =============================
     */
    private function isJson(string $s): bool
    {
        json_decode($s);
        return json_last_error() === JSON_ERROR_NONE;
    }

    public function destroy(PassportScan $passportScan)
    {
        $tourleaderId = auth('tourleader')->id();

        if ($passportScan->tourleader_id !== $tourleaderId) {
            return response()->json([
                'status' => 'error',
                'message' => 'Forbidden'
            ], 403);
        }

        $passportScan->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Scan paspor berhasil dihapus.'
        ]);
    }
}
