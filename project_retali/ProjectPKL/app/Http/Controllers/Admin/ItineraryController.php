<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Itinerary;
use App\Models\ItineraryDay;
use App\Models\ItineraryItem;
use App\Models\TourLeader;
use App\Models\Muthawif;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use App\Models\City;

class ItineraryController extends Controller
{
    // ── INDEX ──────────────────────────────────────────────
    public function index()
    {
        $itineraries = Itinerary::withCount('days')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.itinerary.index', compact('itineraries'));
    }

    // ── CREATE (Single Page) ───────────────────────────────
    public function create()
    {
        $tourLeaders = TourLeader::with('kloter')
            ->orderBy('name')
            ->get();

        $muthawifs = Muthawif::with('kloter')
            ->orderBy('nama')
            ->get();

        return view('admin.itinerary.create', compact('tourLeaders', 'muthawifs'));
    }

    // ── STORE (Single Submit dari Create Page) ─────────────
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title'                => 'required|string|max:150',
            'start_date'           => 'required|date|after_or_equal:today',
            'end_date'             => 'required|date|after_or_equal:start_date',
            'send_to'              => 'required|in:all_users,all_tourleaders,all_muthawif,selected',
            'selected_users'       => 'array',
            'selected_users.*'     => 'string',   // format: "tl:{id}" atau "mw:{id}"
            'days'                 => 'required|array|min:1',
            'days.*.day_number'    => 'required|integer|min:1',
            'days.*.city'          => 'required|string|max:120',
            'days.*.date'          => 'required|date',
            'days.*.items'         => 'required|array|min:1',
            'days.*.items.*.start_time' => 'nullable|date_format:H:i',
            'days.*.items.*.end_time'   => 'nullable|date_format:H:i',
            'days.*.items.*.title' => 'required|string|max:150',
            'days.*.items.*.content' => 'nullable|string',
        ]);

        if ($data['send_to'] === 'selected') {
            $request->validate([
                'selected_users' => 'required|array|min:1',
            ]);
        }

        $itinerary = null;

        DB::transaction(function () use ($data, &$itinerary) {
            // 1) Buat itinerary
            $itinerary = Itinerary::create([
                'title'      => $data['title'],
                'start_date' => $data['start_date'],
                'end_date'   => $data['end_date'],
                'send_to'    => $data['send_to'],
                'status'     => 'draft',
            ]);

            // 2) Sync penerima
            $this->syncRecipients($itinerary, $data['send_to'], $data['selected_users'] ?? []);

            // 3) Buat days & items
            foreach ($data['days'] as $dayData) {
                $day = ItineraryDay::create([
                    'itinerary_id' => $itinerary->id,
                    'day_number'   => $dayData['day_number'],
                    'city'         => $dayData['city'],
                    'date'         => $dayData['date'],
                    'item_count'   => count($dayData['items']),
                ]);

                foreach ($dayData['items'] as $seq => $itemData) {
                    ItineraryItem::create([
                        'itinerary_day_id' => $day->id,
                        'sequence'         => $seq + 1,
                        'start_time' => $itemData['start_time'] ?? null,
                        'end_time'   => $itemData['end_time'] ?? null,
                        'title'            => $itemData['title'],
                        'content'          => $itemData['content'] ?? null,
                    ]);
                }
            }
        });

        return redirect()
            ->route('admin.itinerary.confirm', $itinerary)
            ->with('ok', 'Itinerary berhasil dibuat. Silakan konfirmasi sebelum dikirim.');
    }

    // ── CONFIRM ────────────────────────────────────────────
    public function confirm(Itinerary $itinerary)
    {
        $itinerary->load('days.items', 'tourLeaders', 'muthawifs');

        return view('admin.itinerary.confirm', compact('itinerary'));
    }

    // ── FINALIZE (Kirim) ───────────────────────────────────
    public function finalize(Itinerary $itinerary): RedirectResponse
    {
        $itinerary->update(['status' => 'sent']);

        // TODO: dispatch notification/push ke penerima

        return redirect()
            ->route('admin.itinerary.show', $itinerary)
            ->with('ok', 'Itinerary berhasil dikirim ke penerima.');
    }

    // ── SHOW ───────────────────────────────────────────────
    public function show(Itinerary $itinerary)
    {
        $itinerary->load('days.items', 'tourLeaders', 'muthawifs');

        return view('admin.itinerary.show', compact('itinerary'));
    }

    // ── EDIT ───────────────────────────────────────────────
    public function edit(Itinerary $itinerary)
    {
        $itinerary->load('days.items', 'tourLeaders', 'muthawifs');

        $tourLeaders = TourLeader::orderBy('name')->get();
        $muthawifs   = Muthawif::orderBy('nama')->get();

        // TAMBAHAN
        $cities = City::orderBy('name')->get();

        return view('admin.itinerary.edit', compact(
            'itinerary',
            'tourLeaders',
            'muthawifs',
            'cities'
        ));
    }

    // ── UPDATE ─────────────────────────────────────────────
    public function update(Request $request, Itinerary $itinerary): RedirectResponse
    {
        $data = $request->validate([
            'title'            => 'required|string|max:150',
            'start_date'       => 'required|date',
            'end_date'         => 'required|date|after_or_equal:start_date',
            'send_to'          => 'required|in:all_users,all_tourleaders,all_muthawif,selected',
            'selected_users'   => 'array',
            'selected_users.*' => 'string',

            'new_days'                      => 'nullable|array',
            'new_days.*.city'               => 'nullable|string|max:120',
            'new_days.*.date'               => 'nullable|date',
            'new_days.*.items'              => 'nullable|array',
            'new_days.*.items.*.start_time' => 'nullable|date_format:H:i',
            'new_days.*.items.*.end_time'   => 'nullable|date_format:H:i',
            'new_days.*.items.*.content'    => 'nullable|string',
            'new_days.*.items.*.title'      => 'nullable|string|max:150',

        ]);

        if ($data['send_to'] === 'selected') {
            $request->validate([
                'selected_users' => 'required|array|min:1',
            ]);
        }

        DB::transaction(function () use ($itinerary, $data, $request) {

            $oldTotalDays = \Carbon\Carbon::parse($itinerary->start_date)
                ->diffInDays(\Carbon\Carbon::parse($itinerary->end_date)) + 1;

            $newTotalDays = \Carbon\Carbon::parse($data['start_date'])
                ->diffInDays(\Carbon\Carbon::parse($data['end_date'])) + 1;

            $itinerary->update([
                'title'      => $data['title'],
                'start_date' => $data['start_date'],
                'end_date'   => $data['end_date'],
                'send_to'    => $data['send_to'],
            ]);

            // ── SHIFT DAYS DATES ─────────────────────────────────────
            $startDate = \Carbon\Carbon::parse($data['start_date']);
            foreach ($itinerary->days()->reorder()->orderBy('day_number')->get() as $day) {
                $expectedDate = $startDate->copy()->addDays($day->day_number - 1);
                if (!$day->date || !$day->date->eq($expectedDate)) {
                    $day->update(['date' => $expectedDate]);
                }
            }

            // ── TAMBAH HARI ──────────────────────────────────────────
            if ($newTotalDays > $oldTotalDays) {
                $lastDay = \App\Models\ItineraryDay::where('itinerary_id', $itinerary->id)->orderByDesc('day_number')->first();
                $lastDate = $lastDay?->date
                    ? \Carbon\Carbon::parse($lastDay->date)
                    : \Carbon\Carbon::parse($data['start_date']);

                for ($i = $oldTotalDays + 1; $i <= $newTotalDays; $i++) {
                    $newDate    = $lastDate->copy()->addDays($i - $oldTotalDays);
                    $newDayData = $data['new_days'][$i] ?? [];

                    $day = ItineraryDay::create([
                        'itinerary_id' => $itinerary->id,
                        'day_number'   => $i,
                        'city'         => $newDayData['city'] ?? null,
                        'date'         => $newDayData['date'] ?? $newDate,
                        'item_count'   => 0,
                    ]);

                    if (!empty($newDayData['items'])) {
                        $seq = 1;
                        foreach ($newDayData['items'] as $itemData) {
                            // Skip hanya kalau judul juga kosong
                            if (empty($itemData['title']) && empty($itemData['content']) && empty($itemData['start_time'])) {
                                continue;
                            }
                            ItineraryItem::create([
                                'itinerary_day_id' => $day->id,
                                'sequence'         => $seq++,
                                'start_time'       => $itemData['start_time'] ?? null,
                                'end_time'         => $itemData['end_time']   ?? null,
                                'title'            => $itemData['title'] ?? $itemData['content'] ?? '',
                                'content'          => $itemData['content']    ?? null,
                            ]);
                        }
                        $day->update(['item_count' => $day->items()->count()]);
                    }
                }
            }

            // ── KURANGI HARI ─────────────────────────────────────────
            if ($newTotalDays < $oldTotalDays) {
                $daysToDelete = $itinerary->days()
                    ->where('day_number', '>', $newTotalDays)
                    ->get();

                foreach ($daysToDelete as $day) {
                    $day->items()->delete();
                    $day->delete();
                }
            }

            // ── SYNC RECIPIENTS ──────────────────────────────────────
            $this->syncRecipients(
                $itinerary,
                $data['send_to'],
                $data['selected_users'] ?? []
            );
        });

        if ($request->filled('_reduce_days')) {
            $totalHari = (int) $request->input('_reduce_days');
            return redirect()
                ->route('admin.itinerary.edit', $itinerary)
                ->with('ok', "Tanggal berhasil dikurangi, itinerary sekarang menjadi {$totalHari} hari.");
        }

        return redirect()
            ->route('admin.itinerary.confirm', $itinerary)
            ->with('ok', 'Itinerary berhasil diperbarui. Silakan konfirmasi sebelum dikirim.');
    }


    public function destroy(Itinerary $itinerary): RedirectResponse
    {
        DB::transaction(function () use ($itinerary) {
            $itinerary->tourLeaders()->detach();
            $itinerary->muthawifs()->detach();

            foreach ($itinerary->days as $day) {
                $day->items()->delete();
            }
            $itinerary->days()->delete();
            $itinerary->delete();
        });

        return redirect()
            ->route('admin.itinerary.index')
            ->with('ok', 'Itinerary berhasil dihapus.');
    }

    // ── PRIVATE HELPER ─────────────────────────────────────
    private function syncRecipients(Itinerary $itinerary, string $sendTo, array $selectedUsers): void
    {
        if ($sendTo === 'all_tourleaders' || $sendTo === 'all_users') {
            $itinerary->tourLeaders()->sync(TourLeader::pluck('id'));
        } elseif ($sendTo === 'selected') {
            $tlIds = collect($selectedUsers)
                ->filter(fn($u) => str_starts_with($u, 'tl:'))
                ->map(fn($u) => (int) str_replace('tl:', '', $u));
            $itinerary->tourLeaders()->sync($tlIds);
        } else {
            $itinerary->tourLeaders()->sync([]);
        }

        if ($sendTo === 'all_muthawif' || $sendTo === 'all_users') {
            $itinerary->muthawifs()->sync(Muthawif::pluck('id'));
        } elseif ($sendTo === 'selected') {
            $mwIds = collect($selectedUsers)
                ->filter(fn($u) => str_starts_with($u, 'mw:'))
                ->map(fn($u) => (int) str_replace('mw:', '', $u));
            $itinerary->muthawifs()->sync($mwIds);
        } else {
            $itinerary->muthawifs()->sync([]);
        }
    }
}
