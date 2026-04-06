<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Controllers
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\NotificationController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\TourLeaderController;
use App\Http\Controllers\Admin\ScanController;
use App\Http\Controllers\Admin\KloterController;
use App\Http\Controllers\Admin\TaskWizardController;
use App\Http\Controllers\Admin\TaskResultController;
use App\Http\Controllers\Admin\AttendanceController as AdminAttendanceController;
use App\Http\Controllers\Admin\ChecklistTaskController;
use App\Http\Controllers\Admin\ItineraryController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\JamaahController;
use App\Http\Controllers\Admin\SesiAbsenController;
use App\Http\Controllers\Admin\ScanPasporController;
use App\Http\Controllers\Admin\MuthawifController;
use App\Http\Controllers\Admin\AbsenMuthawifController;
/*
|--------------------------------------------------------------------------
| ROOT
|--------------------------------------------------------------------------
*/

Route::get('/', fn() => redirect('/dashboard'));

/*
|--------------------------------------------------------------------------
| AUTH (ADMIN WEB ONLY)
| TIDAK pakai Auth::routes()
|--------------------------------------------------------------------------
*/
Route::middleware('guest:web')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth:web')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| ADMIN AREA (WEB SESSION)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:web')->group(function () {

    /*
    |----------------------------------------------------------------------
    | DASHBOARD
    |----------------------------------------------------------------------
    */
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |----------------------------------------------------------------------
    | MASTER DATA
    |----------------------------------------------------------------------
    */
    Route::resource('/tourleaders', TourLeaderController::class);
    Route::resource('/muthawif', MuthawifController::class);
    Route::resource('/kloter', KloterController::class);

    /*
    |----------------------------------------------------------------------
    | JAMAAH – MASTER ABSEN
    |----------------------------------------------------------------------
    */
    Route::get('/jamaah/import', [JamaahController::class, 'importForm'])
        ->name('jamaah.importForm');

    Route::post('/jamaah/import', [JamaahController::class, 'import'])
        ->name('jamaah.import');

    Route::get('/jamaah', [JamaahController::class, 'index'])
        ->name('jamaah.index');

    Route::get(
        '/jamaah/{absen}/tourleader/{tourleader}',
        [JamaahController::class, 'detailTourleader']
    )->name('jamaah.detailTourleader');

    Route::get('/jamaah/{absen}', [JamaahController::class, 'detail'])
        ->name('jamaah.detail');

    Route::delete('/jamaah/{absen}', [JamaahController::class, 'destroy'])
        ->name('jamaah.destroy');

    /*
    |----------------------------------------------------------------------
    | RIWAYAT SCAN
    |----------------------------------------------------------------------
    */
    Route::get('/scans', [ScanController::class, 'index'])->name('scans.index');
    Route::get('/scans/export', [ScanController::class, 'export'])->name('scans.export');
    Route::delete('/scans/{scan}', [ScanController::class, 'destroy'])->name('scans.destroy');
    Route::get('/scans/{scan}/detail', [ScanController::class, 'detail'])
        ->name('scans.detail');


    // ==================================================
    // RIWAYAT SCAN PASPOR
    // ==================================================

    Route::get('/scan-paspor', [ScanPasporController::class, 'index'])
        ->name('scan-paspor.index');

    Route::get('/scan-paspor/export', [ScanPasporController::class, 'export'])
        ->name('scan-paspor.export');

    Route::get('/scan-paspor/{passportScan}', [ScanPasporController::class, 'show'])
        ->name('scan-paspor.show');

    Route::delete('/scan-paspor/{passportScan}', [ScanPasporController::class, 'destroy'])
        ->name('scan-paspor.destroy');


    /*
    |----------------------------------------------------------------------
    | ADMIN SUB-AREA
    |----------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->group(function () {

        /*
        | API helper (AJAX admin)
        */
        Route::get('/kloter/{kloter}/tourleaders', function (\App\Models\Kloter $kloter) {
            return response()->json(
                $kloter->tourleaders()->select('id', 'name')->get()
            );
        });

        /*
        | DAY & DAY ITEM
        */
        Route::put('/day/{day}', [\App\Http\Controllers\Admin\DayController::class, 'update'])
            ->name('day.update');

        Route::post('/day/{day}/item', [\App\Http\Controllers\Admin\DayItemController::class, 'store'])
            ->name('day.item.store');

        Route::put('/day/item/{item}', [\App\Http\Controllers\Admin\DayItemController::class, 'update'])
            ->name('day.item.update');

        Route::delete('/day/item/{item}', [\App\Http\Controllers\Admin\DayItemController::class, 'destroy'])
            ->name('day.item.destroy');

        /*
        | NOTIFICATIONS
        */
        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
        Route::get('notifications/create', [NotificationController::class, 'create'])->name('notifications.create');
        Route::post('notifications/send', [NotificationController::class, 'sendNotification'])->name('notifications.send');
        Route::delete('notifications/{id}', [NotificationController::class, 'destroy'])
            ->name('notifications.destroy');


        /*
        | TUGAS (WIZARD)
        */
        Route::prefix('tugas')->name('tasks.')->group(function () {
            Route::get('/', [TaskWizardController::class, 'index'])->name('index');
            Route::get('/create', [TaskWizardController::class, 'createStep1'])->name('create.step1');
            Route::post('/create', [TaskWizardController::class, 'storeStep1'])->name('store.step1');
            Route::get('/create/soal', [TaskWizardController::class, 'createStep2'])->name('create.step2');
            Route::post('/create/soal', [TaskWizardController::class, 'storeStep2'])->name('store.step2');
            Route::get('/{task}', [TaskWizardController::class, 'show'])->name('show');
            Route::get('/{task}/hasil', [TaskResultController::class, 'show'])->name('result');
            Route::get('/{task}/hasil/{tourleader}', [TaskResultController::class, 'detail'])->name('result.detail');

            // Tambahkan baris ini
            Route::delete('/{task}', [TaskWizardController::class, 'destroy'])->name('destroy');
        });

        /*
        | CEKLIS
        */
        Route::prefix('ceklis')->name('ceklis.')->group(function () {
            Route::get('/', [ChecklistTaskController::class, 'index'])->name('index');
            Route::get('/create', [ChecklistTaskController::class, 'createStep1'])->name('create.step1');
            Route::post('/create', [ChecklistTaskController::class, 'storeStep1'])->name('store.step1');
            Route::get('/create/soal', [ChecklistTaskController::class, 'createStep2'])->name('create.step2');
            Route::post('/create/soal', [ChecklistTaskController::class, 'storeStep2'])->name('store.step2');
            Route::get('/create/konfirmasi', [ChecklistTaskController::class, 'createStep3'])->name('create.step3');
            // Jika storeFinal butuh POST khusus, pastikan ini ada:
            Route::post('/create/konfirmasi', [ChecklistTaskController::class, 'storeFinal'])->name('store.final');

            Route::get('/{task}', [ChecklistTaskController::class, 'show'])->name('show');
            Route::get('/{task}/hasil', [ChecklistTaskController::class, 'result'])->name('result');
            Route::get('/{task}/hasil/{submission}', [ChecklistTaskController::class, 'hasilDetail'])->name('hasil.detail');

            // Route Delete yang ditambahkan
            Route::delete('/{task}', [ChecklistTaskController::class, 'destroy'])->name('destroy');
        });


        // ===============================
        // ITINERARY KOTA (FIXED)
        // ===============================
        Route::prefix('itinerary/kota')->name('itinerary.kota.')->group(function () {

            Route::get('/', [CityController::class, 'index'])
                ->name('index');

            Route::get('/create', [CityController::class, 'create'])
                ->name('create');

            Route::post('/', [CityController::class, 'store'])
                ->name('store');

            Route::get('/{city}/edit', [CityController::class, 'edit'])
                ->name('edit');

            Route::put('/{city}', [CityController::class, 'update'])
                ->name('update');

            Route::delete('/{city}', [CityController::class, 'destroy'])
                ->name('destroy');
        });
        /*
        | ITINERARY
        */
        Route::prefix('itinerary')->name('itinerary.')->group(function () {
            Route::get('/', [ItineraryController::class, 'index'])->name('index');
            Route::get('/form1', [ItineraryController::class, 'create'])->name('form1');
            Route::post('/form1', [ItineraryController::class, 'storeForm1'])->name('storeForm1');
            Route::get('/form2', [ItineraryController::class, 'form2'])->name('form2');
            Route::post('/form2', [ItineraryController::class, 'storeForm2'])->name('storeForm2');
            Route::get('/{itinerary}/fill-days', [ItineraryController::class, 'fillDays'])->name('fill-days');
            Route::post('/{itinerary}/save-days', [ItineraryController::class, 'saveDays'])->name('save-days');
            Route::get('/{itinerary}/fill-items', [ItineraryController::class, 'fillItems'])->name('fill-items');
            Route::post('/{itinerary}/save-items', [ItineraryController::class, 'saveItems'])->name('save-items');
            Route::get('/{itinerary}/confirm', [ItineraryController::class, 'confirm'])->name('confirm');
            Route::post('/{itinerary}/finalize', [ItineraryController::class, 'finalize'])->name('finalize');
            Route::get('/{itinerary}/edit', [ItineraryController::class, 'edit'])->name('edit');
            Route::put('/{itinerary}', [ItineraryController::class, 'update'])->name('update');
            Route::delete('/{itinerary}', [ItineraryController::class, 'destroy'])->name('destroy');
            Route::get('/{itinerary}', [ItineraryController::class, 'show'])->name('show');
        });

        /*
        | SESI ABSEN (MASTER)
        */
        Route::prefix('sesi-absen')->name('sesiabsen.')->group(function () {
            Route::get('/', [SesiAbsenController::class, 'index'])->name('index');
            Route::get('/create', [SesiAbsenController::class, 'create'])->name('create');
            Route::post('/', [SesiAbsenController::class, 'store'])->name('store');
            Route::get('/{sesiAbsen}', [SesiAbsenController::class, 'show'])->name('show');
            Route::get('/{sesiAbsen}/edit', [SesiAbsenController::class, 'edit'])->name('edit');
            Route::put('/{sesiAbsen}', [SesiAbsenController::class, 'update'])->name('update');
            Route::delete('/{sesiAbsen}', [SesiAbsenController::class, 'destroy'])->name('destroy');
            Route::get('/{sesiAbsen}/items', [SesiAbsenController::class, 'items'])->name('items');
        });

        /*
        | KEHADIRAN
        */
        Route::get('/attendances', [AdminAttendanceController::class, 'index'])
            ->name('attendances.index');

        Route::delete(
            '/attendances/{id}',
            [AdminAttendanceController::class, 'destroy']
        )
            ->name('attendances.destroy');

        Route::get('/absensi-muthawif', [AbsenMuthawifController::class, 'index'])
            ->name('absensi.muthawif.index');

        Route::delete('/absensi-muthawif/{id}', [AbsenMuthawifController::class, 'destroy'])
            ->name('absensi.muthawif.destroy');
    });
});
