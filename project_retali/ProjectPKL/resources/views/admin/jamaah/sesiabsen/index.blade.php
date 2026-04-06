@extends('layouts.app')

@section('title', 'Sesi Absen')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0">Sesi Absen</h3>

        <div class="d-flex gap-2">
            <a href="{{ route('jamaah.index') }}" class="btn btn-outline-secondary">
                ← Kembali
            </a>

            <button class="btn btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#createModal">
                + Tambah Sesi
            </button>
        </div>
    </div>

    {{-- SUCCESS --}}
    @if(session('success'))
        <div class="alert alert-success shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- TABLE --}}
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">

            @if ($sesiAbsens->count() === 0)
                <div class="text-center text-muted py-5">
                    Belum ada sesi absen.
                </div>
            @else
                <table class="table align-middle mb-0">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th width="60">No</th>
                            <th>Judul Sesi</th>
                            <th width="120">Jumlah Isi</th>
                            <th width="260" class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($sesiAbsens as $sesi)
                            <tr>
                                <td class="text-muted">
                                    {{ $loop->iteration }}
                                </td>

                                <td class="fw-semibold">
                                    {{ $sesi->judul }}
                                </td>

                                <td>
                                    <span class="badge bg-secondary rounded-pill">
                                        {{ $sesi->items_count }}
                                    </span>
                                </td>

                                <td class="text-center">

                                    {{-- DETAIL --}}
                                    <button class="btn btn-sm btn-outline-dark me-1"
                                            data-bs-toggle="modal"
                                            data-bs-target="#detailModal{{ $sesi->id }}">
                                        Detail
                                    </button>

                                    {{-- EDIT --}}
                                    <a href="{{ route('admin.sesiabsen.edit', $sesi->id) }}"
                                       class="btn btn-sm btn-warning me-1">
                                        Edit
                                    </a>

                                    {{-- DELETE --}}
                                    <form action="{{ route('admin.sesiabsen.destroy', $sesi->id) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Yakin ingin menghapus sesi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">
                                            Hapus
                                        </button>
                                    </form>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

        </div>
    </div>
</div>


{{-- ================= CREATE MODAL ================= --}}
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">

            <form action="{{ route('admin.sesiabsen.store') }}" method="POST">
                @csrf

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">
                        Tambah Sesi Absen
                    </h5>
                    <button type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4">

                    {{-- JUDUL --}}
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            Judul Sesi
                        </label>
                        <input type="text"
                               name="judul"
                               class="form-control form-control-lg"
                               placeholder="Contoh: Bus / Pesawat"
                               required>
                    </div>

                    {{-- ISI --}}
                    <div>
                        <label class="form-label fw-semibold">
                            Isi Sesi Absen
                        </label>

                        <div id="items-wrapper"></div>

                        <button type="button"
                                class="btn btn-sm btn-outline-primary mt-3"
                                onclick="addItem()">
                            + Tambah Isi
                        </button>
                    </div>

                </div>

                <div class="modal-footer">
                    <button class="btn btn-success px-4">
                        Simpan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>


{{-- ================= DETAIL MODALS ================= --}}
@foreach ($sesiAbsens as $sesi)
<div class="modal fade" id="detailModal{{ $sesi->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">

            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold">
                    Detail Sesi Absen
                </h5>
                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4">

                <div class="mb-4">
                    <small class="text-muted">Judul Sesi</small>
                    <div class="fw-bold fs-5">
                        {{ $sesi->judul }}
                    </div>
                </div>

                <div>
                    <small class="text-muted">Daftar Isi</small>

                    <div class="list-group mt-2">
                        @foreach($sesi->items as $item)
                            <div class="list-group-item border-0 border-bottom">
                                {{ $loop->iteration }}. {{ $item->isi }}
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>
@endforeach



{{-- ================= SCRIPT ================= --}}
<script>

let itemIndex = 0;

// Reset saat modal dibuka
document.getElementById('createModal')
.addEventListener('shown.bs.modal', function () {

    const wrapper = document.getElementById('items-wrapper');
    wrapper.innerHTML = '';
    itemIndex = 0;

    addItem(); // otomatis 1 isi pertama
});

function addItem() {

    const wrapper = document.getElementById('items-wrapper');

    itemIndex = wrapper.children.length + 1;

    const div = document.createElement('div');
    div.classList.add('input-group', 'mb-2');

    div.innerHTML = `
        <input type="text"
               name="items[]"
               class="form-control"
               placeholder="Isi ${itemIndex}"
               required>

        <button type="button"
                class="btn btn-outline-danger"
                onclick="removeItem(this)">
            ✕
        </button>
    `;

    wrapper.appendChild(div);
}

function removeItem(button) {

    button.parentElement.remove();

    const inputs = document.querySelectorAll('#items-wrapper input');

    inputs.forEach((input, index) => {
        input.placeholder = "Isi " + (index + 1);
    });
}

</script>

@endsection
