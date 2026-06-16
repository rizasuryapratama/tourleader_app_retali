@extends('layouts.app')

@section('content')
    <div class="show-wrap">

        {{-- HEADER --}}
        <div class="show-hero">
            <div class="show-hero-content">
                <div class="show-status-row">
                    <span class="status-chip status-chip--{{ $itinerary->status }}">
                        {{ $itinerary->status === 'sent' ? '✅ Terkirim' : '📝 Draft' }}
                    </span>
                    @if ($itinerary->start_date)
                        <span class="meta-chip">
                            📅 {{ $itinerary->start_date->format('d M Y') }}
                            @if ($itinerary->end_date)
                                — {{ $itinerary->end_date->format('d M Y') }}
                            @endif
                        </span>
                    @endif
                    <span class="meta-chip">🗓 {{ $itinerary->days->count() }} Hari</span>
                </div>
                <h1 class="show-title">{{ $itinerary->title }}</h1>
                <div class="show-recipients">
                    <span class="recipients-label">Penerima:</span>
                    @foreach ($itinerary->tourLeaders->take(4) as $tl)
                        <span class="mini-pill"
                            title="{{ $tl->kloter ? $tl->kloter->nama . ' (' . ($tl->kloter->tanggal_label ?? '') . ')' : '' }}">
                            {{ $tl->name }}
                            @if ($tl->kloter)
                                <small style="opacity:.75;font-weight:400">({{ $tl->kloter->nama }})</small>
                            @endif
                        </span>
                    @endforeach
                    @foreach ($itinerary->muthawifs->take(4) as $mw)
                        {{-- ✅ FIX #1: pakai ->nama bukan ->name, tambah kloter --}}
                        <span class="mini-pill mini-pill--mw"
                            title="{{ $mw->kloter ? $mw->kloter->nama . ' (' . ($mw->kloter->tanggal_label ?? '') . ')' : '' }}">
                            {{ $mw->nama }}
                            @if ($mw->kloter)
                                <small style="opacity:.75;font-weight:400">({{ $mw->kloter->nama }})</small>
                            @endif
                        </span>
                    @endforeach
                    @if ($itinerary->tourLeaders->count() + $itinerary->muthawifs->count() > 8)
                        <span class="mini-pill mini-pill--more">
                            +{{ $itinerary->tourLeaders->count() + $itinerary->muthawifs->count() - 8 }} lainnya
                        </span>
                    @endif
                </div>
            </div>
            <div class="show-hero-actions">
                <a href="{{ route('admin.itinerary.edit', $itinerary) }}" class="btn-edit">
                    ✏️ Edit Itinerary
                </a>
                <a href="{{ route('admin.itinerary.index') }}" class="btn-back">
                    ← Kembali
                </a>
            </div>
        </div>

        @if (session('ok'))
            <div class="alert-ok">{{ session('ok') }}</div>
        @endif

        {{-- DAYS ACCORDION --}}
        <div class="days-list" x-data="{}">
            @forelse($itinerary->days as $day)
                <div class="day-block" x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }">
                    <button class="day-block-head" @click="open = !open" type="button">
                        <div class="dbh-left">
                            <span class="dbh-dot"></span>
                            <span class="dbh-num">Hari {{ $day->day_number }}</span>
                            @if ($day->date)
                                <span class="dbh-date">{{ $day->date->format('d M Y') }}</span>
                            @endif
                            @if ($day->city)
                                <span class="dbh-city">📍 {{ $day->city }}</span>
                            @endif
                        </div>
                        <div class="dbh-right">
                            <span class="dbh-count">{{ $day->items->count() }} kegiatan</span>
                            <svg class="dbh-chevron" :class="{ 'rotated': open }" width="16" height="16"
                                viewBox="0 0 16 16" fill="none">
                                <path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                            </svg>
                        </div>
                    </button>

                    <div class="day-block-body" x-show="open" x-collapse>
                        @if ($day->items->isEmpty())
                            <p class="empty-msg">Belum ada kegiatan di hari ini.</p>
                        @else
                            <div class="items-timeline">
                                @foreach ($day->items as $item)
                                    <div class="it-row">
                                        <div class="it-time-col">
                                            <div class="it-time">
                                                <span class="it-time-icon">🕒</span>
                                                @if ($item->start_time && $item->end_time)
                                                    {{ \Carbon\Carbon::parse($item->start_time)->format('H:i') }}
                                                    -
                                                    {{ \Carbon\Carbon::parse($item->end_time)->format('H:i') }}
                                                @elseif($item->start_time)
                                                    {{ \Carbon\Carbon::parse($item->start_time)->format('H:i') }}
                                                @else
                                                    --:--
                                                @endif
                                            </div>
                                            <div class="it-line"></div>
                                        </div>
                                        <div class="it-body">
                                            @if ($item->title)
                                                <div class="it-title">{{ $item->title }}</div>
                                            @endif
                                            @if ($item->content)
                                                <div class="it-content">{!! nl2br(e($item->content)) !!}</div>
                                            @endif
                                        </div>
                                        <span class="it-seq">#{{ $item->sequence }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <p>Belum ada hari pada itinerary ini.</p>
                    <a href="{{ route('admin.itinerary.edit', $itinerary) }}" class="btn-edit">Mulai Edit</a>
                </div>
            @endforelse
        </div>

    </div>
@endsection

@push('styles')
    <style>
        .show-wrap {
            max-width: 900px;
            margin: 0 auto;
            padding: 24px 20px;
            display: flex;
            flex-direction: column;
            gap: 20px
        }

        /* Hero */
        .show-hero {
            background: linear-gradient(135deg, #0f172a, #1e3a8a);
            border-radius: 20px;
            padding: 32px;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 24px
        }

        .show-status-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 12px;
            flex-wrap: wrap
        }

        .status-chip {
            font-size: .78rem;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 20px
        }

        .status-chip--sent {
            background: #d1fae5;
            color: #065f46
        }

        .status-chip--draft {
            background: #fef3c7;
            color: #92400e
        }

        .meta-chip {
            background: rgba(255, 255, 255, .12);
            border-radius: 20px;
            padding: 3px 12px;
            font-size: .8rem
        }

        .show-title {
            font-size: 1.6rem;
            font-weight: 800;
            margin: 0 0 14px;
            line-height: 1.3
        }

        .show-recipients {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap
        }

        .recipients-label {
            font-size: .78rem;
            color: rgba(255, 255, 255, .6);
            margin-right: 4px
        }

        .mini-pill {
            background: rgba(99, 102, 241, .4);
            border-radius: 20px;
            padding: 3px 10px;
            font-size: .75rem;
            color: #c7d2fe
        }

        .mini-pill--mw {
            background: rgba(16, 185, 129, .3);
            color: #a7f3d0
        }

        .mini-pill--more {
            background: rgba(255, 255, 255, .12);
            color: rgba(255, 255, 255, .6)
        }

        .show-hero-actions {
            display: flex;
            flex-direction: column;
            gap: 8px;
            flex-shrink: 0
        }

        .btn-edit {
            background: rgba(255, 255, 255, .15);
            border: 1.5px solid rgba(255, 255, 255, .3);
            color: #fff;
            border-radius: 10px;
            padding: 9px 18px;
            font-size: .85rem;
            font-weight: 600;
            text-decoration: none;
            text-align: center;
            transition: background .15s
        }

        .btn-edit:hover {
            background: rgba(255, 255, 255, .25)
        }

        .btn-back {
            background: transparent;
            border: 1.5px solid rgba(255, 255, 255, .2);
            color: rgba(255, 255, 255, .7);
            border-radius: 10px;
            padding: 9px 18px;
            font-size: .85rem;
            font-weight: 600;
            text-decoration: none;
            text-align: center
        }

        .btn-back:hover {
            border-color: rgba(255, 255, 255, .4);
            color: #fff
        }

        .alert-ok {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #16a34a;
            border-radius: 12px;
            padding: 12px 20px;
            font-size: .88rem;
            font-weight: 600
        }

        /* Days */
        .days-list {
            display: flex;
            flex-direction: column;
            gap: 12px
        }

        .day-block {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .07), 0 4px 16px rgba(0, 0, 0, .04);
            overflow: hidden
        }

        .day-block-head {
            width: 100%;
            background: none;
            border: none;
            padding: 18px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            text-align: left
        }

        .day-block-head:hover {
            background: #fafafa
        }

        .dbh-left {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap
        }

        .dbh-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #6366f1;
            flex-shrink: 0
        }

        .dbh-num {
            font-weight: 700;
            font-size: .95rem;
            color: #0f172a
        }

        .dbh-date {
            font-size: .8rem;
            color: #94a3b8
        }

        .dbh-city {
            background: #ede9fe;
            color: #7c3aed;
            font-size: .75rem;
            font-weight: 700;
            padding: 2px 10px;
            border-radius: 20px
        }

        .dbh-right {
            display: flex;
            align-items: center;
            gap: 10px
        }

        .dbh-count {
            font-size: .8rem;
            color: #94a3b8;
            font-weight: 600
        }

        .dbh-chevron {
            color: #94a3b8;
            transition: transform .2s;
            flex-shrink: 0
        }

        .dbh-chevron.rotated {
            transform: rotate(180deg)
        }

        .day-block-body {
            padding: 0 24px 20px
        }

        /* Items Timeline */
        .items-timeline {
            display: flex;
            flex-direction: column;
            gap: 0
        }

        .it-row {
            display: flex;
            gap: 16px;
            align-items: flex-start
        }

        .it-time-col {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 110px;
            /* 👈 Ditambah dari 90px biar ada space jarak ke kanan */
            flex-shrink: 0;
        }

        .it-time {
            background: linear-gradient(135deg, #4f46e5, #6366f1);
            color: #fff;
            font-size: .75rem;
            font-weight: 700;
            padding: 6px 14px;
            /* 👈 Padding kanan-kiri digedein dikit */
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: max-content;
            min-width: 90px;
            /* 👈 Ditambah dari 78px biar gak nge-press text */
            box-shadow: 0 4px 10px rgba(99, 102, 241, .25);
        }

        .it-time-icon {
            font-size: .72rem;
            opacity: .9;
        }

        .it-line {
            width: 2px;
            flex: 1;
            background: linear-gradient(to bottom, #6366f1, #c7d2fe);
            min-height: 24px;
            margin: 6px 0;
            border-radius: 999px;
        }

        .it-row:last-child .it-line {
            display: none
        }

        .it-body {
            flex: 1;
            padding: 4px 0 20px
        }

        .it-title {
            font-size: .9rem;
            font-weight: 700;
            color: #0f172a
        }

        .it-content {
            font-size: .82rem;
            color: #64748b;
            margin-top: 4px;
            line-height: 1.5
        }

        .it-seq {
            font-size: .72rem;
            color: #94a3b8;
            padding-top: 6px;
            flex-shrink: 0
        }

        .empty-msg {
            font-size: .85rem;
            color: #94a3b8;
            font-style: italic
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #94a3b8
        }

        @media(max-width:700px) {
            .show-hero {
                flex-direction: column
            }

            .show-hero-actions {
                flex-direction: row
            }
        }
    </style>
@endpush
