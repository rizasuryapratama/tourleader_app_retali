<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\Notification;
use App\Models\Tourleader;
use App\Models\Muthawif;
use App\Mail\ReportMasukMail;
use Google\Client as GoogleClient;

class NotificationController extends Controller
{
    private string $projectId;
    private string $credentialsPath;

    public function __construct()
    {
        $this->projectId = 'retali-project';
        $this->credentialsPath = storage_path('app/firebase/retali-project-firebase.json');
    }

    private function getAccessToken(): string
    {
        $client = new GoogleClient();
        $client->setAuthConfig($this->credentialsPath);
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');
        $client->refreshTokenWithAssertion();
        $token = $client->getAccessToken();
        return $token['access_token'];
    }

    public function index()
    {
        $notifications = Notification::latest()->get();
        return view('admin.notifications.index', compact('notifications'));
    }

    public function create()
    {
        return view('admin.notifications.create');
    }

    /**
     * Kirim Notifikasi Broadcast (Anti-Duplikat di Admin)
     */
    public function sendNotification(Request $request)
    {
        $request->validate([
            'title'   => 'required|string',
            'message' => 'required|string',
        ]);

        // 1. Ambil semua data target
        $tourleaders = Tourleader::all();
        $muthawifs = Muthawif::all();

        Log::info('--- Memulai Broadcast ke Semua TL & Muthawif ---');

        // 2. Simpan cuma 1 baris ke Database untuk riwayat Admin
        // user_id 0 menandakan broadcast umum
        $notification = Notification::create([
            'user_id'   => 0,
            'title'     => $request->title,
            'message'   => $request->message,
            'is_active' => true,
        ]);

        // 3. Looping kirim email ke Tour Leader
        foreach ($tourleaders as $tl) {
            $this->onlySendEmail($tl, $request->title, $request->message);
        }

        // 4. Looping kirim email ke Muthawif
        foreach ($muthawifs as $mu) {
            $this->onlySendEmail($mu, $request->title, $request->message);
        }

        // 5. Kirim Push Notification (Popup HP)
        $this->sendFcmBroadcast($request->title, $request->message, $notification->id);

        return redirect()->route('admin.notifications.index')
            ->with('success', 'Broadcast berhasil dikirim ke semua TL & Muthawif!');
    }

    /**
     * Hapus Riwayat Notifikasi
     */
    public function destroy($id)
    {
        try {
            $notification = Notification::findOrFail($id);
            $notification->delete();
            return redirect()->route('admin.notifications.index')
                ->with('success', 'Notifikasi berhasil dihapus.');
        } catch (\Exception $e) {
            Log::error("Gagal hapus notif ID $id: " . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus notifikasi.');
        }
    }

    /**
     * Helper: Hanya kirim email (Tanpa simpan DB lagi)
     */
    private function onlySendEmail($user, $title, $message)
    {
        try {
            Mail::to($user->email)->send(new ReportMasukMail(
                $user->name ?? $user->nama, // Mendukung kolom 'name' (TL) atau 'nama' (Muthawif)
                $message,
                $title
            ));
            Log::info('Email Berhasil dikirim ke: ' . $user->email);
        } catch (\Exception $e) {
            Log::error('Email Gagal ke ' . $user->email . ': ' . $e->getMessage());
        }
    }

    /**
     * Helper: FCM Broadcast (V1)
     */
    private function sendFcmBroadcast($title, $message, $notifId)
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) return;

        $payload = [
            'message' => [
                'topic' => 'all',
                'data' => [
                    'title' => $title,
                    'body'  => $message,
                    'type'  => 'broadcast',
                    'notification_id' => (string) $notifId,
                ],
                'android' => ['priority' => 'high'],
            ],
        ];

        try {
            $url = "https://fcm.googleapis.com/v1/projects/{$this->projectId}/messages:send";
            Http::withToken($accessToken)->post($url, $payload);
            Log::info('FCM Broadcast Berhasil dikirim.');
        } catch (\Exception $e) {
            Log::error('FCM Broadcast Gagal: ' . $e->getMessage());
        }
    }
}
