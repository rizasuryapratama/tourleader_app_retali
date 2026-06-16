<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Itinerary;
use Illuminate\Http\Request;
use App\Http\Resources\ItineraryResource;

class ItineraryApiController extends Controller
{
    /**
     * PUBLIC LIST /api/itinerary
     */
    public function index(Request $request)
    {
        $auth = $request->user();

        if ($auth instanceof \App\Models\TourLeader) {
            $q = Itinerary::query()
                ->with(['tourLeaders', 'muthawifs'])
                ->whereHas('tourLeaders', function ($qr) use ($auth) {
                    $qr->where('tour_leader_id', $auth->id);
                })
                ->withCount('days')
                ->latest();
        } else {
            $q = Itinerary::query()
                ->with(['tourLeaders', 'muthawifs'])
                ->withCount('days')
                ->latest();
        }

        if ($search = trim((string) $request->query('q', ''))) {
            $q->where('title', 'like', "%{$search}%");
        }

        return ItineraryResource::collection($q->paginate(15));
    }

    /**
     * PUBLIC SHOW /api/itinerary/{id}
     */
    public function show(Itinerary $itinerary, Request $request)
    {
        $auth = $request->user();

        if ($auth instanceof \App\Models\TourLeader) {
            $allowed = $itinerary->tourLeaders()
                ->where('tour_leader_id', $auth->id)
                ->exists();
            if (!$allowed) {
                return response()->json(['message' => 'Forbidden'], 403);
            }
        }

        $itinerary->load(['days.items', 'tourLeaders']);
        return new ItineraryResource($itinerary);
    }

    /**
     * DELETE
     */
    public function destroy(Itinerary $itinerary)
    {
        $itinerary->days()->each(fn($day) => $day->items()->delete());
        $itinerary->days()->delete();
        $itinerary->tourLeaders()->detach();
        $itinerary->muthawifs()->detach();
        $itinerary->delete();

        return response()->json(['message' => 'Itinerary deleted']);
    }

    /**
     * TOUR LEADER — List
     */
    public function tlList(Request $request)
    {
        $tl = $request->user();
        if (!$tl) return response()->json(['message' => 'Unauthenticated'], 401);

        $q = Itinerary::query()
            ->with(['tourLeaders', 'muthawifs'])
            ->whereHas('tourLeaders', function ($qr) use ($tl) {
                $qr->where('tour_leader_id', $tl->id);
            })
            ->withCount('days')
            ->latest();

        if ($search = trim((string) $request->query('q', ''))) {
            $q->where('title', 'like', "%{$search}%");
        }

        return ItineraryResource::collection($q->paginate(15));
    }

    /**
     * TOUR LEADER — Show
     */
    public function tlShow(Itinerary $itinerary, Request $request)
    {
        $tl = $request->user();

        if (!$tl) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $allowed = $itinerary->tourLeaders()
            ->where('tour_leader_id', $tl->id)
            ->exists();

        if (!$allowed) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $itinerary->load(['days.items', 'tourLeaders', 'muthawifs']);
        return new ItineraryResource($itinerary);
    }

    /**
     * MUTHAWIF — List
     */
    public function mwList(Request $request)
    {
        $mw = $request->user();
        if (!$mw) return response()->json(['message' => 'Unauthenticated'], 401);

        $q = Itinerary::query()
            ->with(['tourLeaders', 'muthawifs'])
            ->whereHas('muthawifs', function ($qr) use ($mw) {
                $qr->where('muthawif_id', $mw->id);
            })
            ->withCount('days')
            ->latest();

        if ($search = trim((string) $request->query('q', ''))) {
            $q->where('title', 'like', "%{$search}%");
        }

        return ItineraryResource::collection($q->paginate(15));
    }

    /**
     * MUTHAWIF — Show
     */
    public function mwShow(Itinerary $itinerary, Request $request)
    {
        $mw = $request->user();

        if (!$mw) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $allowed = $itinerary->muthawifs()
            ->where('muthawif_id', $mw->id)
            ->exists();

        if (!$allowed) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $itinerary->load(['days.items', 'muthawifs', 'tourLeaders']);
        return new ItineraryResource($itinerary);
    }
}