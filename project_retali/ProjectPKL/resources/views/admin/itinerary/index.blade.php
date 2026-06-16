@extends('layouts.app')

@section('content')
    <div class="index-wrap">

        {{-- HEADER --}}
        <div class="index-header">
            <div>
                <h2 class="index-title">Manajemen Itinerary</h2>
                <p class="index-sub">Kelola semua itinerary perjalanan umroh & haji</p>
            </div>

            <div class="header-actions">
                <a href="{{ route('admin.itinerary.kota.index') }}" class="btn-city">
                    <i class="fas fa-city"></i>
                    Liat Daftar Kota
                </a>

                <a href="{{ route('admin.itinerary.create') }}" class="btn-create">
                    + Buat Itinerary
                </a>
            </div>
        </div>

        @if (session('ok'))
            <div class="alert-ok">{{ session('ok') }}</div>
        @endif

        {{-- TABLE --}}
        @if ($itineraries->count())
            <div class="table-card">
                <table class="itin-table">
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Periode</th>
                            <th>Hari</th>
                            <th>Penerima</th>
                            <th>Status</th>
                            <th>Dibuat</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($itineraries as $it)
                            <tr>
                                <td>
                                    <a href="{{ route('admin.itinerary.show', $it) }}" class="itin-name">
                                        {{ $it->title }}
                                    </a>
                                </td>
                                <td class="text-sm text-muted">
                                    {{ optional($it->start_date)->format('d M Y') }}
                                    @if ($it->end_date)
                                        — {{ $it->end_date->format('d M Y') }}
                                    @endif
                                </td>
                                <td>
                                    <span class="days-chip">{{ $it->days_count }} hari</span>
                                </td>
                                <td class="text-sm">{{ $it->recipient_label }}</td>
                                <td>
                                    <span class="status-badge status-badge--{{ $it->status }}">
                                        {{ $it->status === 'sent' ? 'Terkirim' : 'Draft' }}
                                    </span>
                                </td>
                                <td class="text-sm text-muted">{{ $it->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="row-actions">
                                        <a href="{{ route('admin.itinerary.show', $it) }}" class="action-btn">👁</a>
                                        <a href="{{ route('admin.itinerary.edit', $it) }}" class="action-btn">✏️</a>
                                        <form action="{{ route('admin.itinerary.destroy', $it) }}" method="POST"
                                            onsubmit="return confirm('Hapus itinerary ini?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="action-btn">🗑</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div style="margin-top:16px">
                {{ $itineraries->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">📋</div>
                <h3>Belum ada itinerary</h3>
                <p>Buat itinerary pertama Anda untuk mulai mengatur perjalanan.</p>
                <a href="{{ route('admin.itinerary.create') }}" class="btn-create">+ Buat Itinerary Pertama</a>
            </div>
        @endif

    </div>
@endsection

@push('styles')
    <style>
        .index-wrap {
            max-width: 1100px;
            margin: 0 auto;
            padding: 24px 20px;
            display: flex;
            flex-direction: column;
            gap: 20px
        }

        .index-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start
        }

        .index-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0
        }

        .header-actions {
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .btn-city {
            background: #fff;
            color: #4f46e5;
            border: 1px solid #c7d2fe;
            border-radius: 10px;
            padding: 11px 18px;
            font-size: .88rem;
            font-weight: 700;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: .2s;
        }

        .btn-city:hover {
            background: #eef2ff;
            color: #4338ca;
            transform: translateY(-1px);
        }

        .index-sub {
            font-size: .88rem;
            color: #64748b;
            margin: 4px 0 0
        }

        .btn-create {
            background: linear-gradient(135deg, #6366f1, #7c3aed);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 11px 22px;
            font-size: .88rem;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            display: inline-block
        }

        .btn-create:hover {
            opacity: .9;
            transform: translateY(-1px)
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

        .table-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .07), 0 4px 16px rgba(0, 0, 0, .04);
            overflow: hidden
        }

        .itin-table {
            width: 100%;
            border-collapse: collapse
        }

        .itin-table thead th {
            padding: 12px 16px;
            font-size: .75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
            color: #94a3b8;
            border-bottom: 1px solid #f1f5f9;
            background: #fafafa;
            white-space: nowrap
        }

        .itin-table tbody tr {
            border-bottom: 1px solid #f8fafc;
            transition: background .12s
        }

        .itin-table tbody tr:last-child {
            border-bottom: none
        }

        .itin-table tbody tr:hover {
            background: #fafafe
        }

        .itin-table td {
            padding: 14px 16px;
            vertical-align: middle
        }

        .itin-name {
            font-weight: 700;
            color: #0f172a;
            text-decoration: none;
            font-size: .9rem
        }

        .itin-name:hover {
            color: #6366f1
        }

        .text-sm {
            font-size: .83rem
        }

        .text-muted {
            color: #94a3b8
        }

        .days-chip {
            background: #e0e7ff;
            color: #4338ca;
            font-size: .75rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px
        }

        .status-badge {
            font-size: .72rem;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 20px
        }

        .status-badge--sent {
            background: #d1fae5;
            color: #065f46
        }

        .status-badge--draft {
            background: #fef3c7;
            color: #92400e
        }

        .row-actions {
            display: flex;
            gap: 4px
        }

        .action-btn {
            background: none;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 5px 9px;
            font-size: .85rem;
            cursor: pointer;
            text-decoration: none;
            color: inherit
        }

        .action-btn:hover {
            background: #f5f3ff;
            border-color: #6366f1
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, .07), 0 4px 16px rgba(0, 0, 0, .04)
        }

        .empty-icon {
            font-size: 3rem;
            margin-bottom: 12px
        }

        .empty-state h3 {
            font-size: 1.1rem;
            font-weight: 700;
            color: #0f172a;
            margin: 0 0 8px
        }

        .empty-state p {
            color: #64748b;
            font-size: .88rem;
            margin: 0 0 20px
        }
    </style>
@endpush
