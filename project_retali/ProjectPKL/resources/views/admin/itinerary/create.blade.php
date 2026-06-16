@extends('layouts.app')

@section('content')
    <div class="itinerary-wrap" x-data="itineraryApp()" x-init="restoreDraft()">

        {{-- HEADER --}}
        <div class="itin-header">
            <div>
                <h2 class="itin-title">Buat Itinerary Baru</h2>
                <p class="itin-subtitle">Isi semua detail dalam satu halaman, lalu kirim.</p>
            </div>
            <div class="itin-header-actions">
                <span class="draft-badge" x-show="draftSaved" x-cloak>
                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                        <circle cx="6" cy="6" r="5" stroke="#16a34a" stroke-width="1.5"/>
                        <path d="M3.5 6l1.8 1.8 3-3.6" stroke="#16a34a" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                    Draft tersimpan
                </span>
                <a href="{{ route('admin.itinerary.index') }}" class="btn-ghost">← Batal</a>
            </div>
        </div>

        <form id="itinerary-form" action="{{ route('admin.itinerary.store') }}" method="POST" @submit.prevent="submitForm">
            @csrf
            <div class="itin-layout">

                {{-- KOLOM KIRI --}}
                <div class="itin-main">

                    {{-- CARD 01: Informasi Utama --}}
                    <div class="itin-card">
                        <div class="itin-card-head">
                            <span class="card-num">01</span>
                            <div>
                                <h3>Informasi Itinerary</h3>
                                <p>Judul dan rentang tanggal keberangkatan</p>
                            </div>
                        </div>
                        <div class="itin-card-body">

                            <div class="field-group">
                                <label class="field-label">Judul Itinerary <span class="req">*</span></label>
                                <input type="text" name="title" x-model="form.title" @input="saveDraft"
                                    class="field-input" :class="{ 'field-input--error': errors.title }"
                                    placeholder="Contoh: Umrah Plus Istanbul 9 Hari" maxlength="150">
                                <div class="field-hint" x-show="errors.title" x-cloak x-text="errors.title" style="color:#ef4444"></div>
                                <div class="field-hint" x-show="!errors.title">
                                    <span x-text="form.title.length"></span>/150 karakter
                                </div>
                            </div>

                            <div class="date-row">
                                <div class="field-group" style="flex:1">
                                    <label class="field-label">Tanggal Mulai <span class="req">*</span></label>
                                    <input type="date" name="start_date" x-model="form.start_date" :min="today"
                                        @change="onDateChange(); saveDraft()" class="field-input"
                                        :class="{ 'field-input--error': errors.start_date }">
                                    <div class="field-hint" style="color:#ef4444" x-show="errors.start_date" x-cloak
                                        x-text="errors.start_date"></div>
                                </div>
                                <div class="date-dash">—</div>
                                <div class="field-group" style="flex:1">
                                    <label class="field-label">Tanggal Selesai <span class="req">*</span></label>
                                    <input type="date" name="end_date" x-model="form.end_date"
                                        :min="form.start_date || today" @change="onDateChange(); saveDraft()"
                                        class="field-input" :class="{ 'field-input--error': errors.end_date }">
                                    <div class="field-hint" style="color:#ef4444" x-show="errors.end_date" x-cloak
                                        x-text="errors.end_date"></div>
                                </div>
                                <div class="days-badge" x-show="form.days.length > 0" x-cloak>
                                    <span class="days-count" x-text="form.days.length"></span>
                                    <span class="days-label">Hari</span>
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- CARD 02: Penerima --}}
                    <div class="itin-card">
                        <div class="itin-card-head">
                            <span class="card-num">02</span>
                            <div>
                                <h3>Penerima Itinerary</h3>
                                <p>Pilih siapa yang menerima itinerary ini</p>
                            </div>
                        </div>
                        <div class="itin-card-body">

                            <div class="send-options">
                                <template x-for="opt in sendOptions" :key="opt.value">
                                    <label class="send-option"
                                        :class="{ 'send-option--active': form.send_to === opt.value }">
                                        <input type="radio" name="send_to" :value="opt.value"
                                            x-model="form.send_to" @change="saveDraft" style="display:none">
                                        <span class="send-option-icon" x-text="opt.icon"></span>
                                        <span class="send-option-label" x-text="opt.label"></span>
                                    </label>
                                </template>
                            </div>

                            {{-- Selected Recipients --}}
                            <div x-show="form.send_to === 'selected'" x-cloak x-transition.opacity class="recipient-box">

                                <div class="recip-section-head">
                                    <span class="recip-section-title">Tourleader</span>
                                    <div style="display:flex;gap:8px;align-items:center">
                                        <input type="text" x-model="searchTL" placeholder="Cari nama..."
                                            class="search-input">
                                        <button type="button" @click="selectAllTL" class="btn-xs">Pilih Semua</button>
                                    </div>
                                </div>
                                <div class="recipient-list">
                                    @foreach ($tourLeaders as $tl)
                                        <label class="recip-item"
                                            :class="{ 'recip-item--checked': form.selected_tl.includes({{ $tl->id }}) }"
                                            x-show="'{{ strtolower($tl->name) }}'.includes(searchTL.toLowerCase())">
                                            <input type="checkbox" value="tl:{{ $tl->id }}" name="selected_users[]"
                                                @change="toggleTL({{ $tl->id }}); saveDraft()"
                                                :checked="form.selected_tl.includes({{ $tl->id }})">
                                            <span class="recip-av">{{ strtoupper(substr($tl->name, 0, 1)) }}</span>
                                            <div>
                                                <div style="font-weight:600;font-size:.85rem;color:#0f172a">{{ $tl->name }}</div>
                                                @if ($tl->kloter)
                                                    <div style="font-size:.74rem;color:#94a3b8;margin-top:1px">
                                                        {{ $tl->kloter->nama }} • {{ $tl->kloter->tanggal_label }}
                                                    </div>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>

                                <div class="recip-section-head" style="margin-top:0;border-top:1px solid #f1f5f9">
                                    <span class="recip-section-title">Muthawif</span>
                                    <div style="display:flex;gap:8px;align-items:center">
                                        <input type="text" x-model="searchMW" placeholder="Cari nama..."
                                            class="search-input">
                                        <button type="button" @click="selectAllMW" class="btn-xs">Pilih Semua</button>
                                    </div>
                                </div>
                                <div class="recipient-list">
                                    @foreach ($muthawifs as $mw)
                                        <label class="recip-item"
                                            :class="{ 'recip-item--checked': form.selected_mw.includes({{ $mw->id }}) }"
                                            x-show="'{{ strtolower($mw->nama) }}'.includes(searchMW.toLowerCase())">
                                            <input type="checkbox" value="mw:{{ $mw->id }}" name="selected_users[]"
                                                @change="toggleMW({{ $mw->id }}); saveDraft()"
                                                :checked="form.selected_mw.includes({{ $mw->id }})">
                                            <span class="recip-av" style="background:#7c3aed">{{ strtoupper(substr($mw->nama, 0, 1)) }}</span>
                                            <div>
                                                <div style="font-weight:600;font-size:.85rem;color:#0f172a">{{ $mw->nama }}</div>
                                                @if ($mw->kloter)
                                                    <div style="font-size:.74rem;color:#94a3b8;margin-top:1px">
                                                        {{ $mw->kloter->nama }} • {{ $mw->kloter->tanggal_label }}
                                                    </div>
                                                @endif
                                            </div>
                                        </label>
                                    @endforeach
                                </div>

                            </div>
                        </div>
                    </div>

                    {{-- CARD 03: Kegiatan Per Hari --}}
                    <div class="itin-card" x-show="form.days.length > 0" x-cloak
                        x-transition:enter="card-enter" x-transition:enter-start="card-enter-start">
                        <div class="itin-card-head">
                            <span class="card-num">03</span>
                            <div>
                                <h3>Kegiatan Per Hari</h3>
                                <p>Lengkapi kota, jumlah kegiatan, dan detail tiap hari</p>
                            </div>
                        </div>
                        <div style="padding:0">
                            <template x-for="(day, idx) in form.days" :key="idx">
                                <div class="day-card" :class="`day-card--${getDayStatus(day)}`">
                                    <div class="day-card-head" @click="day.open = !day.open">
                                        <div class="day-card-left">
                                            <span class="day-pill" x-text="'Hari ' + day.day_number"></span>
                                            <span class="day-date" x-text="formatDate(day.date)"></span>
                                        </div>
                                        <div class="day-card-right">
                                            <span class="day-status-badge"
                                                :class="`day-status-badge--${getDayStatus(day)}`"
                                                x-text="getDayStatusLabel(day)">
                                            </span>
                                            <svg class="day-chevron" :class="{ 'day-chevron--open': day.open }"
                                                width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path d="M4 6l4 4 4-4" stroke="currentColor" stroke-width="2"
                                                    stroke-linecap="round" stroke-linejoin="round"/>
                                            </svg>
                                        </div>
                                    </div>

                                    <div class="day-card-body" x-show="day.open" x-collapse>
                                        <div class="day-fields">
                                            <div class="field-group" style="flex:1">
                                                <label class="field-label">Kota</label>
                                                <select :name="`days[${idx}][city]`" x-model="day.city"
                                                    @change="saveDraft" class="field-input">
                                                    <option value="">-- Pilih Kota --</option>
                                                    @foreach (\App\Models\City::orderBy('name')->get() as $city)
                                                        <option value="{{ $city->name }}">{{ $city->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="field-group" style="width:110px">
                                                <label class="field-label">Kegiatan</label>
                                                <select :name="`days[${idx}][item_count]`"
                                                    x-model.number="day.item_count"
                                                    @change="syncItems(day); saveDraft()" class="field-input">
                                                    <option value="0">0</option>
                                                    <template x-for="n in 20" :key="n">
                                                        <option :value="n" x-text="n"></option>
                                                    </template>
                                                </select>
                                            </div>
                                        </div>

                                        <div style="margin-top:12px">
                                            <button type="button" @click="openModal(idx)"
                                                class="btn-fill-activity"
                                                :class="`btn-fill-activity--${getDayStatus(day)}`"
                                                :disabled="getDayStatus(day) === 'empty'">
                                                <template x-if="getDayStatus(day) === 'empty'">
                                                    <span>Lengkapi kota & jumlah kegiatan dulu</span>
                                                </template>
                                                <template x-if="getDayStatus(day) === 'incomplete'">
                                                    <span>⚠ Isi Kegiatan (Belum Lengkap)</span>
                                                </template>
                                                <template x-if="getDayStatus(day) === 'complete'">
                                                    <span>✓ Kegiatan Lengkap — Klik untuk Edit</span>
                                                </template>
                                            </button>
                                        </div>

                                        {{-- Preview items --}}
                                        <template x-if="day.items.filter(i => i.title).length > 0">
                                            <div class="items-preview">
                                                <template x-for="(item, i) in day.items" :key="i">
                                                    <div class="item-preview-row" x-show="item.title">
                                                        <span class="item-time-chip"
                                                            x-text="(item.start_time || '--:--') + ' – ' + (item.end_time || '--:--')">
                                                        </span>
                                                        <span class="item-preview-title" x-text="item.title"></span>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>

                                        {{-- Hidden inputs --}}
                                        <input type="hidden" :name="`days[${idx}][day_number]`" :value="day.day_number">
                                        <input type="hidden" :name="`days[${idx}][city]`" :value="day.city">
                                        <input type="hidden" :name="`days[${idx}][date]`" :value="day.date">
                                        <template x-for="(item, i) in day.items" :key="i">
                                            <span>
                                                <input type="hidden" :name="`days[${idx}][items][${i}][start_time]`" :value="item.start_time">
                                                <input type="hidden" :name="`days[${idx}][items][${i}][end_time]`" :value="item.end_time">
                                                <input type="hidden" :name="`days[${idx}][items][${i}][title]`" :value="item.title">
                                                <input type="hidden" :name="`days[${idx}][items][${i}][content]`" :value="item.content">
                                            </span>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                </div>{{-- /itin-main --}}

                {{-- KOLOM KANAN --}}
                <div class="itin-sidebar">
                    <div class="summary-card">
                        <div class="summary-title">Ringkasan</div>

                        <div class="summary-row">
                            <span class="summary-key">Judul</span>
                            <span class="summary-val" x-text="form.title || '—'"></span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-key">Tanggal</span>
                            <span class="summary-val">
                                <span x-text="form.start_date ? formatDate(form.start_date) : '—'"></span>
                                <template x-if="form.end_date"><span> – </span></template>
                                <span x-text="form.end_date ? formatDate(form.end_date) : ''"></span>
                            </span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-key">Hari</span>
                            <span class="summary-val" x-text="form.days.length ? form.days.length + ' hari' : '—'"></span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-key">Penerima</span>
                            <span class="summary-val" x-text="recipientLabel"></span>
                        </div>

                        <div class="summary-divider"></div>

                        <div class="summary-progress">
                            <template x-for="(day, idx) in form.days" :key="idx">
                                <div class="progress-day">
                                    <span class="progress-dot" :class="`progress-dot--${getDayStatus(day)}`"></span>
                                    <span style="font-size:.8rem;color:#374151" x-text="'Hari ' + day.day_number"></span>
                                    <span class="progress-status" :class="`progress-status--${getDayStatus(day)}`"
                                        x-text="getDayStatusLabel(day)"></span>
                                </div>
                            </template>
                        </div>

                        <div x-show="form.days.length > 0" x-cloak>
                            <div class="summary-divider"></div>
                        </div>

                        <button type="submit" class="btn-submit" :disabled="!isFormReady"
                            :class="{ 'btn-submit--ready': isFormReady }">
                            <template x-if="!isFormReady">
                                <span>Lengkapi semua data dulu</span>
                            </template>
                            <template x-if="isFormReady">
                                <span>Lanjut ke Konfirmasi →</span>
                            </template>
                        </button>

                        <p class="summary-hint" x-show="!isFormReady" x-cloak>
                            Semua hari harus berstatus hijau sebelum bisa dilanjutkan.
                        </p>
                    </div>
                </div>

            </div>
        </form>

        {{-- MODAL: ISI KEGIATAN --}}
        <div class="modal-overlay" x-show="modalOpen" x-cloak @click.self="closeModal"
            x-transition:enter="overlay-enter" x-transition:enter-start="overlay-enter-start"
            x-transition:leave="overlay-leave" x-transition:leave-end="overlay-leave-end">
            <div class="modal-box" x-show="modalOpen"
                x-transition:enter="modal-enter" x-transition:enter-start="modal-enter-start"
                x-transition:leave="modal-leave" x-transition:leave-end="modal-leave-end">

                <div class="modal-head">
                    <div>
                        <h4 class="modal-title"
                            x-text="'Kegiatan — ' + (activeDay ? 'Hari ' + activeDay.day_number : '')"></h4>
                        <p class="modal-sub" x-text="activeDay ? (activeDay.city || 'Kota belum dipilih') : ''"></p>
                    </div>
                    <button type="button" @click="closeModal" class="modal-close">✕</button>
                </div>

                <div class="modal-body">
                    <template x-if="activeDay">
                        <div>
                            <template x-for="(item, i) in activeDay.items" :key="i">
                                <div class="modal-item">
                                    <div class="modal-item-num" x-text="i + 1"></div>
                                    <div class="modal-item-fields">
                                        <div class="modal-item-row">
                                            <div style="width:110px">
                                                <label class="field-label">Mulai</label>
                                                <input type="time" x-model="item.start_time" class="field-input"
                                                    @change="validateStartTime(item, i)">
                                            </div>
                                            <div style="width:110px">
                                                <label class="field-label">Selesai</label>
                                                <input type="time" x-model="item.end_time" class="field-input"
                                                    @change="validateEndTime(item, i)">
                                            </div>
                                            <div style="flex:1">
                                                <label class="field-label">Judul <span class="req">*</span></label>
                                                <input type="text" x-model="item.title" @input="saveDraft"
                                                    class="field-input" :class="{ 'field-input--error': !item.title }"
                                                    placeholder="Cth: Sholat Subuh di Masjidil Haram">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="field-label">Deskripsi</label>
                                            <textarea x-model="item.content" @input="saveDraft"
                                                class="field-input field-textarea" rows="2"
                                                placeholder="Detail kegiatan (opsional)"></textarea>
                                        </div>
                                    </div>
                                    <button type="button" @click="removeItem(i)" class="modal-item-del"
                                        x-show="activeDay.items.length > 1" title="Hapus">✕</button>
                                </div>
                            </template>

                            <button type="button" @click="addItem" class="btn-add-item">
                                + Tambah Kegiatan
                            </button>
                        </div>
                    </template>
                </div>

                <div class="modal-foot">
                    <button type="button" @click="closeModal" class="btn-ghost-sm">Batal</button>
                    <button type="button" @click="saveModal" class="btn-primary-sm">Simpan Kegiatan</button>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('styles')
<style>
    [x-cloak] { display: none !important }

    /* ── Base ── */
    .itinerary-wrap {
        max-width: 1100px;
        margin: 0 auto;
        padding: 28px 20px;
    }

    /* ── Header ── */
    .itin-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    .itin-title {
        font-size: 1.45rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
        letter-spacing: -.02em;
    }
    .itin-subtitle {
        color: #64748b;
        margin: 3px 0 0;
        font-size: .85rem;
    }
    .itin-header-actions {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .draft-badge {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: .78rem;
        color: #16a34a;
        font-weight: 600;
        background: #f0fdf4;
        padding: 4px 10px;
        border-radius: 20px;
        border: 1px solid #bbf7d0;
    }

    /* ── Layout ── */
    .itin-layout {
        display: grid;
        grid-template-columns: 1fr 268px;
        gap: 20px;
        align-items: start;
    }
    .itin-main {
        display: flex;
        flex-direction: column;
        gap: 18px;
    }

    /* ── Cards ── */
    .itin-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #e8ecf0;
        box-shadow: 0 1px 4px rgba(0,0,0,.05);
        overflow: hidden;
        transition: box-shadow .2s;
    }
    .itin-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,.08); }

    .card-enter { transition: all .3s ease; }
    .card-enter-start { opacity: 0; transform: translateY(12px); }

    .itin-card-head {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 22px;
        border-bottom: 1px solid #f1f5f9;
        background: #fafbfc;
    }
    .itin-card-head h3 {
        font-size: .95rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }
    .itin-card-head p {
        font-size: .78rem;
        color: #94a3b8;
        margin: 2px 0 0;
    }
    .itin-card-body { padding: 20px 22px; }

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
    .field-group:first-child { margin-top: 0; }
    .field-label {
        font-size: .78rem;
        font-weight: 600;
        color: #475569;
    }
    .req { color: #ef4444; }
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
        box-shadow: 0 0 0 3px rgba(124,58,237,.1);
    }
    .field-input--error { border-color: #ef4444; }
    .field-textarea { resize: vertical; min-height: 60px; line-height: 1.5; }
    .field-hint { font-size: .75rem; color: #94a3b8; }

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
    .days-badge {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 52px;
        height: 52px;
        background: #ede9fe;
        border-radius: 12px;
        flex-shrink: 0;
    }
    .days-count {
        font-size: 1.2rem;
        font-weight: 800;
        color: #6d28d9;
        line-height: 1;
    }
    .days-label {
        font-size: .62rem;
        font-weight: 700;
        color: #7c3aed;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    /* ── Send Options ── */
    .send-options {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
    }
    .send-option {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 5px;
        padding: 10px 6px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        cursor: pointer;
        transition: border-color .15s, background .15s, transform .15s;
        text-align: center;
        user-select: none;
    }
    .send-option:hover {
        border-color: #a78bfa;
        transform: translateY(-1px);
    }
    .send-option--active {
        border-color: #7c3aed;
        background: #f5f3ff;
    }
    .send-option-icon { font-size: 1.2rem; }
    .send-option-label {
        font-size: .72rem;
        font-weight: 600;
        color: #475569;
    }
    .send-option--active .send-option-label { color: #6d28d9; }

    /* ── Recipients ── */
    .recipient-box {
        margin-top: 14px;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
    }
    .recip-section-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 9px 14px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }
    .recip-section-title {
        font-size: .74rem;
        font-weight: 700;
        color: #475569;
        text-transform: uppercase;
        letter-spacing: .05em;
    }
    .search-input {
        border: 1px solid #e2e8f0;
        border-radius: 7px;
        padding: 4px 10px;
        font-size: .78rem;
        outline: none;
        width: 110px;
        transition: border-color .15s;
    }
    .search-input:focus { border-color: #7c3aed; }
    .recipient-list {
        max-height: 190px;
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
    .recip-item:hover { background: #f5f3ff; }
    .recip-item--checked { background: #ede9fe; }
    .recip-item input { width: 15px; height: 15px; accent-color: #7c3aed; cursor: pointer; flex-shrink: 0; }
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
    .btn-xs {
        font-size: .71rem;
        padding: 3px 9px;
        border: 1px solid #7c3aed;
        border-radius: 6px;
        color: #7c3aed;
        background: transparent;
        cursor: pointer;
        transition: background .12s;
        white-space: nowrap;
    }
    .btn-xs:hover { background: #ede9fe; }

    /* ── Day Cards ── */
    .day-card { border-bottom: 1px solid #f1f5f9; }
    .day-card:last-child { border-bottom: none; }
    .day-card-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 22px;
        cursor: pointer;
        user-select: none;
        transition: background .12s;
    }
    .day-card-head:hover { background: #fafbfc; }
    .day-card-left { display: flex; align-items: center; gap: 10px; }
    .day-pill {
        background: #ede9fe;
        color: #5b21b6;
        font-size: .75rem;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        flex-shrink: 0;
    }
    .day-date { font-size: .8rem; color: #94a3b8; }
    .day-card-right { display: flex; align-items: center; gap: 10px; }
    .day-card-body { padding: 0 22px 18px; }
    .day-fields { display: flex; align-items: flex-end; gap: 12px; margin-bottom: 2px; }
    .day-chevron { color: #94a3b8; transition: transform .22s ease; }
    .day-chevron--open { transform: rotate(180deg); }

    /* Day Status Badge */
    .day-status-badge {
        font-size: .71rem;
        font-weight: 700;
        padding: 3px 9px;
        border-radius: 20px;
    }
    .day-status-badge--empty    { background: #f1f5f9; color: #94a3b8; }
    .day-status-badge--incomplete { background: #fef2f2; color: #ef4444; }
    .day-status-badge--complete { background: #f0fdf4; color: #16a34a; }

    /* Fill Activity Button */
    .btn-fill-activity {
        width: 100%;
        padding: 10px;
        border-radius: 9px;
        font-size: .84rem;
        font-weight: 600;
        border: 1.5px dashed;
        cursor: pointer;
        transition: all .15s;
        margin-top: 10px;
    }
    .btn-fill-activity--empty {
        border-color: #e2e8f0; color: #94a3b8; background: #f8fafc; cursor: not-allowed;
    }
    .btn-fill-activity--incomplete {
        border-color: #fca5a5; color: #ef4444; background: #fef2f2;
    }
    .btn-fill-activity--incomplete:hover { background: #fee2e2; }
    .btn-fill-activity--complete {
        border-color: #86efac; color: #16a34a; background: #f0fdf4;
    }
    .btn-fill-activity--complete:hover { background: #dcfce7; }

    /* Items Preview */
    .items-preview {
        margin-top: 12px;
        display: flex;
        flex-direction: column;
        gap: 3px;
    }
    .item-preview-row {
        display: flex;
        gap: 10px;
        align-items: center;
        padding: 5px 8px;
        border-radius: 6px;
        background: #f8fafc;
    }
    .item-time-chip {
        font-size: .72rem;
        font-weight: 700;
        color: #6d28d9;
        background: #ede9fe;
        padding: 2px 8px;
        border-radius: 20px;
        flex-shrink: 0;
        white-space: nowrap;
    }
    .item-preview-title { font-size: .82rem; color: #374151; }

    /* ── Sidebar ── */
    .itin-sidebar { position: sticky; top: 24px; }
    .summary-card {
        background: #fff;
        border-radius: 14px;
        border: 1px solid #e8ecf0;
        box-shadow: 0 1px 4px rgba(0,0,0,.05);
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
    .summary-row {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 9px;
    }
    .summary-key { font-size: .78rem; color: #94a3b8; flex-shrink: 0; }
    .summary-val { font-size: .78rem; font-weight: 600; color: #0f172a; text-align: right; }
    .summary-divider { height: 1px; background: #f1f5f9; margin: 14px 0; }
    .summary-progress { display: flex; flex-direction: column; gap: 6px; }
    .progress-day { display: flex; align-items: center; gap: 8px; }
    .progress-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
    .progress-dot--empty       { background: #cbd5e1; }
    .progress-dot--incomplete  { background: #f87171; }
    .progress-dot--complete    { background: #22c55e; }
    .progress-status {
        margin-left: auto;
        font-size: .68rem;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 20px;
    }
    .progress-status--empty       { background: #f1f5f9; color: #94a3b8; }
    .progress-status--incomplete  { background: #fef2f2; color: #ef4444; }
    .progress-status--complete    { background: #f0fdf4; color: #16a34a; }
    .summary-hint { font-size: .74rem; color: #94a3b8; text-align: center; margin-top: 8px; }

    /* ── Buttons ── */
    .btn-submit {
        width: 100%;
        padding: 11px;
        border-radius: 10px;
        border: none;
        font-size: .88rem;
        font-weight: 700;
        cursor: pointer;
        background: #e2e8f0;
        color: #94a3b8;
        transition: all .2s;
        margin-top: 4px;
    }
    .btn-submit--ready {
        background: #6d28d9;
        color: #fff;
        box-shadow: 0 4px 14px rgba(109,40,217,.3);
    }
    .btn-submit--ready:hover {
        background: #5b21b6;
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(109,40,217,.35);
    }
    .btn-submit:disabled { cursor: not-allowed; }

    .btn-ghost {
        background: transparent;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        padding: 8px 16px;
        font-size: .84rem;
        font-weight: 600;
        color: #475569;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
        transition: border-color .15s, color .15s;
    }
    .btn-ghost:hover { border-color: #7c3aed; color: #7c3aed; }
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
    .btn-ghost-sm:hover { border-color: #94a3b8; }
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
    .btn-primary-sm:hover { background: #5b21b6; transform: translateY(-1px); }

    /* ── Modal ── */
    .modal-overlay {
        position: fixed;
        inset: 0;
        background: rgba(15,23,42,.45);
        backdrop-filter: blur(3px);
        z-index: 1000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .overlay-enter      { transition: opacity .2s ease; }
    .overlay-enter-start { opacity: 0; }
    .overlay-leave      { transition: opacity .15s ease; }
    .overlay-leave-end  { opacity: 0; }

    .modal-box {
        background: #fff;
        border-radius: 16px;
        width: 100%;
        max-width: 620px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        box-shadow: 0 20px 50px rgba(0,0,0,.18);
    }
    .modal-enter       { transition: all .22s cubic-bezier(.34,1.56,.64,1); }
    .modal-enter-start { opacity: 0; transform: scale(.94) translateY(8px); }
    .modal-leave       { transition: all .15s ease; }
    .modal-leave-end   { opacity: 0; transform: scale(.96); }

    .modal-head {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
    }
    .modal-title { font-size: .95rem; font-weight: 700; color: #0f172a; margin: 0; }
    .modal-sub { font-size: .8rem; color: #7c3aed; margin: 3px 0 0; font-weight: 600; }
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
    .modal-close:hover { background: #f1f5f9; color: #475569; }
    .modal-body {
        overflow-y: auto;
        padding: 18px 20px;
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .modal-foot {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        padding: 14px 20px;
        border-top: 1px solid #f1f5f9;
    }
    .modal-item {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        padding: 14px;
        background: #f8fafc;
        border-radius: 10px;
        border: 1px solid #e8ecf0;
        transition: box-shadow .15s;
    }
    .modal-item:hover { box-shadow: 0 2px 8px rgba(0,0,0,.06); }
    .modal-item-num {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #6d28d9;
        color: #fff;
        font-size: .72rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        margin-top: 2px;
    }
    .modal-item-fields { flex: 1; display: flex; flex-direction: column; gap: 10px; }
    .modal-item-row { display: flex; gap: 10px; }
    .modal-item-del {
        background: none;
        border: none;
        color: #cbd5e1;
        cursor: pointer;
        font-size: .95rem;
        padding: 4px;
        flex-shrink: 0;
        margin-top: 2px;
        border-radius: 6px;
        transition: color .12s, background .12s;
    }
    .modal-item-del:hover { color: #ef4444; background: #fef2f2; }
    .btn-add-item {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        padding: 10px;
        border: 1.5px dashed #c4b5fd;
        border-radius: 9px;
        color: #7c3aed;
        font-size: .84rem;
        font-weight: 600;
        background: transparent;
        cursor: pointer;
        margin-top: 2px;
        transition: background .15s, border-color .15s;
    }
    .btn-add-item:hover { background: #f5f3ff; border-color: #7c3aed; }

    /* ── Responsive ── */
    @media (max-width: 900px) {
        .itin-layout { grid-template-columns: 1fr; }
        .itin-sidebar { position: static; }
        .send-options { grid-template-columns: repeat(2, 1fr); }
        .modal-item-row { flex-direction: column; }
    }
</style>
@endpush

@push('scripts')
<script>
    function itineraryApp() {
        return {
            form: {
                title: '',
                start_date: '',
                end_date: '',
                send_to: 'all_tourleaders',
                selected_tl: [],
                selected_mw: [],
                days: [],
            },
            errors: {},
            draftSaved: false,
            modalOpen: false,
            activeDayIdx: null,
            searchTL: '',
            searchMW: '',

            get today() {
                return new Date().toISOString().split('T')[0];
            },
            get activeDay() {
                return this.activeDayIdx !== null ? this.form.days[this.activeDayIdx] : null;
            },
            get recipientLabel() {
                const map = {
                    all_users:       'Semua Pengguna',
                    all_tourleaders: 'Semua Tourleader',
                    all_muthawif:    'Semua Muthawif',
                    selected: `Terpilih (${this.form.selected_tl.length} TL, ${this.form.selected_mw.length} MW)`,
                };
                return map[this.form.send_to] || '—';
            },
            get isFormReady() {
                if (!this.form.title.trim()) return false;
                if (!this.form.start_date || !this.form.end_date) return false;
                if (this.form.send_to === 'selected' && !this.form.selected_tl.length && !this.form.selected_mw.length) return false;
                if (!this.form.days.length) return false;
                return this.form.days.every(d => this.getDayStatus(d) === 'complete');
            },

            sendOptions: [
                { value: 'all_tourleaders', label: 'Semua Tourleader', icon: '👥' },
                { value: 'all_muthawif',    label: 'Semua Muthawif',   icon: '🕌' },
                { value: 'all_users',       label: 'Semua Pengguna',   icon: '🌐' },
                { value: 'selected',        label: 'Pengguna Tertentu',icon: '🎯' },
            ],

            onDateChange() {
                if (!this.form.start_date || !this.form.end_date) return;
                const start = new Date(this.form.start_date);
                const end   = new Date(this.form.end_date);
                if (end < start) { this.form.end_date = ''; return; }
                const diff     = Math.round((end - start) / 86400000) + 1;
                const existing = this.form.days;
                const newDays  = [];
                for (let i = 0; i < diff; i++) {
                    const d = new Date(start);
                    d.setDate(d.getDate() + i);
                    const dateStr = d.toISOString().split('T')[0];
                    const ex = existing[i];
                    newDays.push(ex
                        ? { ...ex, day_number: i + 1, date: dateStr }
                        : { day_number: i + 1, date: dateStr, city: '', item_count: 0, items: [], open: false }
                    );
                }
                this.form.days = newDays;
                this.saveDraft();
            },

            syncItems(day) {
                const n = parseInt(day.item_count) || 0;
                while (day.items.length < n) day.items.push({ start_time:'', end_time:'', title:'', content:'' });
                if (day.items.length > n) day.items.splice(n);
            },

            getDayStatus(day) {
                if (!day.city || !day.item_count) return 'empty';
                const n = parseInt(day.item_count) || 0;
                if (day.items.length < n) return 'incomplete';
                return day.items.every(i => i.start_time && i.end_time && i.title?.trim())
                    ? 'complete' : 'incomplete';
            },

            getDayStatusLabel(day) {
                return { empty:'Belum Siap', incomplete:'Belum Lengkap', complete:'Lengkap' }[this.getDayStatus(day)];
            },

            formatDate(str) {
                if (!str) return '';
                return new Date(str + 'T00:00:00').toLocaleDateString('id-ID', {
                    day: '2-digit', month: 'short', year: 'numeric'
                });
            },

            openModal(idx) {
                this.activeDayIdx = idx;
                this.syncItems(this.form.days[idx]);
                this.modalOpen = true;
            },
            closeModal() { this.modalOpen = false; this.activeDayIdx = null; },
            saveModal()  { this.saveDraft(); this.closeModal(); },

            addItem() {
                const items = this.activeDay.items;
                if (items.length > 0 && !items[items.length - 1].end_time) {
                    alert('Lengkapi jam selesai kegiatan sebelumnya dulu.'); return;
                }
                this.activeDay.items.push({ start_time:'', end_time:'', title:'', content:'' });
                this.activeDay.item_count = this.activeDay.items.length;
            },
            removeItem(i) {
                if (this.activeDay.items.length <= 1) return;
                this.activeDay.items.splice(i, 1);
                this.activeDay.item_count = this.activeDay.items.length;
            },

            validateStartTime(item, index) {
                if (index === 0) return;
                const prev = this.activeDay.items[index - 1];
                if (prev?.end_time && item.start_time < prev.end_time) {
                    alert(`Jam mulai harus setelah jam ${prev.end_time}.`);
                    item.start_time = '';
                }
            },
            validateEndTime(item, index) {
                if (!item.start_time) return;
                if (item.end_time <= item.start_time) {
                    alert('Jam selesai harus lebih besar dari jam mulai.');
                    item.end_time = ''; return;
                }
                const next = this.activeDay.items[index + 1];
                if (next?.start_time && item.end_time > next.start_time) {
                    alert(`Jam selesai bentrok dengan kegiatan berikutnya (mulai jam ${next.start_time}).`);
                    item.end_time = '';
                }
            },

            toggleTL(id) {
                const idx = this.form.selected_tl.indexOf(id);
                idx === -1 ? this.form.selected_tl.push(id) : this.form.selected_tl.splice(idx, 1);
            },
            toggleMW(id) {
                const idx = this.form.selected_mw.indexOf(id);
                idx === -1 ? this.form.selected_mw.push(id) : this.form.selected_mw.splice(idx, 1);
            },
            selectAllTL() {
                this.form.selected_tl = [...document.querySelectorAll('input[name="selected_users[]"][value^="tl:"]')]
                    .map(el => parseInt(el.value.replace('tl:', '')));
            },
            selectAllMW() {
                this.form.selected_mw = [...document.querySelectorAll('input[name="selected_users[]"][value^="mw:"]')]
                    .map(el => parseInt(el.value.replace('mw:', '')));
            },

            saveDraft() {
                try {
                    localStorage.setItem('itinerary_draft', JSON.stringify(this.form));
                    this.draftSaved = true;
                    setTimeout(() => this.draftSaved = false, 2000);
                } catch(e) {}
            },
            restoreDraft() {
                try {
                    const raw = localStorage.getItem('itinerary_draft');
                    if (raw) this.form = { ...this.form, ...JSON.parse(raw) };
                } catch(e) {}
            },

            submitForm() {
                this.errors = {};
                if (!this.form.title.trim())    { this.errors.title      = 'Judul wajib diisi.';           return; }
                if (!this.form.start_date)      { this.errors.start_date = 'Tanggal mulai wajib diisi.';   return; }
                if (!this.form.end_date)        { this.errors.end_date   = 'Tanggal selesai wajib diisi.'; return; }
                if (!this.isFormReady) return;
                localStorage.removeItem('itinerary_draft');
                document.getElementById('itinerary-form').submit();
            },
        }
    }
</script>
@endpush