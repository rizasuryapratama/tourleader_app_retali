@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">
                <i class="bi bi-calendar-event me-2"></i> Daftar Kloter
            </h2>
            <small class="text-muted">Kelola jadwal keberangkatan kloter</small>
        </div>

        <button class="btn btn-primary px-4 rounded-pill shadow-sm"
                data-bs-toggle="modal"
                data-bs-target="#modalTambahKloter">
            <i class="bi bi-plus-lg me-1"></i> Tambah Kloter
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success shadow-sm border-0">
            {{ session('success') }}
        </div>
    @endif

    <!-- CARD TABLE -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table align-middle mb-0">

                    <thead style="background: linear-gradient(135deg,#1f3c88,#162c5c); color:white;">
                        <tr>
                            <th width="60" class="text-center">No</th>
                            <th>Nama Kloter</th>
                            <th>Tanggal</th>
                            <th width="220" class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($kloter as $index => $k)
                        <tr class="border-top">
                            <td class="text-center fw-semibold">
                                {{ $index + 1 }}
                            </td>

                            <td class="fw-semibold">
                                {{ $k->nama }}
                            </td>

                            <td>
                                <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
                                    {{ $k->tanggal }}
                                </span>
                            </td>

                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">

                                    <!-- EDIT -->
                                    <button class="btn btn-sm btn-light border rounded-pill px-3 shadow-sm action-edit"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditKloter{{ $k->id }}">
                                        <i class="bi bi-pencil-fill text-primary me-1"></i>
                                        Edit
                                    </button>

                                    <!-- DELETE -->
                                    <form action="{{ route('kloter.destroy', $k->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Yakin hapus kloter ini?')">
                                        @csrf
                                        @method('DELETE')

                                        <button class="btn btn-sm btn-light border rounded-pill px-3 shadow-sm action-delete">
                                            <i class="bi bi-trash-fill text-danger me-1"></i>
                                            Hapus
                                        </button>
                                    </form>

                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-muted">
                                Belum ada data kloter.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>

</div>


<!-- ================= MODAL TAMBAH ================= -->
<div class="modal fade" id="modalTambahKloter" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">

            <div class="modal-header text-white border-0"
                 style="background: linear-gradient(135deg,#1f3c88,#162c5c);">
                <h5 class="modal-title">
                    <i class="bi bi-plus-circle me-2 text-warning"></i>
                    Tambah Kloter
                </h5>
                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('kloter.store') }}" method="POST">
                @csrf

                <div class="modal-body p-4">
                    <div class="form-floating mb-3">
                        <input type="text"
                               name="nama"
                               class="form-control"
                               placeholder="Nama Kloter"
                               required>
                        <label>Nama Kloter</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="text"
                               name="tanggal"
                               class="form-control"
                               placeholder="Tanggal"
                               required>
                        <label>Tanggal</label>
                    </div>
                </div>

                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button"
                            class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit"
                            class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="bi bi-save-fill me-1"></i>
                        Simpan
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>


<!-- ================= MODAL EDIT (DI LUAR TABLE) ================= -->
@foreach($kloter as $k)
<div class="modal fade" id="modalEditKloter{{ $k->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">

            <div class="modal-header text-white border-0"
                 style="background: linear-gradient(135deg,#1f3c88,#162c5c);">
                <h5 class="modal-title">
                    <i class="bi bi-pencil-square me-2 text-warning"></i>
                    Edit Kloter
                </h5>
                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('kloter.update', $k->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="modal-body p-4">

                    <div class="form-floating mb-3">
                        <input type="text"
                               name="nama"
                               class="form-control"
                               value="{{ $k->nama }}"
                               required>
                        <label>Nama Kloter</label>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="text"
                               name="tanggal"
                               class="form-control"
                               value="{{ $k->tanggal }}"
                               required>
                        <label>Tanggal</label>
                    </div>

                </div>

                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button"
                            class="btn btn-light rounded-pill px-4"
                            data-bs-dismiss="modal">
                        Batal
                    </button>

                    <button type="submit"
                            class="btn btn-primary rounded-pill px-4 shadow-sm">
                        <i class="bi bi-save-fill me-1"></i>
                        Update
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>
@endforeach


<!-- Hover Effect -->
<style>
.action-edit:hover {
    background-color: #e7f1ff;
    border-color: #0d6efd;
    transform: translateY(-1px);
    transition: 0.2s ease;
}

.action-delete:hover {
    background-color: #ffeaea;
    border-color: #dc3545;
    transform: translateY(-1px);
    transition: 0.2s ease;
}
</style>

@endsection
