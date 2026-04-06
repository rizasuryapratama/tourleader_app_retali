<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// ==================== CONTROLLERS ====================

use App\Http\Controllers\Api\ScanController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\TourLeaderController;
use App\Http\Controllers\FCMTokenController;
use App\Http\Controllers\Api\TaskApiController;
use App\Http\Controllers\Api\ChecklistTaskApiController;
use App\Http\Controllers\Api\ChecklistSubmitController;
use App\Http\Controllers\Api\ItineraryApiController;
use App\Http\Controllers\Api\PassportScanController;
use App\Http\Controllers\Api\MuthawifApiController;
use App\Http\Controllers\Api\AbsenMuthawifController;
use App\Http\Controllers\Api\LoginController;

// **ABSEN JAMAAH**
use App\Http\Controllers\Api\AttendanceJamaahController;


// **ABSEN TOURLEADER**
use App\Http\Controllers\Api\AttendanceController;


// ======================================================
// ===============  USER LOGIN ==========================
// ======================================================
Route::post('/login', [LoginController::class, 'login']);

// ======================================================
// ===============  ITINERARY PUBLIC ====================
// ======================================================
Route::get('/itinerary', [ItineraryApiController::class, 'index']);
Route::get('/itinerary/{itinerary}', [ItineraryApiController::class, 'show']);


// ======================================================
// ===============  AUTH SANCTUM USER ====================
// ======================================================
Route::middleware('auth:sanctum')->group(function () {

    // SCAN
    Route::get('/scans', [ScanController::class, 'index']);
    Route::post('/scans', [ScanController::class, 'store']);

    // ==================================================
    // SCAN PASPOR (GLOBAL – 1 PASPOR = 1 SCAN)
    // ==================================================
    Route::post('/passport-scan', [PassportScanController::class, 'store']);

    Route::post(
        '/tourleader/passport-scan',
        [PassportScanController::class, 'store']
    );



    // FCM
    Route::post('/save-fcm-token', function (Request $request) {
        $data = $request->validate([
            'fcm_token' => 'required',
            'platform'  => 'nullable',
        ]);

        $request->user()->update(['fcm_token' => $data['fcm_token']]);

        return response()->json(['success' => true]);
    });

    // NOTIF
    Route::get('/notifications', [NotificationController::class, 'list']);
    Route::post('/notifications/send', [NotificationController::class, 'send']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);

    // ITINERARY USER
    Route::post('/itinerary', [ItineraryApiController::class, 'store']);
    Route::put('/itinerary/{itinerary}', [ItineraryApiController::class, 'updateHeader']);
    Route::put('/itinerary/{itinerary}/day-config', [ItineraryApiController::class, 'setDayConfig']);
    Route::put('/itinerary/{itinerary}/days/{dayNumber}', [ItineraryApiController::class, 'fillDay']);
    Route::delete('/itinerary/{itinerary}', [ItineraryApiController::class, 'destroy']);

    // ABSENSI TOUR LEADER (ABSEN KERJA TL)
    Route::post('/attendance', [AttendanceController::class, 'store']);
    Route::get('/attendance', [AttendanceController::class, 'myHistory']);
});



// ======================================================
// ===============  TOUR LEADER AUTH =====================
// ======================================================
//Route::post('/tourleader/login', [TourLeaderController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/tourleader/profile', [TourLeaderController::class, 'profile']);

    // SCAN
    Route::get('/tourleader/scans', [ScanController::class, 'index']);
    Route::post('/tourleader/scans', [ScanController::class, 'store']);
    Route::delete('/tourleader/scans/{scan}', [ScanController::class, 'destroy']);

    Route::get('/tourleader/passport-scans', [PassportScanController::class, 'index']);
    Route::delete('/tourleader/passport-scans/{passportScan}', [PassportScanController::class, 'destroy']);



    // FCM TL
    Route::post('/tourleader/fcm-token', [FCMTokenController::class, 'store']);
    Route::delete('/tourleader/fcm-token', [FCMTokenController::class, 'destroy']);

    // TASK TL
    Route::get('/tourleader/tasks', [TaskApiController::class, 'index']);
    Route::get('/tourleader/tasks/{task}', [TaskApiController::class, 'show']);
    Route::post('/tourleader/tasks/{task}/done', [TaskApiController::class, 'markDone']);

    // ===============================
    // TASK TL - PER SOAL (WAJIB)
    // ===============================

    // Ambil status per soal (Sudah / Belum)
    Route::get(
        '/tourleader/tasks/{task}/answers',
        [TaskApiController::class, 'answers']
    );

    // Tandai soal = Sudah
    Route::post(
        '/tourleader/tasks/{task}/questions/{question}/answer',
        [TaskApiController::class, 'answer']
    );

    // Tandai soal = Belum
    Route::delete(
        '/tourleader/tasks/{task}/questions/{question}/answer',
        [TaskApiController::class, 'unanswer']
    );


    // CHECKLIST TL
    Route::get('/tourleader/checklist', [ChecklistTaskApiController::class, 'index']);
    Route::get('/tourleader/checklist/{task}', [ChecklistTaskApiController::class, 'show']);
    Route::post('/tourleader/checklist/{task}/submit', [ChecklistSubmitController::class, 'submit']);

    // ITINERARY TL
    Route::get('/tourleader/itinerary', [ItineraryApiController::class, 'tlList']);
    Route::get('/tourleader/itinerary/{itinerary}', [ItineraryApiController::class, 'tlShow']);

    // ======================================================
    // ABSENSI JAMAAH - PER JAMAAH (STATUS + CATATAN)
    // ======================================================

    // LIST ABSEN (HOME)
    Route::get(
        '/tourleader/attendance-jamaah',
        [AttendanceJamaahController::class, 'index']
    );

    // DETAIL ABSEN + LIST JAMAAH  ⬅️ WAJIB ADA
    Route::get(
        '/tourleader/attendance-jamaah/{id}',
        [AttendanceJamaahController::class, 'show']
    );

    // UPDATE STATUS + CATATAN
    Route::post(
        '/tourleader/attendance-jamaah',
        [AttendanceJamaahController::class, 'update']
    );

    Route::post(
        '/tourleader/attendance-jamaah/bulk',
        [AttendanceJamaahController::class, 'bulkUpdate']
    );

    // ======================================================
    // ========== ABSENSI TOUR LEADER (ABSEN DIRI SENDIRI) ==
    // ======================================================
    // ABSENSI TOUR LEADER (ABSEN DIRI SENDIRI)
    Route::post('/tourleader/attendance', [AttendanceController::class, 'store']);  // <= WAJIB ADA
    Route::get('/tourleader/attendance', [AttendanceController::class, 'myHistory']);
});


// ======================
// MUTHAWIF LOGIN
// ======================
//Route::post('/muthawif/login', [MuthawifApiController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/muthawif/profile', [MuthawifApiController::class, 'profile']);
    Route::post('/muthawif/logout', [MuthawifApiController::class, 'logout']);

    
    Route::post('/muthawif/attendance', [AbsenMuthawifController::class, 'store']);
    Route::get('/muthawif/attendance', [AbsenMuthawifController::class, 'history']);
});


