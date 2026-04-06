{{-- ============================= --}}
{{-- MODAL DETAIL SCAN PASPOR --}}
{{-- ============================= --}}
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-4">

            {{-- HEADER --}}
            <div class="modal-header bg-light border-0 px-4 pt-4 pb-2">
                <div>
                    <h5 class="modal-title mb-0 fw-semibold">
                        Detail Scan Paspor
                    </h5>
                    <small class="text-muted">
                        Informasi hasil pemindaian paspor jamaah
                    </small>
                </div>
                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
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

{{-- ============================= --}}
{{-- STYLE --}}
{{-- ============================= --}}
@push('styles')
<style>
.passport-detail .label {
    font-size: 12px;
    color: #6b7280;
    letter-spacing: .3px;
}
.passport-detail .value {
    font-weight: 600;
    color: #111827;
}
.passport-hero {
    background: linear-gradient(135deg, rgba(13,110,253,.08), rgba(13,110,253,.02));
    border: 1px solid rgba(13,110,253,.2);
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

    // RESET CONTENT
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
            <div class="passport-detail">

                <!-- PASSPORT HERO -->
                <div class="passport-hero rounded-4 p-4 mb-4">
                    <div class="label mb-1">Nomor Paspor</div>
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

                <!-- DETAIL GRID -->
                <div class="row g-4">

                    <div class="col-md-6">
                        <div class="label">Nama Pemilik</div>
                        <div class="value fs-6">
                            ${d.owner_name ?? '-'}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="label">Telepon</div>
                        <div class="value fs-6">
                            ${d.owner_phone ?? '-'}
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="label">Kloter</div>
                        <div class="value">
                            ${d.kloter ?? '-'}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="label">Tour Leader</div>
                        <div class="value">
                            ${d.tourleader && d.tourleader.name ? d.tourleader.name : '-'}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="label">Waktu Scan</div>
                        <div class="value">
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
