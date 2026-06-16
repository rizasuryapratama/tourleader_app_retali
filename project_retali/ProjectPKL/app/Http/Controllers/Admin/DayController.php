<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ItineraryDay;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class DayController extends Controller
{
    /** Update city & date (AJAX) */
    public function update(Request $request, ItineraryDay $day): JsonResponse
    {
        $data = $request->validate([
            'city' => 'required|string|max:120',
            'date' => 'nullable|date',
        ]);

        $day->update($data);
        $day->load('items');

        return response()->json([
            'status'  => $day->status,
            'message' => 'Data hari diperbarui.',
        ]);
    }

    public function destroy(ItineraryDay $day): \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
    {
        $itinerary = $day->itinerary;

        $day->items()->delete();
        $day->delete();

        $remainingDays = $itinerary->days()->orderBy('day_number')->get();
        $startDate = \Carbon\Carbon::parse($itinerary->start_date);

        foreach ($remainingDays as $index => $remainingDay) {
            $remainingDay->update([
                'day_number' => $index + 1,
                'date'       => $startDate->copy()->addDays($index)->toDateString(),
            ]);
        }

        $newTotalDays = $remainingDays->count();
        if ($newTotalDays > 0) {
            $itinerary->update([
                'end_date' => $startDate->copy()->addDays($newTotalDays - 1)->toDateString(),
            ]);
        }

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['status' => 'ok', 'message' => 'Hari berhasil dihapus.']);
        }

        return redirect()
            ->route('admin.itinerary.edit', $itinerary)
            ->with('ok', 'Hari berhasil dihapus.');
    }
}
