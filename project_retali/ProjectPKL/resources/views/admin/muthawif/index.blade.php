@extends('layouts.app')

@section('content')
<div class="container-fluid p-4">

    <!-- ================= HEADER ================= -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold mb-0">
                <i class="bi bi-people me-2"></i> Daftar Muthawif
            </h2>
            <small class="text-muted">Kelola data muthawif & penempatan kloter</small>
        </div>

        <button class="btn btn-primary rounded-pill px-4 shadow-sm"
                data-bs-toggle="modal"
                data-bs-target="#modalTambahMuthawif">
            <i class="bi bi-plus-lg me-1"></i> Tambah Muthawif
        </button>
    </div>

    <!-- SUCCESS ALERT -->
    @if(session('success'))
        <div class="alert alert-success shadow-sm border-0">
            {{ session('success') }}
        </div>
    @endif

    <!-- ================= TABLE ================= -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-body p-0">

            <div class="table-responsive">
                <table class="table align-middle mb-0">

                    <thead style="background: linear-gradient(135deg,#1f3c88,#162c5c); color:white;">
                        <tr>
                            <th class="px-4 py-3">Nama</th>
                            <th class="px-4 py-3">Email</th>
                            <th class="px-4 py-3">Kloter</th>
                            <th class="px-4 py-3 text-center" width="240">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($muthawifs as $muthawif)
                        <tr>
                            <td class="px-4 py-3 fw-semibold">
                                {{ $muthawif->nama }}
                            </td>

                            <td class="px-4 py-3">
                                {{ $muthawif->email }}
                            </td>

                            <td class="px-4 py-3">
                                @if($muthawif->kloter)
                                    <div class="fw-semibold">
                                        {{ $muthawif->kloter->nama }}
                                    </div>
                                    <small class="text-muted">
                                        {{ $muthawif->kloter->tanggal }}
                                    </small>
                                @else
                                    <span class="text-muted">Belum ditentukan</span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-center">
                                <div class="d-flex justify-content-center gap-2">

                                    <!-- EDIT -->
                                    <a href="{{ route('muthawif.edit', $muthawif->id) }}"
                                       class="btn btn-sm btn-light border rounded-pill px-3 shadow-sm">
                                        <i class="bi bi-pencil-fill text-primary me-1"></i>
                                        Edit
                                    </a>

                                    <!-- DELETE -->
                                    <form action="{{ route('muthawif.destroy', $muthawif->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('Yakin ingin menghapus muthawif ini?')">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-sm btn-light border rounded-pill px-3 shadow-sm">
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
                                Belum ada data muthawif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>
</div>


<!-- ================= MODAL TAMBAH MUTHWAF ================= -->
<div class="modal fade" id="modalTambahMuthawif" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg">

            <div class="modal-header text-white border-0"
                 style="background: linear-gradient(135deg,#1f3c88,#162c5c);">
                <h5 class="modal-title">
                    <i class="bi bi-person-plus-fill me-2 text-warning"></i>
                    Tambah Muthawif Baru
                </h5>
                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>

            <form action="{{ route('muthawif.store') }}" method="POST">
                @csrf

                <div class="modal-body p-4">

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0 small">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row">

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text"
                                   name="nama"
                                   class="form-control"
                                   value="{{ old('nama') }}"
                                   required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email"
                                   name="email"
                                   class="form-control"
                                   value="{{ old('email') }}"
                                   required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password</label>
                            <input type="password"
                                   name="password"
                                   class="form-control"
                                   required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password"
                                   name="password_confirmation"
                                   class="form-control"
                                   required>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">Kloter</label>
                            <select name="kloter_id"
                                    class="form-select"
                                    required>
                                <option value="">-- Pilih Kloter --</option>
                                @foreach(\App\Models\Kloter::all() as $kloter)
                                    <option value="{{ $kloter->id }}"
                                        {{ old('kloter_id') == $kloter->id ? 'selected' : '' }}>
                                        {{ $kloter->nama }} - {{ $kloter->tanggal }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

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


<!-- AUTO OPEN MODAL IF VALIDATION ERROR -->
@if($errors->any())
<script>
document.addEventListener("DOMContentLoaded", function() {
    var modal = new bootstrap.Modal(document.getElementById('modalTambahMuthawif'));
    modal.show();
});
</script>
@endif

@endsection
