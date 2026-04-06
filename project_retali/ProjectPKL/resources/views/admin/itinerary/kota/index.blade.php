@extends('layouts.app')

@section('content')
    <div class="container py-4">

        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-bold">
                <i class="bi bi-building text-primary me-2"></i>
                Pilihan Kota
            </h4>

            <button class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal"
                data-bs-target="#modalTambahKota">
                <i class="bi bi-plus-lg me-1"></i>
                Tambah Kota
            </button>
        </div>

        @if (session('ok'))
            <div class="alert alert-success shadow-sm">
                {{ session('ok') }}
            </div>
        @endif

        <!-- TABLE -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">

                <div class="table-responsive">
                    <table class="table align-middle mb-0">

                        <thead style="background: linear-gradient(135deg,#1f3c88,#162c5c); color:white;">
                            <tr>
                                <th class="px-4 py-3">Nama Kota</th>
                                <th width="200" class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($cities as $city)
                                <tr>
                                    <td class="px-4 py-3 fw-semibold">
                                        {{ $city->name }}
                                    </td>

                                    <td class="px-4 py-3 text-center">

                                        <div class="d-flex justify-content-center gap-3">

                                            <!-- EDIT -->
                                            <button class="btn btn-edit-action" data-bs-toggle="modal"
                                                data-bs-target="#modalEditKota{{ $city->id }}">
                                                <i class="bi bi-pencil-square me-2"></i>
                                                Edit
                                            </button>

                                            <!-- DELETE -->
                                            <form action="{{ route('admin.itinerary.kota.destroy', $city->id) }}"
                                                method="POST" onsubmit="return confirm('Hapus kota ini?')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-delete-action">
                                                    <i class="bi bi-trash3 me-2"></i>
                                                    Hapus
                                                </button>
                                            </form>

                                        </div>

                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center py-4 text-muted">
                                        Belum ada data kota
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

                <div class="p-3">
                    {{ $cities->links() }}
                </div>

            </div>
        </div>

    </div>


    <!-- ================= MODAL TAMBAH ================= -->
    <div class="modal fade" id="modalTambahKota" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content rounded-4 border-0 shadow-lg">

                <div class="modal-header text-white border-0" style="background: linear-gradient(135deg,#1f3c88,#162c5c);">
                    <h5 class="modal-title">
                        <i class="bi bi-geo-alt-fill text-warning me-2"></i>
                        Tambah Kota
                    </h5>

                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form action="{{ route('admin.itinerary.kota.store') }}" method="POST">
                    @csrf

                    <div class="modal-body p-4">

                        <div id="inputWrapper">
                            <div class="input-group mb-3">
                                <input type="text" name="names[]" class="form-control"
                                    placeholder="Masukkan nama kota..." required>

                                <button type="button" class="btn btn-danger removeInput d-none">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>

                        <button type="button" id="addInput" class="btn btn-outline-primary btn-sm rounded-pill">
                            <i class="bi bi-plus-circle me-1"></i>
                            Tambah Isi
                        </button>

                    </div>

                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                            Batal
                        </button>

                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                            Simpan Semua
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>


    <!-- ================= MODAL EDIT (OUTSIDE TABLE) ================= -->
    @foreach ($cities as $city)
        <div class="modal fade" id="modalEditKota{{ $city->id }}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg">

                    <div class="modal-header text-white border-0"
                        style="background: linear-gradient(135deg,#1f3c88,#162c5c);">
                        <h5 class="modal-title">
                            <i class="bi bi-pencil-square text-warning me-2"></i>
                            Edit Kota
                        </h5>

                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <form action="{{ route('admin.itinerary.kota.update', $city->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="modal-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nama Kota</label>
                                <input type="text" name="name" class="form-control" value="{{ $city->name }}"
                                    required>
                            </div>
                        </div>

                        <div class="modal-footer border-0 px-4 pb-4">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                                Batal
                            </button>

                            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                Update
                            </button>
                        </div>

                    </form>

                </div>
            </div>
        </div>
    @endforeach


    <!-- MULTIPLE INPUT SCRIPT -->
    <script>
        document.getElementById('addInput').addEventListener('click', function() {
            let wrapper = document.getElementById('inputWrapper');

            let newInput = document.createElement('div');
            newInput.classList.add('input-group', 'mb-3');

            newInput.innerHTML = `
        <input type="text"
               name="names[]"
               class="form-control"
               placeholder="Masukkan nama kota..."
               required>

        <button type="button"
                class="btn btn-danger removeInput">
            <i class="bi bi-x-lg"></i>
        </button>
    `;

            wrapper.appendChild(newInput);
        });

        document.addEventListener('click', function(e) {
            if (e.target.closest('.removeInput')) {
                e.target.closest('.input-group').remove();
            }
        });
    </script>

    <style>
.btn-edit-action {
    background: #eef5ff;
    color: #1f3c88;
    border: none;
    padding: 8px 18px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    transition: 0.2s ease;
}

.btn-edit-action:hover {
    background: #1f3c88;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(31,60,136,0.3);
}

.btn-delete-action {
    background: #fff1f1;
    color: #dc3545;
    border: none;
    padding: 8px 18px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    transition: 0.2s ease;
}

.btn-delete-action:hover {
    background: #dc3545;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(220,53,69,0.3);
}
</style>

@endsection
