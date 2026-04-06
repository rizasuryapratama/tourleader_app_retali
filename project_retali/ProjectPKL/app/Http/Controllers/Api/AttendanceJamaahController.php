<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\AbsensiJamaah;
use App\Models\AttendanceJamaah;
use App\Models\Jamaah;

class AttendanceJamaahController extends Controller
{
    /**
     * GET /api/tourleader/attendance-jamaah
     * LIST ABSENSI JAMAAH UNTUK TOUR LEADER
     */
    public function index(Request $request)
    {
        $tourleaderId = $request->user()->id;

        $absens = AbsensiJamaah::with(['kloter.tourleaders', 'sesiAbsen', 'sesiAbsenItem'])
            ->whereHas('jamaah', fn($q) => $q->where('assigned_tourleader_id', $tourleaderId))
            ->latest()
            ->get();

        $formattedAbsens = $absens->map(function ($absen) use ($tourleaderId) {

            $isDone = !AttendanceJamaah::where('absensi_jamaah_id', $absen->id)
                ->where('created_by', $tourleaderId)
                ->where('status', 'BELUM_ABSEN')
                ->exists();

            $periode = null;

            if (
                $absen->kloter &&
                $absen->kloter->tanggal_berangkat &&
                $absen->kloter->tanggal_pulang
            ) {

                $periode =
                    \Carbon\Carbon::parse($absen->kloter->tanggal_berangkat)
                    ->translatedFormat('d F')
                    . ' - ' .
                    \Carbon\Carbon::parse($absen->kloter->tanggal_pulang)
                    ->translatedFormat('d F Y');
            }

            return [
                'id' => $absen->id,
                'judul_absen' => $absen->judul_absen ?? ($absen->kloter->nama ?? 'Sesi Absen'),
                'periode_kloter' => $periode,
                'tanggal_operasional' => $absen->created_at->toDateString(),
                'jamaah_count' => $absen->jamaah()->where('assigned_tourleader_id', $tourleaderId)->count(),
                'is_done' => $isDone,
                'kloter' => $absen->kloter,
                'sesi_absen' => $absen->sesiAbsen,
                'sesi_absen_item' => $absen->sesiAbsenItem,
                'sesi_lengkap' => ($absen->sesiAbsen->judul ?? '') . ' - ' . ($absen->sesiAbsenItem->isi ?? ''),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $formattedAbsens,
        ]);
    }
    /**
     * GET /api/tourleader/attendance-jamaah/{id}
     * DETAIL ABSENSI + LIST JAMAAH
     */
    public function show(Request $request, $absenId)
    {
        $tourleaderId = $request->user()->id;

        $absen = AbsensiJamaah::where('id', $absenId)
            ->whereHas(
                'jamaah',
                fn($q) =>
                $q->where('assigned_tourleader_id', $tourleaderId)
            )
            ->with(['sesiAbsen', 'sesiAbsenItem', 'kloter'])
            ->firstOrFail();

        $jamaah = Jamaah::where('absen_id', $absen->id)
            ->where('assigned_tourleader_id', $tourleaderId)
            ->with('latestAttendance')
            ->orderBy('urutan_absen')
            ->get()
            ->map(fn($j) => [
                'jamaah_id' => $j->id,
                'urutan' => $j->urutan_absen,
                'nama_jamaah' => $j->nama_jamaah,
                'no_hp' => $j->no_hp,
                'no_paspor' => $j->no_paspor,
                'kode_kloter' => $j->kode_kloter,
                'nomor_bus' => $j->nomor_bus,
                'jenis_kelamin' => $j->jenis_kelamin,
                'tanggal_lahir' => $j->tanggal_lahir,
                'keterangan' => $j->keterangan,
                'status' => $j->latestAttendance->status ?? 'BELUM_ABSEN',
                'catatan' => $j->latestAttendance->catatan ?? null,
                'attendance_id' => $j->latestAttendance->id ?? null,
                'absen_ke' => $j->latestAttendance->absen_ke ?? 0,
                'tanggal_absen' => $j->latestAttendance->tanggal ?? null,
            ]);

        // 🔥 FORMAT SESI LENGKAP UNTUK DETAIL
        $transportasi = $absen->sesiAbsen->judul ?? 'Pesawat';
        $rute = $absen->sesiAbsenItem->isi ?? '';
        $sesiLengkap = $transportasi;
        if (!empty($rute)) {
            $sesiLengkap = $transportasi . ' - ' . $rute;
        }

        $periode = null;

        if (
            $absen->kloter &&
            $absen->kloter->tanggal_berangkat &&
            $absen->kloter->tanggal_pulang
        ) {

            $periode =
                \Carbon\Carbon::parse($absen->kloter->tanggal_berangkat)
                ->translatedFormat('d F')
                . ' - ' .
                \Carbon\Carbon::parse($absen->kloter->tanggal_pulang)
                ->translatedFormat('d F Y');
        }

        return response()->json([
            'success' => true,
            'absen' => [
                'id' => $absen->id,
                'judul' => $absen->judul_absen,
                'judul_absen' => $absen->judul_absen,
                'created_at' => $absen->created_at,
                'kloter_id' => $absen->kloter_id,
                'sesi_absen_id' => $absen->sesi_absen_id,
                'sesi_absen_item_id' => $absen->sesi_absen_item_id,
                'periode_kloter' => $absen->kloter->tanggal ?? null,
                'tanggal_operasional' => $absen->created_at->toDateString(),
                'transportasi' => $transportasi,
                'rute' => $rute,
                'sesi_lengkap' => $sesiLengkap,
                'sesi_absen' => $absen->sesiAbsen,
                'sesi_absen_item' => $absen->sesiAbsenItem,
            ],
            'jamaah' => $jamaah,
            'total_jamaah' => $jamaah->count(),
            'total_hadir' => $jamaah->filter(fn($j) => ($j['status'] ?? '') == 'HADIR')->count(),
            'total_tidak_hadir' => $jamaah->filter(fn($j) => ($j['status'] ?? '') == 'TIDAK_HADIR')->count(),
            'total_belum' => $jamaah->filter(fn($j) => ($j['status'] ?? '') == 'BELUM_ABSEN')->count(),
        ]);
    }

    /**
     * POST /api/tourleader/attendance-jamaah/bulk
     * UPDATE STATUS ABSENSI JAMAAH (BULK)
     */
    public function bulkUpdate(Request $request)
    {
        $request->validate([
            'absensi_jamaah_id' => 'required|exists:absensi_jamaah,id',
            'data' => 'required|array|min:1',
            'data.*.jamaah_id' => 'required|exists:jamaahs,id',
            'data.*.status' => 'required|in:HADIR,TIDAK_HADIR',
            'data.*.catatan' => 'nullable|string|max:500',
        ]);

        $tourleaderId = $request->user()->id;

        // 🔥 PERBAIKAN 1: Hapus semua data absensi lama (termasuk BELUM_ABSEN) 
        // untuk jamaah-jamaah ini di sesi tersebut agar tidak duplikat
        AttendanceJamaah::where('absensi_jamaah_id', $request->absensi_jamaah_id)
            ->whereIn('jamaah_id', collect($request->data)->pluck('jamaah_id'))
            ->delete();

        $insertData = [];

        foreach ($request->data as $item) {
            // Karena data lama sudah dihapus, absen_ke bisa dimulai dari 1 
            // atau kamu bisa modifikasi jika memang ingin menyimpan history lengkap.
            $insertData[] = [
                'jamaah_id' => $item['jamaah_id'],
                'absensi_jamaah_id' => $request->absensi_jamaah_id,
                'absen_ke' => 1,
                'tanggal' => now()->toDateString(),
                'status' => $item['status'],
                'catatan' => $item['catatan'],
                'created_by' => $tourleaderId,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        AttendanceJamaah::insert($insertData);

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil disimpan (bulk)',
            'total' => count($insertData)
        ]);
    }

    /**
     * POST /api/tourleader/attendance-jamaah
     * UPDATE STATUS ABSENSI JAMAAH
     */
    public function update(Request $request)
    {
        $request->validate([
            'jamaah_id' => 'required|exists:jamaahs,id',
            'absensi_jamaah_id' => 'required|exists:absensi_jamaah,id',
            'status' => 'required|in:HADIR,TIDAK_HADIR',
            'catatan' => 'nullable|string|max:500',
        ]);

        $tourleaderId = $request->user()->id;

        // ✅ VALIDASI: Pastikan jamaah ini memang milik TL ini
        $jamaah = Jamaah::where('id', $request->jamaah_id)
            ->where('assigned_tourleader_id', $tourleaderId)
            ->firstOrFail();

        // ✅ CEK: Apakah jamaah ini benar ada di sesi absen ini?
        if ($jamaah->absen_id != $request->absensi_jamaah_id) {
            return response()->json([
                'success' => false,
                'message' => 'Jamaah tidak terdaftar pada sesi absen ini'
            ], 422);
        }

        // ✅ HITUNG absen_ke
        $lastAbsenKe = AttendanceJamaah::where('jamaah_id', $request->jamaah_id)
            ->where('absensi_jamaah_id', $request->absensi_jamaah_id)
            ->orderByDesc('absen_ke')
            ->value('absen_ke') ?? 0;


        // ✅ CREATE attendance baru
        $attendance = AttendanceJamaah::create([
            'jamaah_id' => $request->jamaah_id,
            'absensi_jamaah_id' => $request->absensi_jamaah_id,
            'absen_ke' => $lastAbsenKe + 1,
            'tanggal' => now()->toDateString(),
            'status' => $request->status,
            'catatan' => $request->catatan,
            'created_by' => $tourleaderId,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Absensi berhasil disimpan',
            'data' => [
                'attendance_id' => $attendance->id,
                'jamaah_id' => $attendance->jamaah_id,
                'status' => $attendance->status,
                'catatan' => $attendance->catatan,
                'absen_ke' => $attendance->absen_ke,
                'tanggal' => $attendance->tanggal,
            ]
        ]);
    }

    /**
     * GET /api/tourleader/attendance-jamaah/stats/{absenId}
     * STATISTIK ABSENSI (OPSIONAL)
     */
    public function stats(Request $request, $absenId)
    {
        $tourleaderId = $request->user()->id;

        $total = Jamaah::where('absen_id', $absenId)
            ->where('assigned_tourleader_id', $tourleaderId)
            ->count();

        $hadir = AttendanceJamaah::where('absensi_jamaah_id', $absenId)
            ->where('created_by', $tourleaderId)
            ->where('status', 'HADIR')
            ->count();

        $tidakHadir = AttendanceJamaah::where('absensi_jamaah_id', $absenId)
            ->where('created_by', $tourleaderId)
            ->where('status', 'TIDAK_HADIR')
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'total' => $total,
                'hadir' => $hadir,
                'tidak_hadir' => $tidakHadir,
                'belum' => $total - ($hadir + $tidakHadir),
            ]
        ]);
    }
}
