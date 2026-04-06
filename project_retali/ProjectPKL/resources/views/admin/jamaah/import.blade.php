@extends('layouts.app')

@section('title', 'Import Absen Jamaah')

@section('content')
<div class="container-fluid">

    <h1 class="h3 fw-bold mb-4">Import Absen Jamaah</h1>

    {{-- ERROR --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('jamaah.import') }}"
          method="POST"
          enctype="multipart/form-data">
        @csrf

        {{-- KONTEXT SESI --}}
        <div class="card mb-4">
            <div class="card-body">

                <div class="row">

                   {{-- KLOTER --}}
<div class="col-md-6 mb-3">
    <label class="form-label fw-bold">Kloter</label>
    <select name="kloter_id"
            id="kloter_id"
            class="form-select"
            required>
        <option value="">-- Pilih Kloter --</option>
        @foreach ($kloters as $kloter)
            <option value="{{ $kloter->id }}">
                {{ $kloter->nama }}
                ({{ $kloter->tanggal_label }})
            </option>
        @endforeach
    </select>
</div>


                    {{-- SESI ABSEN --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label fw-bold">Sesi Absen</label>
                        <select name="sesi_absen_id"
                                id="sesi_absen_id"
                                class="form-select"
                                required>
                            <option value="">-- Pilih Sesi --</option>
                            @foreach ($sesiAbsens as $s)
                                <option value="{{ $s->id }}">
                                    {{ $s->judul }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                {{-- BAGIAN SESI --}}
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            Bagian Sesi Absen
                        </label>
                        <select name="sesi_absen_item_id"
                                id="sesi_absen_item_id"
                                class="form-select"
                                disabled
                                required>
                            <option value="">-- Pilih Bagian --</option>
                        </select>
                    </div>
                </div>

            </div>
        </div>

        {{-- UPLOAD --}}
        <div class="card">
            <div class="card-body">

                <h5 class="fw-bold mb-3">
                    Upload File Excel per Tour Leader
                </h5>

                <div id="tourleader-wrapper">
                    <p class="text-muted">
                        Pilih kloter untuk menampilkan tour leader.
                    </p>
                </div>

                <div class="mt-4 text-end">
                    <button class="btn btn-primary px-4">
                        Selesai
                    </button>
                </div>

            </div>
        </div>

    </form>
</div>
@endsection
@push('scripts')
<script>
/* LOAD BAGIAN SESI */
document.getElementById('sesi_absen_id')
.addEventListener('change', function () {

    const sesiId = this.value;
    const target = document.getElementById('sesi_absen_item_id');

    target.innerHTML = '<option>Loading...</option>';
    target.disabled = true;

    if (!sesiId) return;

    fetch(`/admin/sesi-absen/${sesiId}/items`)
        .then(res => res.json())
        .then(items => {
            target.innerHTML = '<option value="">-- Pilih Bagian --</option>';
            items.forEach(item => {
                target.innerHTML += `
                    <option value="${item.id}">
                        ${item.isi}
                    </option>`;
            });
            target.disabled = false;
        });
});

/* LOAD TOUR LEADER */
document.getElementById('kloter_id')
.addEventListener('change', function () {

    const kloterId = this.value;
    const wrapper = document.getElementById('tourleader-wrapper');

    if (!kloterId) {
        wrapper.innerHTML = '<p class="text-muted">Pilih kloter.</p>';
        return;
    }

    wrapper.innerHTML = '<p>Loading...</p>';

    fetch(`/admin/kloter/${kloterId}/tourleaders`)
        .then(res => res.json())
        .then(data => {

            if (data.length === 0) {
                wrapper.innerHTML =
                    '<p class="text-muted">Tidak ada tour leader.</p>';
                return;
            }

            wrapper.innerHTML = '';

            data.forEach(tl => {
                wrapper.innerHTML += `
                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            Tour Leader: ${tl.name}
                        </label>

                        <div class="small text-muted mb-2">
                            Format Excel:
                            nama_jamaah, no_paspor, no_hp, jenis_kelamin,
                            tanggal_lahir, kode_kloter, nomor_bus, keterangan
                        </div>

                        <input type="file"
                               name="files[${tl.id}]"
                               class="form-control"
                               accept=".xlsx,.xls,.csv">
                    </div>
                `;
            });
        });
});
</script>
@endpush
