@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h4 class="mb-4">Riwayat Scan Paspor</h4>

    <form class="row g-2 mb-3 align-items-end" method="GET">

    {{-- TANGGAL --}}
    <div class="col-md-3">
        <label class="form-label text-muted small">Tanggal Scan</label>
        <input type="date"
               name="tanggal"
               class="form-control"
               value="{{ request('tanggal') }}">
    </div>

    {{-- KLOTER --}}
    <div class="col-md-4">
        <label class="form-label text-muted small">Kloter</label>
        <select name="kloter" class="form-select">
            <option value="">— Semua Kloter —</option>
            @foreach ($kloters as $kloter)
                <option value="{{ $kloter }}"
                    {{ request('kloter') === $kloter ? 'selected' : '' }}>
                    {{ $kloter }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- BUTTON --}}
    <div class="col-md-5 d-flex gap-2">
        <button class="btn btn-primary">
            Refresh
        </button>

        <a href="{{ route('scan-paspor.export', request()->query()) }}"
           class="btn btn-success">
            Export Excel
        </a>
    </div>

</form>

    {{-- TABLE --}}
    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="60">No</th>
                        <th>No Paspor</th>
                        <th>Nama Pemilik</th>
                        <th>Telepon</th>
                        <th>Kloter</th>
                        <th>Waktu Scan</th>
                        <th width="140">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                @forelse ($scans as $i => $scan)
                    <tr>
                        <td class="text-muted">
                            {{ $scans->firstItem() + $i }}
                        </td>

                        <td class="fw-bold text-primary">
                            {{ $scan->passport_number }}
                        </td>

                        <td>
                            {{ $scan->owner_name ?: '-' }}
                        </td>

                        <td class="text-nowrap">
                            {{ $scan->owner_phone ?: '-' }}
                        </td>

                        <td style="max-width:280px">
                            <span class="text-muted">
                                {{ $scan->kloter ?: '-' }}
                            </span>
                        </td>

                        <td class="text-nowrap">
                            {{ $scan->scanned_at->format('d-m-Y H:i') }}
                        </td>

                        <td class="text-nowrap">
                            <button class="btn btn-sm btn-outline-primary"
                                    onclick="showDetail({{ $scan->id }})">
                                Detail
                            </button>

                            <form method="POST"
                                  action="{{ route('scan-paspor.destroy', $scan->id) }}"
                                  class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"
                                        onclick="return confirm('Hapus data scan paspor ini?')">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7"
                            class="text-center text-muted py-4">
                            Data kosong
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>

            {{ $scans->links() }}
        </div>
    </div>
</div>

{{-- ============================= --}}
{{-- MODAL DETAIL SCAN PASPOR --}}
{{-- ============================= --}}
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">

            {{-- HEADER --}}
            <div class="modal-header bg-light border-0 px-4 pt-4 pb-2">
                <div>
                    <h5 class="modal-title fw-semibold mb-0">
                        Detail Scan Paspor
                    </h5>
                    <small class="text-muted">
                        Informasi hasil pemindaian paspor
                    </small>
                </div>
                <button class="btn-close"
                        data-bs-dismiss="modal"></button>
            </div>

            {{-- BODY --}}
            <div class="modal-body px-4 pb-4" id="detailContent">
                <div class="text-center text-muted py-5">
                    <div class="spinner-border spinner-border-sm me-2"></div>
                    Memuat detail paspor...
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

{{-- ============================= --}}
{{-- STYLE --}}
{{-- ============================= --}}
@push('styles')
<style>
.passport-hero {
    background: linear-gradient(135deg,
        rgba(13,110,253,.1),
        rgba(13,110,253,.03)
    );
    border: 1px solid rgba(13,110,253,.2);
}
.passport-label {
    font-size: 12px;
    color: #6b7280;
    letter-spacing: .3px;
}
.passport-value {
    font-weight: 600;
    color: #111827;
}
</style>
@endpush

{{-- ============================= --}}
{{-- SCRIPT --}}
{{-- ============================= --}}
@push('scripts')
<script>
function showDetail(id) {
    const modalEl   = document.getElementById('detailModal');
    const contentEl = document.getElementById('detailContent');
    const modal     = new bootstrap.Modal(modalEl);

    contentEl.innerHTML = `
        <div class="text-center text-muted py-5">
            <div class="spinner-border spinner-border-sm me-2"></div>
            Memuat detail paspor...
        </div>
    `;

    fetch(`/scan-paspor/${id}`, {
        headers: { 'Accept': 'application/json' }
    })
    .then(res => {
        if (res.redirected) throw new Error('SESSION_EXPIRED');
        if (!res.ok) throw new Error('HTTP_ERROR');
        return res.json();
    })
    .then(d => {
        contentEl.innerHTML = `
            <div>

                <div class="passport-hero rounded-4 p-4 mb-4">
                    <div class="passport-label mb-1">
                        Nomor Paspor
                    </div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="fs-3 fw-bold text-primary">
                            ${d.passport_number ?? '-'}
                        </div>
                        <button class="btn btn-sm btn-outline-primary"
                                onclick="navigator.clipboard && navigator.clipboard.writeText('${d.passport_number ?? ''}')">
                            Salin
                        </button>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="passport-label">Nama Pemilik</div>
                        <div class="passport-value">
                            ${d.owner_name ?? '-'}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="passport-label">Telepon</div>
                        <div class="passport-value">
                            ${d.owner_phone ?? '-'}
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="passport-label">Kloter</div>
                        <div class="passport-value">
                            ${d.kloter ?? '-'}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="passport-label">Tour Leader</div>
                        <div class="passport-value">
                            ${d.tourleader?.name ?? '-'}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="passport-label">Waktu Scan</div>
                        <div class="passport-value">
                            ${d.scanned_at ?? '-'}
                        </div>
                    </div>
                </div>

            </div>
        `;
    })
    .catch(err => {
        contentEl.innerHTML =
            err.message === 'SESSION_EXPIRED'
            ? `<div class="alert alert-warning mb-0">
                    Session habis. Silakan refresh halaman.
               </div>`
            : `<div class="alert alert-danger mb-0">
                    Gagal memuat detail scan paspor.
               </div>`;
    });

    modal.show();
}
</script>
@endpush
