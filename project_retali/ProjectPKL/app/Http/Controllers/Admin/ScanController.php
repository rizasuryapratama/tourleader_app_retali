<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Scan;
use Illuminate\Http\Request;
use App\Exports\ScansExport;
use Maatwebsite\Excel\Facades\Excel;

class ScanController extends Controller
{
    // =============================
    // INDEX (List data scan)
    // =============================
    public function index(Request $request)
    {
        $query = Scan::query()->with('tourleader:id,name');

        // 🔍 FILTER KLOTER
        if ($request->filled('kloter')) {
            $query->where('kloter', 'like', '%' . $request->kloter . '%');
        }

        // 📅 FILTER TANGGAL
        if ($request->filled('date')) {
            $query->whereDate('scanned_at', $request->date);
        }

        $scans = $query->orderBy('koper_code')->get();

        // ✅ LIST KLOTER UNIK (UNTUK DROPDOWN)
        $kloters = Scan::query()
            ->whereNotNull('kloter')
            ->select('kloter')
            ->distinct()
            ->orderBy('kloter')
            ->pluck('kloter');

        if ($request->wantsJson()) {
            return response()->json(['data' => $scans], 200);
        }

        return view('admin.scans.index', compact('scans', 'kloters'));
    }

    // =============================
    // DETAIL (AJAX MODAL)
    // =============================
    public function detail(Scan $scan)
    {
        $scan->load('tourleader');

        return response()->json([
            'koper_code'  => $scan->koper_code,
            'owner_name'  => $scan->owner_name,
            'owner_phone' => $scan->owner_phone,
            'kloter'      => $scan->kloter,
            'tourleader'  => optional($scan->tourleader)->name,
            'scanned_at'  => optional($scan->scanned_at)->format('d-m-Y H:i'),
        ]);
    }

    // =============================
    // EXPORT EXCEL (FILTER KLOTER)
    // =============================
    public function export(Request $request)
    {
        return Excel::download(
            new ScansExport(
                $request->input('kloter'),
                $request->input('date')
            ),
            'scans.xlsx'
        );
    }

    // =============================
    // STORE (SCAN BARU)
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

        $raw = trim((string) $request->input('qr_text'));
        if ($raw === '') {
            return response()->json([
                'status'  => 'error',
                'message' => 'QR tidak valid'
            ], 422);
        }

        $firstToken = trim(explode('|', $raw)[0]);

        if ($this->looksLikePassport($firstToken) || $this->isJsonPassport($raw)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'QR Paspor terdeteksi. Gunakan menu Scan Paspor.'
            ], 422);
        }

        $payload = $this->parseQr($raw);

        $data = validator($payload, [
            'koper_code'  => 'required|string|max:255',
            'owner_name'  => 'nullable|string|max:255',
            'owner_phone' => 'nullable|string|max:30',
            'kloter'      => 'nullable|string|max:255',
        ])->validate();

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

        $scan = Scan::create([
            'tourleader_id' => $tourleaderId,
            'koper_code'    => $data['koper_code'],
            'owner_name'    => $data['owner_name'] ?? null,
            'owner_phone'   => $data['owner_phone'] ?? null,
            'kloter'        => $data['kloter'] ?? null,
            'scanned_at'    => now('Asia/Jakarta'),
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Scan koper berhasil.',
            'data'    => $scan
        ], 201);
    }

    // =============================
    // DELETE
    // =============================
    public function destroy(Scan $scan)
    {
        $scan->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Data scan berhasil dihapus.'
        ]);
    }

    // =============================
    // HELPER
    // =============================
    private function parseQr(string $raw): array
    {
        if ($this->isJson($raw)) {
            $j = json_decode($raw, true);
            return [
                'koper_code'  => $j['kode'] ?? null,
                'owner_name'  => $j['nama'] ?? null,
                'owner_phone' => $j['phone'] ?? null,
                'kloter'      => $j['kloter'] ?? null,
            ];
        }

        $parts = array_map('trim', preg_split('/\||\r\n|\n|\r/', $raw));
        return [
            'koper_code'  => $parts[0] ?? null,
            'owner_name'  => $parts[1] ?? null,
            'owner_phone' => $parts[2] ?? null,
            'kloter'      => $parts[3] ?? null,
        ];
    }

    private function looksLikePassport(string $value): bool
    {
        return preg_match('/^[A-Z]{1,2}[0-9]{6,9}$/', trim($value)) === 1;
    }

    private function isJsonPassport(string $value): bool
    {
        if (!$this->isJson($value)) return false;
        $json = json_decode($value, true);
        return isset($json['passport'], $json['passport_number'], $json['passportNumber']);
    }

    private function isJson(string $s): bool
    {
        json_decode($s);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
