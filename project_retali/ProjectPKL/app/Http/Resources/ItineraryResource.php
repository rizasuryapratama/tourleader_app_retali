<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\TourLeader;

class ItineraryResource extends JsonResource
{
    public function toArray($request)
    {
        
        $auth = $request->user();
        $currentTlName = null;
        $currentMwName = null;

        if ($auth instanceof TourLeader) {
            
            $match = $this->tourLeaders->firstWhere('id', $auth->id);
            if ($match) {
                $currentTlName = $match->name;
            }
        } elseif ($auth instanceof \App\Models\Muthawif) {
            
            $match = $this->muthawifs ? $this->muthawifs->firstWhere('id', $auth->id) : null;
            if ($match) {
                $currentMwName = $match->nama;
            }
        }

        $displayTlName = $currentTlName ?: optional($this->tourLeaders->first())->name;
        $displayMwName = $currentMwName ?: ($this->muthawifs ? optional($this->muthawifs->first())->nama : null);

        return [
            'id'         => $this->id,
            'title'      => $this->title,
            'start_date' => $this->start_date,
            'end_date'   => $this->end_date,
            'tour_leader_name' => $displayTlName,
            'muthawif_name'    => $displayMwName,

            'tour_leaders' => $this->tourLeaders->map(function ($tl) {
                return [
                    'id'    => $tl->id,
                    'name'  => $tl->name,
                    'email' => $tl->email,
                ];
            }),

            'muthawifs' => $this->muthawifs ? $this->muthawifs->map(function ($mw) {
                return [
                    'id'    => $mw->id,
                    'name'  => $mw->nama,
                    'email' => $mw->email,
                ];
            }) : [],

            'send_to'         => $this->send_to,
            'recipient_label' => $this->recipient_label,
            'status'          => $this->status,

            'days' => ItineraryDayResource::collection(
                $this->whenLoaded('days')
            ),
        ];
    }
}
