@extends('layouts.app')

@section('content')
    <div class="confirm-wrap">

        {{-- HEADER --}}
        <div class="confirm-header">
            <div>
                <h2 class="confirm-title">Konfirmasi Itinerary</h2>
                <p class="confirm-sub">Periksa semua detail sebelum mengirim ke penerima.</p>
            </div>
            <span class="step-badge">Langkah Terakhir</span>
        </div>

        {{-- SUMMARY HERO --}}
        <div class="hero-card">
            <div class="hero-main">
                <div class="hero-title">{{ $itinerary->title }}</div>
                <div class="hero-meta">
                    @if ($itinerary->start_date)
                        <span class="hero-chip">
                            📅 {{ $itinerary->start_date->format('d M Y') }}
                            @if ($itinerary->end_date)
                                — {{ $itinerary->end_date->format('d M Y') }}
                            @endif
                        </span>
                    @endif
                    <span class="hero-chip">🗓 {{ $itinerary->days->count() }} Hari</span>
                    <span class="hero-chip">📤 {{ $itinerary->recipient_label }}</span>
                </div>
            </div>
            <div class="hero-actions">
                <a href="{{ route('admin.itinerary.edit', $itinerary) }}" class="btn-outline-sm">
                    ✏️ Edit Itinerary
                </a>
            </div>
        </div>

        {{-- PENERIMA --}}
        @if ($itinerary->tourLeaders->count() || $itinerary->muthawifs->count())
            <div class="section-card">
                <div class="section-head">
                    <span class="section-icon">👥</span>
                    <div>
                        <div class="section-title">Penerima Itinerary</div>
                        <div class="section-sub">{{ $itinerary->tourLeaders->count() }} Tourleader ·
                            {{ $itinerary->muthawifs->count() }} Muthawif</div>
                    </div>
                </div>
                <div class="pill-group">
                    @foreach ($itinerary->tourLeaders as $tl)
                        <div class="recipient-pill pill--tl">
                            <span class="pill-name">{{ $tl->name }}</span>
                            @if ($tl->kloter)
                                <span class="pill-kloter">{{ $tl->kloter->nama }}
                                    ({{ $tl->kloter->tanggal_label ?? '' }})
                                </span>
                            @endif
                        </div>
                    @endforeach
                    @foreach ($itinerary->muthawifs as $mw)
                        <div class="recipient-pill pill--mw">
                            <span class="pill-name">{{ $mw->nama }}</span>
                            @if ($mw->kloter)
                                <span class="pill-kloter">{{ $mw->kloter->nama }}
                                    ({{ $mw->kloter->tanggal_label ?? '' }})
                                </span>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- TIMELINE HARI --}}
        <div class="section-card">
            <div class="section-head">
                <span class="section-icon">📋</span>
                <div>
                    <div class="section-title">Rangkuman Hari & Kegiatan</div>
                    <div class="section-sub">Pastikan semua detail sudah benar</div>
                </div>
            </div>

            <div class="timeline">
                @forelse($itinerary->days as $day)
                    <div class="tl-day">
                        <div class="tl-day-dot"></div>
                        <div class="tl-day-content">
                            <div class="tl-day-head">
                                <div class="tl-day-info">
                                    <span class="tl-day-num">Hari {{ $day->day_number }}</span>
                                    @if ($day->date)
                                        <span class="tl-day-date">{{ $day->date->format('d M Y') }}</span>
                                    @endif
                                    @if ($day->city)
                                        <span class="tl-city-chip">{{ $day->city }}</span>
                                    @endif
                                </div>
                                <span class="tl-count">{{ $day->items->count() }} kegiatan</span>
                            </div>

                            @if ($day->items->count())
                                <div class="tl-items">
                                    @foreach ($day->items as $item)
                                        <div class="tl-item">
                                            <div class="tl-time">
                                                {{ $item->start_time ? \Carbon\Carbon::parse($item->start_time)->format('H:i') : '--:--' }}

                                                -

                                                {{ $item->end_time ? \Carbon\Carbon::parse($item->end_time)->format('H:i') : '--:--' }}
                                            </div>
                                            <div class="tl-item-body">
                                                <div class="tl-item-title">{{ $item->title ?: 'Belum diisi' }}</div>
                                                @if ($item->content)
                                                    <div class="tl-item-desc">
                                                        {{ \Illuminate\Support\Str::limit($item->content, 100) }}
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="tl-empty">Belum ada kegiatan untuk hari ini.</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="tl-empty">Belum ada hari yang dibuat.</p>
                @endforelse
            </div>
        </div>

        {{-- AKSI FINAL --}}
        <div class="confirm-footer">
            <div class="footer-note">
                Setelah dikonfirmasi, itinerary akan dikirim ke penerima yang dipilih.
            </div>
            <div class="footer-actions">
                <a href="{{ route('admin.itinerary.edit', $itinerary) }}" class="btn-ghost-lg">
                    ← Kembali &amp; Edit
                </a>
                <form action="{{ route('admin.itinerary.finalize', $itinerary) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn-finalize">
                        ✅ Kirim Itinerary Sekarang
                    </button>
                </form>
            </div>
        </div>

    </div>
@endsection

@push('styles')
    <style>
        .confirm-wrap {
            max-width: 900px;
            margin: 0 auto;
            padding: 24px 20px;
            display: flex;
            flex-direction: column;
            gap: 20px
        }

        .confirm-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start
        }

        .confirm-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0
        }

        .confirm-sub {
            color: #64748b;
            font-size: .88rem;
            margin: 4px 0 0
        }

        .step-badge {
            background: #ede9fe;
            color: #7c3aed;
            font-size: .75rem;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 20px;
            white-space: nowrap
        }

        /* Hero */
        .hero-card {
            background: linear-gradient(135deg, #1e1b4b, #4338ca);
            border-radius: 20px;
            padding: 28px;
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px
        }

        .hero-title {
            font-size: 1.4rem;
            font-weight: 700;
            margin-bottom: 12px
        }

        .hero-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 8px
        }

        .hero-chip {
            background: rgba(255, 255, 255, .15);
            border-radius: 20px;
            padding: 4px 12px;
            font-size: .82rem;
            font-weight: 500
        }

        .btn-outline-sm {
            background: rgba(255, 255, 255, .15);
            border: 1.5px solid rgba(255, 255, 255, .3);
            color: #fff;
            border-radius: 10px;
            padding: 8px 16px;
            font-size: .83rem;
            font-weight: 600;
            text-decoration: none;
            white-space: nowrap;
            backdrop-filter: blur(4px)
        }

        .btn-outline-sm:hover {
            background: rgba(255, 255, 255, .25)
        }

        /* Section cards */
        .section-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .07), 0 4px 16px rgba(0, 0, 0, .04);
            padding: 24px
        }

        .section-head {
            display: flex;
            align-items: flex-start;
            gap: 14px;
            margin-bottom: 20px
        }

        .section-icon {
            font-size: 1.4rem
        }

        .section-title {
            font-size: 1rem;
            font-weight: 700;
            color: #0f172a
        }

        .section-sub {
            font-size: .8rem;
            color: #94a3b8;
            margin-top: 2px
        }

        /* Pills */
        .pill-group {
            display: flex;
            flex-wrap: wrap;
            gap: 8px
        }

        .pill {
            font-size: .8rem;
            font-weight: 600;
            padding: 4px 12px;
            border-radius: 20px
        }

        .pill--tl {
            background: #ede9fe;
            color: #7c3aed
        }

        .pill--mw {
            background: #d1fae5;
            color: #059669
        }

        /* Timeline */
        .timeline {
            display: flex;
            flex-direction: column;
            gap: 0;
            padding-left: 16px;
            border-left: 2px solid #e2e8f0
        }

        .tl-day {
            position: relative;
            padding: 0 0 24px 24px
        }

        .tl-day:last-child {
            padding-bottom: 0
        }

        .tl-day-dot {
            position: absolute;
            left: -9px;
            top: 4px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #6366f1;
            border: 2px solid #fff;
            box-shadow: 0 0 0 2px #e0e7ff
        }

        .tl-day-content {
            background: #f8fafc;
            border-radius: 14px;
            padding: 16px
        }

        .tl-day-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 14px
        }

        .tl-day-info {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap
        }

        .tl-day-num {
            font-weight: 700;
            color: #1e1b4b;
            font-size: .95rem
        }

        .tl-day-date {
            font-size: .8rem;
            color: #94a3b8
        }

        .tl-city-chip {
            background: #e0e7ff;
            color: #4338ca;
            font-size: .75rem;
            font-weight: 700;
            padding: 2px 10px;
            border-radius: 20px
        }

        .tl-count {
            font-size: .78rem;
            color: #94a3b8;
            font-weight: 600;
            white-space: nowrap
        }

        .tl-items {
            display: flex;
            flex-direction: column;
            gap: 8px
        }

        .tl-item {
            display: flex;
            gap: 12px;
            align-items: flex-start
        }

        .tl-time {
            min-width: 110px;
            text-align: center;
            background: #6366f1;
            color: #fff;
            font-size: .75rem;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 20px;
            white-space: nowrap;
            flex-shrink: 0;
            margin-top: 1px
        }

        .tl-item-body {
            flex: 1
        }

        .tl-item-title {
            font-size: .88rem;
            font-weight: 600;
            color: #0f172a
        }

        .tl-item-desc {
            font-size: .8rem;
            color: #64748b;
            margin-top: 2px
        }

        .tl-empty {
            font-size: .85rem;
            color: #94a3b8;
            font-style: italic;
            margin: 0
        }

        /* Footer */
        .confirm-footer {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .07), 0 4px 16px rgba(0, 0, 0, .04);
            padding: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap
        }

        .footer-note {
            font-size: .85rem;
            color: #64748b;
            max-width: 400px
        }

        .footer-actions {
            display: flex;
            gap: 12px;
            align-items: center
        }

        .btn-ghost-lg {
            background: transparent;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            padding: 11px 22px;
            font-size: .88rem;
            font-weight: 600;
            color: #374151;
            text-decoration: none;
            cursor: pointer
        }

        .btn-ghost-lg:hover {
            border-color: #6366f1;
            color: #6366f1
        }

        .btn-finalize {
            background: linear-gradient(135deg, #16a34a, #15803d);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 12px 28px;
            font-size: .9rem;
            font-weight: 700;
            cursor: pointer;
            box-shadow: 0 4px 14px rgba(22, 163, 74, .3)
        }

        .btn-finalize:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(22, 163, 74, .35)
        }

        @media(max-width:768px) {
            .hero-card {
                flex-direction: column
            }

            .confirm-footer {
                flex-direction: column;
                align-items: stretch;
                text-align: center
            }

            .footer-actions {
                flex-direction: column
            }

            .btn-ghost-lg,
            .btn-finalize {
                width: 100%;
                text-align: center
            }
        }

        .recipient-pill {
            display: flex;
            flex-direction: column;
            padding: 8px 14px;
            border-radius: 20px;
        }

        .pill-name {
            font-weight: 700;
            font-size: .82rem;
        }

        .pill-kloter {
            font-size: .72rem;
            opacity: .8;
            margin-top: 2px;
        }

        .pill--tl {
            background: #ede9fe;
            color: #7c3aed;
        }

        .pill--mw {
            background: #d1fae5;
            color: #059669;
        }
    </style>
@endpush
