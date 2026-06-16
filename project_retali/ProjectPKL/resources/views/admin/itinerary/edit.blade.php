@extends('layouts.app')

@section('content')
    <div class="edit-wrap" x-data="editApp()" x-init="init()">

        {{-- HEADER --}}
        <div class="edit-header">
            <div>
                <h2 class="edit-title">Edit Itinerary</h2>
                <p class="edit-sub">Ubah informasi, penerima, dan kegiatan harian</p>
            </div>
            <a href="{{ route('admin.itinerary.show', $itinerary) }}" class="btn-ghost">← Kembali</a>
        </div>

        @if (session('ok'))
            <div class="alert-ok" x-data="{ show: true }" x-show="show" x-transition>
                ✓ {{ session('ok') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert-err">
                <ul>
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="edit-layout">

            {{-- KOLOM KIRI --}}
            <div class="edit-main">

                {{-- CARD: Info Utama --}}
                <div class="edit-card">
                    <div class="edit-card-head">
                        <span class="card-num">01</span>
                        <div style="flex:1">
                            <h3>Informasi Itinerary</h3>
                            <p>Judul dan rentang tanggal</p>
                        </div>
                        <div x-show="showNewDayAlert" x-transition.opacity class="new-day-alert">
                            ⚠️ SILAKAN ISI HARI DAN KEGIATAN TERBARU DI BAWAH YA ⬇️⬇️.
                        </div>
                    </div>

                    <form id="itineraryForm" action="{{ route('admin.itinerary.update', $itinerary) }}" method="POST"
                        class="edit-card-body">
                        @csrf
                        @method('PUT')

                        <div class="field-group">
                            <label class="field-label">Judul <span class="req">*</span></label>
                            <input type="text" name="title" class="field-input"
                                value="{{ old('title', $itinerary->title) }}" required maxlength="150"
                                placeholder="Contoh: Umrah Plus Istanbul 9 Hari">
                        </div>

                        <div class="date-row">
                            <div class="field-group" style="flex:1">
                                <label class="field-label">Tanggal Mulai</label>
                                <input type="date" name="start_date" class="field-input"
                                    value="{{ old('start_date', optional($itinerary->start_date)->toDateString()) }}">
                                <div x-show="startDateWarning" x-cloak class="field-warning" x-text="startDateWarning">
                                </div>
                            </div>
                            <div class="date-dash">—</div>
                            <div class="field-group" style="flex:1">
                                <label class="field-label">Tanggal Selesai</label>
                                <input type="date" name="end_date" class="field-input"
                                    value="{{ old('end_date', optional($itinerary->end_date)->toDateString()) }}">
                            </div>
                        </div>

                        {{-- Penerima --}}
                        <div class="field-group">
                            <label class="field-label">Kirim Ke</label>
                            <div class="send-opts">
                                @foreach ([
            'all_tourleaders' => ['Semua Tourleader', '👥'],
            'all_muthawif' => ['Semua Muthawif', '🕌'],
            'all_users' => ['Semua Pengguna', '🌐'],
            'selected' => ['Pengguna Tertentu', '🎯'],
        ] as $val => $opt)
                                    <label class="send-opt" :class="{ 'send-opt--on': sendTo === '{{ $val }}' }">
                                        <input type="radio" name="send_to" value="{{ $val }}"
                                            @change="sendTo = '{{ $val }}'"
                                            {{ old('send_to', $itinerary->send_to) === $val ? 'checked' : '' }}
                                            style="display:none">
                                        <span class="send-opt-icon">{{ $opt[1] }}</span>
                                        <span class="send-opt-label">{{ $opt[0] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Selected Recipients --}}
                        <div x-show="sendTo === 'selected'" x-cloak x-transition.opacity style="margin-top:4px">
                            <div class="recip-box">
                                <div class="recip-head">Tourleader</div>
                                <div class="recip-list">
                                    @foreach ($tourLeaders as $tl)
                                        <label class="recip-item">
                                            <input type="checkbox" name="selected_users[]" value="tl:{{ $tl->id }}"
                                                {{ in_array($tl->id, old('tourleaders', $itinerary->tourLeaders->pluck('id')->toArray())) ? 'checked' : '' }}>
                                            <span class="recip-av">{{ strtoupper(substr($tl->name, 0, 1)) }}</span>
                                            <div>
                                                <div style="font-weight:600;font-size:.85rem">{{ $tl->name }}</div>
                                                @if ($tl->kloter)
                                                    <div style="font-size:.75rem;color:#94a3b8">{{ $tl->kloter->nama }} •
                                                        {{ $tl->kloter->tanggal_label }}</div>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div class="recip-box" style="margin-top:8px">
                                <div class="recip-head">Muthawif</div>
                                <div class="recip-list">
                                    @foreach ($muthawifs as $mw)
                                        <label class="recip-item">
                                            <input type="checkbox" value="mw:{{ $mw->id }}" name="selected_users[]"
                                                {{ in_array($mw->id, $itinerary->muthawifs->pluck('id')->toArray()) ? 'checked' : '' }}>
                                            <span class="recip-av"
                                                style="background:#7c3aed">{{ strtoupper(substr($mw->nama, 0, 1)) }}</span>
                                            <div>
                                                <div style="font-weight:600;font-size:.85rem">{{ $mw->nama }}</div>
                                                @if ($mw->kloter)
                                                    <div style="font-size:.75rem;color:#94a3b8">{{ $mw->kloter->nama }} •
                                                        {{ $mw->kloter->tanggal_label }}</div>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- CARD: Days & Activities --}}
                <div class="edit-card">
                    <div class="edit-card-head">
                        <span class="card-num">02</span>
                        <div>
                            <h3>Hari & Kegiatan</h3>
                            <p>Edit kota, tanggal, dan kegiatan tiap hari</p>
                        </div>
                    </div>

                    @foreach ($itinerary->days as $day)
                        <div class="day-row" data-day-id="{{ $day->id }}" x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }">

                            <div class="day-row-head" @click="open = !open">
                                <div style="display:flex;align-items:center;gap:10px">
                                    <span class="day-pill">Hari {{ $day->day_number }}</span>
                                    <span class="day-row-city">{{ $day->city ?: 'Kota belum diisi' }}</span>
                                    @if ($day->date)
                                        <span class="day-row-date">{{ $day->date->format('d M Y') }}</span>
                                    @endif
                                </div>
                                <div style="display:flex;align-items:center;gap:8px">
                                    <button type="button" class="btn-del-day"
                                        @click.stop="hapusHari({{ $day->id }}, {{ $day->day_number }}, this.$el)">
                                        🗑 Hapus
                                    </button>
                                    <svg class="day-chev" :class="{ 'rotated': open }" width="16" height="16"
                                        viewBox="0 0 16 16" fill="none">
                                        <path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </div>
                            </div>

                            <div x-show="open" x-collapse class="day-row-body">
                                <form action="{{ route('admin.day.update', $day) }}" method="POST" class="day-form"
                                    data-day-number="{{ $day->day_number }}">
                                    @csrf @method('PUT')
                                    <div class="field-group" style="flex:1">
                                        <label class="field-label">Kota</label>
                                        <select name="city" class="field-input">
                                            <option value="">-- Pilih Kota --</option>
                                            @foreach ($cities as $city)
                                                <option value="{{ $city->name }}"
                                                    {{ $day->city === $city->name ? 'selected' : '' }}>
                                                    {{ $city->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="field-group" style="width:150px">
                                        <label class="field-label">Tanggal</label>
                                        <input type="date" name="date" class="field-input field-input--readonly"
                                            value="{{ $day->date ? $day->date->format('Y-m-d') : '' }}" readonly>
                                    </div>
                                </form>

                                <div class="items-section">
                                    <div class="items-section-head">
                                        <span class="field-label">Kegiatan
                                            <span class="items-count">{{ $day->items->count() }}</span>
                                        </span>
                                        <button type="button" @click="openAddModal({{ $day->id }})"
                                            class="btn-add-activity">
                                            + Tambah Kegiatan
                                        </button>
                                    </div>

                                    @forelse($day->items as $item)
                                        <div class="item-row">
                                            <div class="item-time-chip">
                                                @if ($item->start_time && $item->end_time)
                                                    {{ \Carbon\Carbon::parse($item->start_time)->format('H:i') }} –
                                                    {{ \Carbon\Carbon::parse($item->end_time)->format('H:i') }}
                                                @elseif($item->start_time)
                                                    {{ \Carbon\Carbon::parse($item->start_time)->format('H:i') }}
                                                @else
                                                    --:--
                                                @endif
                                            </div>
                                            <div class="item-info">
                                                <div class="item-title">{{ $item->title ?: '—' }}</div>
                                                @if ($item->content)
                                                    <div class="item-desc">
                                                        {{ \Illuminate\Support\Str::limit($item->content, 80) }}</div>
                                                @endif
                                            </div>
                                            <div class="item-actions">
                                                <button type="button"
                                                    @click="openEditModal({{ $item->id }}, '{{ addslashes($item->title) }}', '{{ $item->start_time ? substr($item->start_time, 0, 5) : '' }}', '{{ $item->end_time ? substr($item->end_time, 0, 5) : '' }}', '{{ addslashes($item->content) }}')"
                                                    class="btn-icon btn-icon--edit" title="Edit">✏️</button>
                                                <form action="{{ route('admin.day.item.destroy', $item) }}"
                                                    method="POST" onsubmit="return confirm('Hapus kegiatan ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn-icon btn-icon--del"
                                                        title="Hapus">🗑</button>
                                                </form>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="no-items">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="1.5">
                                                <rect x="3" y="4" width="18" height="18" rx="2" />
                                                <path d="M16 2v4M8 2v4M3 10h18" />
                                            </svg>
                                            Belum ada kegiatan
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div id="newDaysContainer"></div>
            </div>

            {{-- KOLOM KANAN --}}
            <div class="edit-sidebar">
                <div class="summary-card">
                    <div class="summary-title">Status Hari</div>

                    @foreach ($itinerary->days as $day)
                        <div class="status-row" data-day-number="{{ $day->day_number }}">
                            <div class="status-dot status-dot--{{ $day->status }}"
                                data-day-number="{{ $day->day_number }}"></div>
                            <span class="status-name" data-day-number="{{ $day->day_number }}">
                                Hari {{ $day->day_number }}
                                @if ($day->city)
                                    <span style="color:#94a3b8">— {{ $day->city }}</span>
                                @endif
                            </span>
                            <span class="status-badge status-badge--{{ $day->status }}"
                                data-day-number="{{ $day->day_number }}">
                                {{ ['empty' => 'Kosong', 'incomplete' => 'Kurang', 'complete' => 'Lengkap'][$day->status] ?? '?' }}
                            </span>
                        </div>
                    @endforeach

                    <template x-for="newDay in newDayStatuses" :key="newDay.dayNumber">
                        <div class="status-row">
                            <div class="status-dot status-dot--empty"></div>
                            <span class="status-name"
                                x-text="'Hari ' + newDay.dayNumber + (newDay.city ? ' — ' + newDay.city : '')"></span>
                            <span class="status-badge status-badge--new">Baru</span>
                        </div>
                    </template>

                    <div style="margin-top:20px">
                        <button type="submit" form="itineraryForm" id="saveBtn" class="save-btn" disabled>
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </div>

        </div>

        {{-- MODAL: TAMBAH KEGIATAN --}}
        <div class="modal-overlay" x-show="addModal" x-cloak
            @click.self="addModal = false; window.checkChanges && window.checkChanges()"
            x-transition:enter="overlay-enter" x-transition:enter-start="overlay-enter-start"
            x-transition:leave="overlay-leave" x-transition:leave-end="overlay-leave-end">
            <div class="modal-box" x-show="addModal" x-transition:enter="modal-enter"
                x-transition:enter-start="modal-enter-start" x-transition:leave="modal-leave"
                x-transition:leave-end="modal-leave-end">
                <div class="modal-head">
                    <h4 class="modal-title">Tambah Kegiatan</h4>
                    <button @click="addModal = false; window.checkChanges && window.checkChanges()"
                        class="modal-close">✕</button>
                </div>
                <form :action="`/admin/day/${activeDayId}/item`" method="POST"
                    onsubmit="return validateTambahWaktu(this)">
                    @csrf
                    <div class="modal-body">
                        <div class="field-group">
                            <label class="field-label">Judul Kegiatan <span class="req">*</span></label>
                            <input type="text" name="title" class="field-input"
                                placeholder="Cth: Sholat Subuh di Masjidil Haram" required>
                        </div>
                        <div style="display:flex;gap:12px">
                            <div class="field-group" style="flex:1">
                                <label class="field-label">Jam Mulai</label>
                                <input type="time" name="start_time" class="field-input">
                            </div>
                            <div class="field-group" style="flex:1">
                                <label class="field-label">Jam Selesai</label>
                                <input type="time" name="end_time" class="field-input">
                            </div>
                        </div>
                        <div class="field-group">
                            <label class="field-label">Deskripsi</label>
                            <textarea name="content" class="field-input field-textarea" rows="3" placeholder="Detail kegiatan (opsional)"></textarea>
                        </div>
                    </div>
                    <div class="modal-foot">
                        <button type="button" @click="addModal = false; checkChanges()"
                            class="btn-ghost-sm">Batal</button>
                        <button type="submit" class="btn-primary-sm">Tambah</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL: EDIT KEGIATAN --}}
        <div class="modal-overlay" x-show="editModal" x-cloak @click.self="editModal = false"
            x-transition:enter="overlay-enter" x-transition:enter-start="overlay-enter-start"
            x-transition:leave="overlay-leave" x-transition:leave-end="overlay-leave-end">
            <div class="modal-box" x-show="editModal" x-transition:enter="modal-enter"
                x-transition:enter-start="modal-enter-start" x-transition:leave="modal-leave"
                x-transition:leave-end="modal-leave-end">
                <div class="modal-head">
                    <h4 class="modal-title">Edit Kegiatan</h4>
                    <button @click="editModal = false" class="modal-close">✕</button>
                </div>
                <form :action="`/admin/day/item/${activeItemId}`" method="POST">
                    @csrf @method('PUT')
                    <div class="modal-body">
                        <div class="field-group">
                            <label class="field-label">Judul Kegiatan <span class="req">*</span></label>
                            <input type="text" name="title" class="field-input"
                                placeholder="Cth: Sholat Subuh di Masjidil Haram" required
                                x-effect="$el.value = editTitle" @input="editTitle = $el.value">
                        </div>
                        <div style="display:flex;gap:12px">
                            <div class="field-group" style="flex:1">
                                <label class="field-label">Jam Mulai</label>
                                <input type="time" name="start_time" class="field-input"
                                    x-effect="$el.value = editStartTime" @change="editStartTime = $el.value">
                            </div>
                            <div class="field-group" style="flex:1">
                                <label class="field-label">Jam Selesai</label>
                                <input type="time" name="end_time" class="field-input"
                                    x-effect="$el.value = editEndTime" @change="editEndTime = $el.value">
                            </div>
                        </div>
                        <div class="field-group">
                            <label class="field-label">Deskripsi</label>
                            <textarea name="content" class="field-input field-textarea" rows="3" placeholder="Detail kegiatan (opsional)"
                                x-effect="$el.value = editContent" @input="editContent = $el.value"></textarea>
                        </div>
                    </div>
                    <div class="modal-foot">
                        <button type="button" @click="editModal = false" class="btn-ghost-sm">Batal</button>
                        <button type="submit" class="btn-primary-sm">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection

@push('styles')
    <style>
        [x-cloak] {
            display: none !important
        }

        /* ── Base ── */
        .edit-wrap {
            max-width: 1100px;
            margin: 0 auto;
            padding: 28px 20px;
            display: flex;
            flex-direction: column;
            gap: 22px;
        }

        /* ── Header ── */
        .edit-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .edit-title {
            font-size: 1.45rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
            letter-spacing: -.02em;
        }

        .edit-sub {
            font-size: .85rem;
            color: #64748b;
            margin: 3px 0 0;
        }

        /* ── Layout ── */
        .edit-layout {
            display: grid;
            grid-template-columns: 1fr 268px;
            gap: 20px;
            align-items: start;
        }

        .edit-main {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        /* ── Cards ── */
        .edit-card {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e8ecf0;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .05);
            overflow: hidden;
            transition: box-shadow .2s;
        }

        .edit-card:hover {
            box-shadow: 0 4px 16px rgba(0, 0, 0, .08);
        }

        .edit-card-head {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 22px;
            border-bottom: 1px solid #f1f5f9;
            background: #fafbfc;
        }

        .edit-card-head h3 {
            font-size: .95rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }

        .edit-card-head p {
            font-size: .78rem;
            color: #94a3b8;
            margin: 2px 0 0;
        }

        .edit-card-body {
            padding: 20px 22px;
        }

        .card-num {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 8px;
            background: #ede9fe;
            color: #6d28d9;
            font-size: .72rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        /* ── Fields ── */
        .field-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
            margin-top: 14px;
        }

        .field-group:first-child {
            margin-top: 0;
        }

        .field-label {
            font-size: .78rem;
            font-weight: 600;
            color: #475569;
            letter-spacing: .01em;
        }

        .req {
            color: #ef4444;
        }

        .field-input {
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 12px;
            font-size: .88rem;
            color: #0f172a;
            background: #fff;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
            width: 100%;
            box-sizing: border-box;
        }

        .field-input:focus {
            border-color: #7c3aed;
            box-shadow: 0 0 0 3px rgba(124, 58, 237, .1);
        }

        .field-input--readonly {
            opacity: .55;
            cursor: not-allowed;
            background: #f8fafc;
        }

        .field-textarea {
            resize: vertical;
            min-height: 72px;
            line-height: 1.5;
        }

        .field-warning {
            font-size: .76rem;
            font-weight: 600;
            color: #dc2626;
            padding: 5px 10px;
            background: #fef2f2;
            border-radius: 6px;
            border: 1px solid #fecaca;
            margin-top: 2px;
        }

        /* ── Date row ── */
        .date-row {
            display: flex;
            align-items: flex-end;
            gap: 10px;
            margin-top: 14px;
        }

        .date-dash {
            color: #cbd5e1;
            padding-bottom: 9px;
            flex-shrink: 0;
            font-size: 1.1rem;
        }

        /* ── Send options ── */
        .send-opts {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 7px;
            margin-top: 5px;
        }

        .send-opt {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            padding: 10px 6px;
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            cursor: pointer;
            transition: border-color .15s, background .15s, transform .15s;
            text-align: center;
            user-select: none;
        }

        .send-opt:hover {
            border-color: #a78bfa;
            transform: translateY(-1px);
        }

        .send-opt--on {
            border-color: #7c3aed;
            background: #f5f3ff;
        }

        .send-opt-icon {
            font-size: 1.2rem;
        }

        .send-opt-label {
            font-size: .72rem;
            font-weight: 600;
            color: #475569;
        }

        .send-opt--on .send-opt-label {
            color: #6d28d9;
        }

        /* ── Recipients ── */
        .recip-box {
            border: 1.5px solid #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            margin-top: 8px;
        }

        .recip-head {
            padding: 8px 14px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
            font-size: .75rem;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .recip-list {
            max-height: 180px;
            overflow-y: auto;
            padding: 6px;
        }

        .recip-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: 8px;
            cursor: pointer;
            transition: background .12s;
        }

        .recip-item:hover {
            background: #f5f3ff;
        }

        .recip-item input {
            accent-color: #7c3aed;
            width: 15px;
            height: 15px;
            flex-shrink: 0;
        }

        .recip-av {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #6d28d9;
            color: #fff;
            font-size: .7rem;
            font-weight: 700;
            flex-shrink: 0;
        }

        /* ── Day rows ── */
        .day-row {
            border-bottom: 1px solid #f1f5f9;
        }

        .day-row:last-child {
            border-bottom: none;
        }

        .day-row-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 22px;
            cursor: pointer;
            transition: background .12s;
        }

        .day-row-head:hover {
            background: #fafbfc;
        }

        .day-pill {
            background: #ede9fe;
            color: #5b21b6;
            font-size: .75rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px;
            flex-shrink: 0;
        }

        .day-row-city {
            font-size: .82rem;
            color: #64748b;
        }

        .day-row-date {
            font-size: .78rem;
            color: #94a3b8;
        }

        .day-chev {
            color: #94a3b8;
            transition: transform .22s ease;
            flex-shrink: 0;
        }

        .day-chev.rotated {
            transform: rotate(180deg);
        }

        .day-row-body {
            padding: 0 22px 20px;
        }

        .day-form {
            display: flex;
            align-items: flex-end;
            gap: 12px;
            margin-bottom: 14px;
            padding-top: 4px;
        }

        /* ── Items section ── */
        .items-section {
            background: #f8fafc;
            border-radius: 10px;
            padding: 14px;
            border: 1px solid #f1f5f9;
        }

        .items-section-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .items-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            background: #e0e7ff;
            color: #4338ca;
            border-radius: 50%;
            font-size: .7rem;
            font-weight: 700;
            margin-left: 4px;
        }

        .btn-add-activity {
            background: #ede9fe;
            border: none;
            border-radius: 7px;
            padding: 5px 12px;
            font-size: .78rem;
            font-weight: 700;
            color: #6d28d9;
            cursor: pointer;
            transition: background .12s, transform .12s;
        }

        .btn-add-activity:hover {
            background: #ddd6fe;
            transform: translateY(-1px);
        }

        .item-row {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 10px;
            background: #fff;
            border-radius: 8px;
            margin-bottom: 6px;
            border: 1px solid #e8ecf0;
            transition: box-shadow .15s;
        }

        .item-row:hover {
            box-shadow: 0 2px 8px rgba(0, 0, 0, .06);
        }

        .item-time-chip {
            background: #6d28d9;
            color: #fff;
            font-size: .7rem;
            font-weight: 700;
            padding: 3px 9px;
            border-radius: 20px;
            flex-shrink: 0;
            white-space: nowrap;
        }

        .item-info {
            flex: 1;
            min-width: 0;
        }

        .item-title {
            font-size: .84rem;
            font-weight: 600;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .item-desc {
            font-size: .76rem;
            color: #94a3b8;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            margin-top: 1px;
        }

        .item-actions {
            display: flex;
            gap: 3px;
        }

        .btn-icon {
            background: none;
            border: none;
            cursor: pointer;
            padding: 5px 6px;
            border-radius: 6px;
            font-size: .88rem;
            transition: background .12s;
        }

        .btn-icon:hover {
            background: #f1f5f9;
        }

        .no-items {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: .8rem;
            color: #94a3b8;
            font-style: italic;
            padding: 8px 4px;
        }

        /* ── Del day btn ── */
        .btn-del-day {
            background: transparent;
            border: 1px solid #fecaca;
            color: #ef4444;
            border-radius: 7px;
            padding: 4px 10px;
            font-size: .73rem;
            font-weight: 600;
            cursor: pointer;
            transition: all .15s;
            white-space: nowrap;
        }

        .btn-del-day:hover {
            background: #fef2f2;
            border-color: #f87171;
        }

        /* ── Sidebar ── */
        .edit-sidebar {
            position: sticky;
            top: 24px;
        }

        .summary-card {
            background: #fff;
            border-radius: 14px;
            border: 1px solid #e8ecf0;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .05);
            padding: 18px;
        }

        .summary-title {
            font-size: .72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .07em;
            color: #94a3b8;
            margin-bottom: 14px;
        }

        .status-row {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 5px 0;
            border-bottom: 1px solid #f8fafc;
        }

        .status-row:last-of-type {
            border-bottom: none;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }

        .status-dot--empty {
            background: #cbd5e1;
        }

        .status-dot--incomplete {
            background: #f87171;
        }

        .status-dot--complete {
            background: #22c55e;
        }

        .status-name {
            flex: 1;
            font-size: .79rem;
            color: #374151;
            font-weight: 500;
        }

        .status-badge {
            font-size: .68rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 20px;
            flex-shrink: 0;
        }

        .status-badge--empty {
            background: #f1f5f9;
            color: #94a3b8;
        }

        .status-badge--incomplete {
            background: #fef2f2;
            color: #ef4444;
        }

        .status-badge--complete {
            background: #f0fdf4;
            color: #16a34a;
        }

        .status-badge--new {
            background: #fef3c7;
            color: #d97706;
        }

        /* ── Save btn ── */
        .save-btn {
            width: 100%;
            padding: 11px;
            background: #6d28d9;
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: .88rem;
            font-weight: 700;
            cursor: pointer;
            transition: background .2s, transform .15s, box-shadow .2s;
            box-shadow: 0 4px 12px rgba(109, 40, 217, .25);
            margin-top: 16px;
        }

        .save-btn:hover:not(:disabled) {
            background: #5b21b6;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(109, 40, 217, .3);
        }

        .save-btn:active:not(:disabled) {
            transform: translateY(0);
        }

        .save-btn:disabled {
            opacity: .4;
            cursor: not-allowed;
            box-shadow: none;
            transform: none;
        }

        /* ── Alerts ── */
        .alert-ok {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #15803d;
            border-radius: 10px;
            padding: 12px 18px;
            font-size: .86rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .alert-err {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #dc2626;
            border-radius: 10px;
            padding: 12px 18px;
            font-size: .86rem;
        }

        .alert-err ul {
            margin: 0;
            padding-left: 18px;
        }

        /* ── Buttons ── */
        .btn-ghost {
            background: transparent;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: .84rem;
            font-weight: 600;
            color: #475569;
            text-decoration: none;
            transition: border-color .15s, color .15s;
        }

        .btn-ghost:hover {
            border-color: #7c3aed;
            color: #7c3aed;
        }

        .btn-ghost-sm {
            background: transparent;
            border: 1.5px solid #e2e8f0;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: .84rem;
            font-weight: 600;
            color: #475569;
            cursor: pointer;
            transition: border-color .15s;
        }

        .btn-ghost-sm:hover {
            border-color: #94a3b8;
        }

        .btn-primary-sm {
            background: #6d28d9;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 9px 20px;
            font-size: .84rem;
            font-weight: 700;
            cursor: pointer;
            transition: background .15s, transform .12s;
        }

        .btn-primary-sm:hover {
            background: #5b21b6;
            transform: translateY(-1px);
        }

        .btn-remove-activity {
            background: transparent;
            border: 1px solid #fecaca;
            border-radius: 7px;
            padding: 5px 12px;
            font-size: .77rem;
            font-weight: 600;
            color: #ef4444;
            cursor: pointer;
            transition: background .12s;
        }

        .btn-remove-activity:hover {
            background: #fef2f2;
        }

        /* ── Modal ── */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(15, 23, 42, .45);
            backdrop-filter: blur(3px);
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .overlay-enter {
            transition: opacity .2s ease;
        }

        .overlay-enter-start {
            opacity: 0;
        }

        .overlay-leave {
            transition: opacity .15s ease;
        }

        .overlay-leave-end {
            opacity: 0;
        }

        .modal-box {
            background: #fff;
            border-radius: 16px;
            width: 100%;
            max-width: 500px;
            box-shadow: 0 20px 50px rgba(0, 0, 0, .18);
        }

        .modal-enter {
            transition: all .22s cubic-bezier(.34, 1.56, .64, 1);
        }

        .modal-enter-start {
            opacity: 0;
            transform: scale(.94) translateY(8px);
        }

        .modal-leave {
            transition: all .15s ease;
        }

        .modal-leave-end {
            opacity: 0;
            transform: scale(.96);
        }

        .modal-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 16px 20px;
            border-bottom: 1px solid #f1f5f9;
        }

        .modal-title {
            font-size: .95rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1rem;
            color: #94a3b8;
            cursor: pointer;
            padding: 4px 6px;
            border-radius: 6px;
            transition: background .12s, color .12s;
        }

        .modal-close:hover {
            background: #f1f5f9;
            color: #475569;
        }

        .modal-body {
            padding: 18px 20px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .modal-foot {
            display: flex;
            justify-content: flex-end;
            gap: 8px;
            padding: 14px 20px;
            border-top: 1px solid #f1f5f9;
        }

        /* ── New day alert ── */
        .new-day-alert {
            background: #fffbeb;
            border: 1px solid #fcd34d;
            color: #92400e;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: .78rem;
            font-weight: 600;
            margin-left: auto;
        }

        /* ── Responsive ── */
        @media (max-width: 900px) {
            .edit-layout {
                grid-template-columns: 1fr;
            }

            .edit-sidebar {
                position: static;
            }

            .send-opts {
                grid-template-columns: repeat(2, 1fr);
            }

            .day-form {
                flex-direction: column;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        let adaHariBaru = false;

        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('itineraryForm');
            const saveBtn = document.getElementById('saveBtn');

            function getCheckboxState() {
                const state = {};
                form.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                    state[cb.value] = cb.checked;
                });
                return JSON.stringify(state);
            }

            const initialCheckboxState = getCheckboxState();
            let initialFormSnapshot = {};
            form.querySelectorAll('input:not([type="checkbox"]), textarea, select').forEach(el => {
                initialFormSnapshot[el.name || el.id] = el.value;
            });

            function checkChanges() {
                let changed = {{ session('ok') ? 'true' : 'false' }};
                form.querySelectorAll('input:not([type="checkbox"]):not([type="radio"]), textarea, select').forEach(
                    el => {
                        const key = el.name || el.id;
                        if (initialFormSnapshot[key] !== undefined && initialFormSnapshot[key] !== el.value) {
                            changed = true;
                        }
                    });
                if (getCheckboxState() !== initialCheckboxState) changed = true;
                if (adaHariBaru) {
                    saveBtn.disabled = true;
                    return;
                }
                saveBtn.disabled = !changed;
            }

            form.querySelectorAll('input, textarea, select').forEach(el => {
                el.addEventListener('input', checkChanges);
                el.addEventListener('change', checkChanges);
            });
            checkChanges();

            // Auto-save day (city) on change
            document.querySelectorAll('.day-form').forEach(dForm => {
                const saveDay = () => {
                    const formData = new FormData(dForm);
                    formData.append('_method', 'PUT');
                    fetch(dForm.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(r => r.json())
                        .then(data => {
                            const dayNum = dForm.getAttribute('data-day-number');
                            if (!dayNum) return;
                            const dot = document.querySelector(
                                `.status-dot[data-day-number="${dayNum}"]`);
                            const badge = document.querySelector(
                                `.status-badge[data-day-number="${dayNum}"]`);
                            const name = document.querySelector(
                                `.status-name[data-day-number="${dayNum}"]`);
                            const city = dForm.querySelector('select[name="city"]')?.value || '?';
                            if (dot) dot.className = `status-dot status-dot--${data.status}`;
                            if (badge) {
                                badge.className = `status-badge status-badge--${data.status}`;
                                badge.textContent = {
                                    empty: 'Kosong',
                                    incomplete: 'Kurang',
                                    complete: 'Lengkap'
                                } [data.status] || '?';
                            }
                            if (name) name.innerHTML =
                                `Hari ${dayNum} <span style="color:#94a3b8">— ${city}</span>`;
                        })
                        .catch(() => {});
                };
                dForm.querySelectorAll('select, input').forEach(el => el.addEventListener('change',
                    saveDay));
            });

            window.checkChanges = checkChanges;
        });

        function checkNewDaysFilled() {
            const container = document.getElementById('newDaysContainer');
            if (!container || !container.children.length) {
                adaHariBaru = false;
                return;
            }

            let allFilled = true;
            outer:
                for (const row of container.querySelectorAll('.day-row')) {
                    const id = row.id?.replace('new-day-row-', '');
                    if (!id) continue;
                    const city = row.querySelector(`[name="new_days[${id}][city]"]`)?.value?.trim();
                    const date = row.querySelector(`[name="new_days[${id}][date]"]`)?.value?.trim();
                    if (!city || !date) {
                        allFilled = false;
                        break outer;
                    }
                    const items = row.querySelectorAll('.activity-item');
                    for (let idx = 0; idx < items.length; idx++) {
                        const item = items[idx];
                        const title = item.querySelector(`[name="new_days[${id}][items][${idx}][title]"]`)?.value?.trim();
                        const start = item.querySelector(`[name="new_days[${id}][items][${idx}][start_time]"]`)?.value
                            ?.trim();
                        const end = item.querySelector(`[name="new_days[${id}][items][${idx}][end_time]"]`)?.value?.trim();
                        const content = item.querySelector(`[name="new_days[${id}][items][${idx}][content]"]`)?.value
                            ?.trim();
                        if (!title || !start || !end || !content) {
                            allFilled = false;
                            break outer;
                        }
                    }
                }
            adaHariBaru = !allFilled;
            document.getElementById('saveBtn').disabled = !allFilled;
        }

        function editApp() {
            return {
                sendTo: '{{ old('send_to', $itinerary->send_to) }}',
                addModal: false,
                editModal: false,
                activeDayId: null,
                activeItemId: null,
                editTitle: '',
                editStartTime: '',
                editEndTime: '',
                editContent: '',
                startDateWarning: '',
                showNewDayAlert: false,
                newDayStatuses: [],
                originalStartDate: '{{ optional($itinerary->start_date)->format('Y-m-d') }}',
                originalEndDate: '{{ optional($itinerary->end_date)->format('Y-m-d') }}',

                init() {
                    const startInput = document.querySelector('input[name="start_date"]');
                    const endInput = document.querySelector('input[name="end_date"]');
                    const saveBtn = document.getElementById('saveBtn');
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    const diff = (s, e) => Math.ceil((new Date(e) - new Date(s)) / 86400000) + 1;

                    startInput.addEventListener('change', () => {
                        const origStart = new Date(this.originalStartDate);
                        origStart.setHours(0, 0, 0, 0);
                        const newStart = new Date(startInput.value);
                        newStart.setHours(0, 0, 0, 0);
                        if (origStart >= today && newStart < today) {
                            startInput.value = this.originalStartDate;
                            this.startDateWarning = 'Tanggal mulai tidak boleh diubah ke tanggal yang sudah lewat.';
                            return;
                        }
                        this.startDateWarning = '';
                    });

                    endInput.addEventListener('change', () => {
                        const oldDays = diff(this.originalStartDate, this.originalEndDate);
                        const newDays = diff(startInput.value, endInput.value);

                        if (endInput.value === this.originalEndDate) {
                            const container = document.getElementById('newDaysContainer');
                            if (!container.children.length) alert('Tanggal selesai tidak berubah dari sebelumnya.');
                            this.newDayStatuses = [];
                            container.innerHTML = '';
                            this.showNewDayAlert = false;
                            saveBtn.disabled = false;
                            return;
                        }

                        if (newDays < oldDays) {
                            if (!confirm(
                                    `Yakin mengurangi dari ${oldDays} menjadi ${newDays} hari?\n\nHari ke-${newDays+1} dan seterusnya akan dihapus permanen.`
                                )) {
                                endInput.value = this.originalEndDate;
                                return;
                            }
                            this.newDayStatuses = [];
                            document.getElementById('newDaysContainer').innerHTML = '';
                            this.showNewDayAlert = false;
                            let flag = document.getElementById('_reduce_days_flag');
                            if (!flag) {
                                flag = document.createElement('input');
                                flag.type = 'hidden';
                                flag.id = '_reduce_days_flag';
                                flag.name = '_reduce_days';
                                document.getElementById('itineraryForm').appendChild(flag);
                            }
                            flag.value = newDays;
                            saveBtn.disabled = false;
                            document.getElementById('itineraryForm').submit();
                            return;
                        }

                        if (newDays > oldDays) {
                            if (!confirm(`Yakin menambah dari ${oldDays} menjadi ${newDays} hari?`)) {
                                endInput.value = this.originalEndDate;
                                this.newDayStatuses = [];
                                document.getElementById('newDaysContainer').innerHTML = '';
                                this.showNewDayAlert = false;
                                adaHariBaru = false;
                                window.checkChanges && window.checkChanges();
                                return;
                            }
                            adaHariBaru = true;
                            saveBtn.disabled = true;
                            this.showNewDayAlert = true;
                            const container = document.getElementById('newDaysContainer');
                            const existing = container.querySelectorAll('.day-row').length;
                            const base = oldDays + existing;
                            this.newDayStatuses = this.newDayStatuses.filter(d => d.dayNumber <= newDays);
                            for (let i = base + 1; i <= newDays; i++) {
                                const d = new Date(startInput.value);
                                d.setDate(d.getDate() + (i - 1));
                                const fDate = d.toISOString().split('T')[0];
                                this.newDayStatuses.push({
                                    dayNumber: i,
                                    city: ''
                                });
                                container.insertAdjacentHTML('beforeend', `<div class="day-row edit-card" id="new-day-row-${i}" style="margin-top:10px;border-radius:12px">
    <div class="day-row-head" style="cursor:default">
        <div style="display:flex;align-items:center;gap:10px">
            <span class="day-pill">Hari ${i}</span>
            <span style="font-size:.78rem;color:#f87171;font-weight:600"> INI HARI DAN KEGIATAN BARU (SILAKAN ISI DENGAN BENAR DAN TELITI ‼️)</span>
        </div>
    </div>
    <div class="day-row-body">
        <div class="items-section">
            <div class="field-group" style="margin-top:0;margin-bottom:12px">
                <label class="field-label">Kota</label>
                <select name="new_days[${i}][city]" form="itineraryForm" class="field-input"
                    onchange="updateSidebarCity(${i}, this.value); checkNewDaysFilled()">
                    <option value="">-- Pilih Kota --</option>
                    @foreach ($cities as $city)
                        <option value="{{ $city->name }}">{{ $city->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="field-group" style="margin-bottom:12px">
                <label class="field-label">Tanggal</label>
                <input type="date" name="new_days[${i}][date]" form="itineraryForm"
                    class="field-input field-input--readonly" value="${fDate}" readonly>
            </div>
            <button type="button" class="btn-add-activity" onclick="addNewActivity(${i})">+ Tambah Kegiatan</button>
            <div id="activity-container-${i}" style="margin-top:12px">
                <div class="item-row activity-item" style="align-items:flex-start;flex-direction:column;gap:10px">
                    <div class="field-group" style="margin-top:0;width:100%">
                        <label class="field-label">Judul Kegiatan <span style="color:#ef4444">*</span></label>
                        <input type="text" name="new_days[${i}][items][0][title]" form="itineraryForm"
                            class="field-input" placeholder="Cth: Sholat Subuh di Masjidil Haram" required
                            oninput="checkNewDaysFilled()">
                    </div>
                    <div style="display:flex;gap:12px;width:100%">
                        <div class="field-group" style="margin-top:0;flex:1">
                            <label class="field-label">Jam Mulai</label>
                            <input type="time" name="new_days[${i}][items][0][start_time]" form="itineraryForm"
                                class="field-input" onchange="cekWaktuMulai(${i},0,this);checkNewDaysFilled()">
                        </div>
                        <div class="field-group" style="margin-top:0;flex:1">
                            <label class="field-label">Jam Selesai</label>
                            <input type="time" name="new_days[${i}][items][0][end_time]" form="itineraryForm"
                                class="field-input" onchange="cekWaktuSelesai(${i},0,this);checkNewDaysFilled()">
                        </div>
                    </div>
                    <div class="field-group" style="margin-top:0;width:100%">
                        <label class="field-label">Deskripsi</label>
                        <textarea name="new_days[${i}][items][0][content]" form="itineraryForm"
                            class="field-input field-textarea" rows="2" placeholder="Detail kegiatan (opsional)"
                            oninput="checkNewDaysFilled()"></textarea>
                    </div>
                    <div style="display:flex;justify-content:flex-end;width:100%">
                        <button type="button" class="btn-remove-activity" onclick="removeActivity(this)">🗑 Hapus</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>`);
                            }
                            alert('Silakan isi dulu hari dan kegiatan terbaru sebelum menyimpan.');
                        }
                    });
                },

                openAddModal(dayId) {
                    this.activeDayId = dayId;
                    this.addModal = true;
                    document.getElementById('saveBtn').disabled = true;
                },
                openEditModal(itemId, title, startTime, endTime, content) {
                    this.activeItemId = itemId;
                    this.editTitle = title;
                    this.editStartTime = startTime;
                    this.editEndTime = endTime;
                    this.editContent = content;
                    this.editModal = true;
                },
                hapusHari(dayId, dayNumber) {
                    if (!confirm(`Hapus Hari ${dayNumber}?\n\nSemua kegiatan di hari ini akan ikut terhapus.`)) return;
                    const token = document.querySelector('input[name="_token"]')?.value;
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = `/admin/day/${dayId}`;
                    form.innerHTML =
                        `<input type="hidden" name="_token" value="${token}"><input type="hidden" name="_method" value="DELETE">`;
                    document.body.appendChild(form);
                    form.submit();
                },
            }
        }

        function updateSidebarCity(dayIndex, cityValue) {
            const appEl = document.querySelector('[x-data]');
            if (appEl?._x_dataStack) {
                const data = appEl._x_dataStack[0];
                const idx = data.newDayStatuses.findIndex(d => d.dayNumber === dayIndex);
                if (idx !== -1) data.newDayStatuses[idx].city = cityValue;
            }
        }

        function addNewActivity(dayIndex) {
            document.getElementById('saveBtn').disabled = true;
            const container = document.getElementById(`activity-container-${dayIndex}`);
            const total = container.querySelectorAll('.activity-item').length;
            if (total > 0) {
                const lastEnd = container.querySelector(`[name="new_days[${dayIndex}][items][${total-1}][end_time]"]`)
                    ?.value;
                if (!lastEnd) {
                    alert('Lengkapi jam selesai kegiatan sebelumnya dulu.');
                    return;
                }
            }
            container.insertAdjacentHTML('beforeend', `
<div class="item-row activity-item" style="align-items:flex-start;flex-direction:column;gap:10px;margin-top:10px">
    <div class="field-group" style="margin-top:0;width:100%">
        <label class="field-label">Judul <span style="color:#ef4444">*</span></label>
        <input type="text" name="new_days[${dayIndex}][items][${total}][title]" form="itineraryForm"
            class="field-input" placeholder="Cth: Sholat Subuh di Masjidil Haram" required oninput="checkNewDaysFilled()">
    </div>
    <div style="display:flex;gap:12px;width:100%">
        <div class="field-group" style="margin-top:0;flex:1">
            <label class="field-label">Jam Mulai</label>
            <input type="time" name="new_days[${dayIndex}][items][${total}][start_time]" form="itineraryForm"
                class="field-input" onchange="cekWaktuMulai(${dayIndex},${total},this);checkNewDaysFilled()">
        </div>
        <div class="field-group" style="margin-top:0;flex:1">
            <label class="field-label">Jam Selesai</label>
            <input type="time" name="new_days[${dayIndex}][items][${total}][end_time]" form="itineraryForm"
                class="field-input" onchange="cekWaktuSelesai(${dayIndex},${total},this);checkNewDaysFilled()">
        </div>
    </div>
    <div class="field-group" style="margin-top:0;width:100%">
        <label class="field-label">Deskripsi</label>
        <textarea name="new_days[${dayIndex}][items][${total}][content]" form="itineraryForm"
            class="field-input field-textarea" rows="2" placeholder="Detail kegiatan (opsional)"
            oninput="checkNewDaysFilled()"></textarea>
    </div>
    <div style="display:flex;justify-content:flex-end;width:100%">
        <button type="button" class="btn-remove-activity" onclick="removeActivity(this)">🗑 Hapus</button>
    </div>
</div>`);
        }

        function removeActivity(button) {
            const container = button.closest('[id^="activity-container-"]');
            if (container.querySelectorAll('.activity-item').length <= 1) {
                alert('Minimal harus ada 1 kegiatan.');
                return;
            }
            button.closest('.activity-item').remove();
            checkNewDaysFilled();
        }

        function validateTambahWaktu(form) {
            const newStart = form.querySelector('[name="start_time"]').value;
            const newEnd = form.querySelector('[name="end_time"]').value;
            if (!newStart || !newEnd) return true;
            if (newEnd <= newStart) {
                alert('Jam selesai harus lebih besar dari jam mulai.');
                return false;
            }
            let resolvedDayId = null;
            const match = form.action.match(/\/day\/(\d+)\/item/);
            if (match) resolvedDayId = match[1];
            if (resolvedDayId) {
                const chips = [...document.querySelectorAll(`.day-row[data-day-id="${resolvedDayId}"] .item-time-chip`)];
                for (const chip of chips) {
                    const parts = chip.textContent.trim().split(/[-–]/).map(s => s.trim());
                    if (parts.length < 2 || parts[0] === '--') continue;
                    if (newStart < parts[1] && newEnd > parts[0]) {
                        alert(
                            `Waktu bentrok dengan kegiatan ${parts[0]} – ${parts[1]}.\nSilakan pilih waktu setelah jam ${parts[1]}.`
                        );
                        return false;
                    }
                }
            }
            return true;
        }

        function cekWaktuMulai(dayIndex, itemIndex, input) {
            if (itemIndex === 0) return;
            const prevEnd = document.getElementById(`activity-container-${dayIndex}`)
                ?.querySelector(`[name="new_days[${dayIndex}][items][${itemIndex-1}][end_time]"]`)?.value;
            if (prevEnd && input.value <= prevEnd) {
                alert(`Jam mulai harus setelah jam ${prevEnd}.`);
                input.value = '';
            }
        }

        function cekWaktuSelesai(dayIndex, itemIndex, input) {
            const startVal = document.getElementById(`activity-container-${dayIndex}`)
                ?.querySelector(`[name="new_days[${dayIndex}][items][${itemIndex}][start_time]"]`)?.value;
            if (startVal && input.value <= startVal) {
                alert('Jam selesai harus lebih besar dari jam mulai.');
                input.value = '';
            }
        }
    </script>
@endpush
