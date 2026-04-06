@extends('layouts.app')

@section('content')
    <div class="container-fluid py-4">

        <!-- Page Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-0">Daftar Tour Leader</h2>
                <small class="text-muted">Kelola data Tour Leader & penempatan kloter</small>
            </div>

            <button class="btn btn-primary px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#tambahLeaderModal">
                <i class="bi bi-plus-lg me-1"></i> Tambah
            </button>
        </div>

        <!-- Alert -->
        @if (session('success'))
            <div class="alert alert-success shadow-sm border-0">
                {{ session('success') }}
            </div>
        @endif

        <!-- Card Table -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">

                <div class="table-responsive">
                    <table class="table align-middle mb-0">

                        <thead class="bg-light">
                            <tr>
                                <th class="px-4 py-3">Nama</th>
                                <th>Email</th>
                                <th>Kloter</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($tourleaders as $leader)
                                <tr class="border-top">
                                    <td class="px-4">
                                        <div class="fw-semibold">{{ $leader->name }}</div>
                                    </td>

                                    <td>{{ $leader->email }}</td>

                                    <td>
                                        @if ($leader->kloter)
                                            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
                                                {{ $leader->kloter->nama }}
                                            </span>
                                            <div class="small text-muted">
                                                {{ $leader->kloter->tanggal }}
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    <td class="text-center">

                                        <div class="d-flex justify-content-center gap-2">

                                            <!-- Edit Button -->
                                            <a href="{{ route('tourleaders.edit', $leader->id) }}"
                                                class="btn btn-sm btn-light border rounded-pill px-3 shadow-sm action-edit">
                                                <i class="bi bi-pencil-fill text-warning me-1"></i>
                                                <span class="fw-medium">Edit</span>
                                            </a>

                                            <!-- Delete Button -->
                                            <form action="{{ route('tourleaders.destroy', $leader->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin mau hapus?')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                    class="btn btn-sm btn-light border rounded-pill px-3 shadow-sm action-delete">
                                                    <i class="bi bi-trash-fill text-danger me-1"></i>
                                                    <span class="fw-medium">Hapus</span>
                                                </button>
                                            </form>

                                        </div>

                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">
                                        Belum ada data
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
    <div class="modal fade" id="tambahLeaderModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">

                <div class="modal-header text-white border-0"
                    style="background: linear-gradient(135deg, #1f3c88, #162c5c); border-radius: 1rem 1rem 0 0;">
                    <h5 class="modal-title fw-semibold">
                        <i class="bi bi-person-plus-fill me-2 text-warning"></i>
                        Tambah Tour Leader
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <form method="POST" action="{{ route('tourleaders.store') }}">
                    @csrf

                    <div class="modal-body p-4">

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0 small">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <!-- Nama -->
                        <div class="form-floating mb-3">
                            <input type="text" name="name" class="form-control" placeholder="Nama"
                                value="{{ old('name') }}" required>
                            <label>Nama Lengkap</label>
                        </div>

                        <!-- Email -->
                        <div class="form-floating mb-3">
                            <input type="email" name="email" class="form-control" placeholder="Email"
                                value="{{ old('email') }}" required>
                            <label>Email</label>
                        </div>

                        <!-- Password -->
                        <div class="form-floating mb-3">
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                            <label>Password</label>
                        </div>

                        <!-- Kloter -->
                        <div class="form-floating mb-3">
                            <select name="kloter_id" class="form-select" required>
                                <option value="">-- Pilih Kloter --</option>
                                @foreach ($kloters as $kloter)
                                    <option value="{{ $kloter->id }}"
                                        {{ old('kloter_id') == $kloter->id ? 'selected' : '' }}>
                                        {{ $kloter->nama }} ({{ $kloter->tanggal }})
                                    </option>
                                @endforeach
                            </select>
                            <label>Kloter</label>
                        </div>

                    </div>

                    <div class="modal-footer border-0 px-4 pb-4">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                            Batal
                        </button>

                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                            Simpan
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>


    <!-- Auto open modal if validation error -->
    @if ($errors->any())
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var myModal = new bootstrap.Modal(document.getElementById('tambahLeaderModal'));
                myModal.show();
            });
        </script>
    @endif

@endsection
