@extends('layouts.app')

@section('content')
<div class="container-fluid">

    {{-- =====================
        PAGE HEADER
    ===================== --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Riwayat Scan Koper</h4>
    </div>

    {{-- =====================
        FILTER CARD
    ===================== --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <form method="GET" action="{{ route('scans.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Kloter</label>
                    <select name="kloter" class="form-select">
                        <option value="">-- Semua Kloter --</option>
                        @foreach($kloters as $kloter)
                            <option value="{{ $kloter }}"
                                {{ request('kloter') == $kloter ? 'selected' : '' }}>
                                {{ $kloter }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">Tanggal Scan</label>
                    <input type="date" name="date" class="form-control"
                           value="{{ request('date') }}">
                </div>

                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary px-4">Filter</button>
                    <a href="{{ route('scans.index') }}" class="btn btn-outline-secondary">Reset</a>
                    <a href="{{ route('scans.export', request()->only('kloter','date')) }}"
                       class="btn btn-success">
                       ⬇️ Export
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- =====================
        TABLE CARD
    ===================== --}}
    <div class="card shadow-sm">
        <div class="card-body p-0 table-responsive" style="max-height:480px">

            <table class="table table-hover align-middle mb-0">
                <thead class="table-light sticky-top">
                    <tr class="text-center">
                        <th width="60">No</th>
                        <th width="120">Kode Koper</th>
                        <th>Nama Pemilik</th>
                        <th width="150">No Telepon</th>
                        <th width="200">Kloter</th>
                        <th width="160">Status Scan</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @php
                        $sortedScans = $scans->sortBy(fn ($scan) => (int) ltrim($scan->koper_code, '0'));
                    @endphp

                    @forelse($sortedScans as $scan)
                    <tr class="text-center">
                        <td>{{ $loop->iteration }}</td>
                        <td class="fw-bold">{{ $scan->koper_code }}</td>
                        <td>{{ $scan->owner_name ?? '-' }}</td>
                        <td>{{ $scan->owner_phone ?? '-' }}</td>
                        <td class="text-start">{{ $scan->kloter ?? '-' }}</td>
                        <td>
                            @if($scan->scanned_at)
                                <span class="badge bg-success">
                                    {{ $scan->scanned_at->format('d-m-Y H:i') }}
                                </span>
                            @else
                                <span class="badge bg-secondary">Belum discan</span>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary mb-1 w-100"
                                    onclick="showDetail({{ $scan->id }})">
                                Detail
                            </button>
                            <button class="btn btn-sm btn-outline-danger w-100 btn-delete"
                                    data-id="{{ $scan->id }}"
                                    data-kode="{{ $scan->koper_code }}">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">
                            Belum ada data scan koper
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>
</div>

{{-- =====================
    MODAL DETAIL (DESAIN BARU)
===================== --}}
<div class="modal fade" id="detailModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow">

            <div class="modal-header">
                <h5 class="modal-title">Detail Scan Koper</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <div class="row g-4">

                    <div class="col-md-4">
                        <div class="text-muted small">Kode Koper</div>
                        <div class="fw-semibold" id="d_kode">-</div>
                    </div>

                    <div class="col-md-4">
                        <div class="text-muted small">Nama Pemilik</div>
                        <div class="fw-semibold" id="d_nama">-</div>
                    </div>

                    <div class="col-md-4">
                        <div class="text-muted small">No Telepon</div>
                        <div class="fw-semibold" id="d_hp">-</div>
                    </div>

                    <div class="col-md-6">
                        <div class="text-muted small">Kloter</div>
                        <div class="fw-semibold" id="d_kloter">-</div>
                    </div>

                    <div class="col-md-3">
                        <div class="text-muted small">Tour Leader</div>
                        <div class="fw-semibold" id="d_tl">-</div>
                    </div>

                    <div class="col-md-3">
                        <div class="text-muted small">Status Scan</div>
                        <span class="badge bg-success" id="d_status">-</span>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

{{-- =====================
    SCRIPT
===================== --}}
<script>
function showDetail(id) {
    fetch(`/scans/${id}/detail`)
        .then(res => res.json())
        .then(data => {
            d_kode.innerText   = data.koper_code ?? '-';
            d_nama.innerText   = data.owner_name ?? '-';
            d_hp.innerText     = data.owner_phone ?? '-';
            d_kloter.innerText = data.kloter ?? '-';
            d_tl.innerText     = data.tourleader ?? '-';
            d_status.innerText = data.scanned_at ?? '-';

            new bootstrap.Modal(detailModal).show();
        });
}

document.querySelectorAll('.btn-delete').forEach(btn => {
    btn.onclick = function () {
        if (!confirm(`Yakin ingin menghapus scan koper ${this.dataset.kode}?`)) return;

        fetch(`/scans/${this.dataset.id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(res => res.status === 'success' && location.reload());
    };
});
</script>
@endsection
