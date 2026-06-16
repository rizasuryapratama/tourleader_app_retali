<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ItineraryDay;
use App\Models\ItineraryItem;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class DayItemController extends Controller
{
    /** Tambah item baru (AJAX) */
    /** Tambah item baru */
    public function store(Request $request, ItineraryDay $day): RedirectResponse
    {
        $data = $request->validate([
            'start_time' => 'nullable|date_format:H:i',
            'end_time'   => 'nullable|date_format:H:i',
            'title'      => 'required|string|max:150',
            'content'    => 'nullable|string',
        ]);

        $lastSeq = $day->items()->max('sequence') ?? 0;

        $item = ItineraryItem::create([
            'itinerary_day_id' => $day->id,
            'sequence'         => $lastSeq + 1,
            'start_time'       => $data['start_time'] ?? null,
            'end_time'         => $data['end_time'] ?? null,
            'title'            => $data['title'],
            'content'          => $data['content'] ?? null,
        ]);

        $day->update(['item_count' => $day->items()->count()]);

        return redirect()
            ->route('admin.itinerary.edit', $day->itinerary_id)
            ->with('ok', 'Kegiatan berhasil ditambahkan.');
    }

    /** Update item (AJAX) */
    public function update(Request $request, ItineraryItem $item): RedirectResponse
    {
        $data = $request->validate([
            'start_time' => 'nullable|date_format:H:i',
            'end_time'   => 'nullable|date_format:H:i',
            'title'      => 'required|string|max:150',
            'content'    => 'nullable|string',
        ]);

        $item->update($data);

        return redirect()
            ->route('admin.itinerary.edit', $item->day->itinerary_id)
            ->with('ok', 'Kegiatan berhasil diperbarui.');
    }

    /** Hapus item (AJAX) */
    public function destroy(ItineraryItem $item): RedirectResponse
    {
        $day = $item->day;
        $item->delete();

        $day->items()->orderBy('sequence')->get()
            ->each(fn($it, $idx) => $it->update(['sequence' => $idx + 1]));

        $day->update(['item_count' => $day->items()->count()]);

        return redirect()
            ->route('admin.itinerary.edit', $day->itinerary_id)
            ->with('ok', 'Kegiatan berhasil dihapus.');
    }
}
