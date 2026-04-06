@extends('layouts.app')

@section('content')
<div class="container-fluid px-4">

    {{-- Alert Success --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
        <h1 class="h3 text-dark fw-bold">Daftar Notifikasi</h1>

        <!-- Button Modal Trigger -->
        <button class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#notifModal">
            <i class="fas fa-plus"></i> Buat Notifikasi Baru
        </button>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">

            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th width="5%">No</th>
                        <th width="20%">Judul</th>
                        <th width="30%">Pesan</th>
                        <th width="10%">Aktif</th>
                        <th width="20%">Tanggal</th>
                        <th width="15%">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($notifications as $notif)
                        <tr>
                            <td>{{ $loop->iteration }}</td>

                            <td>{{ $notif->title }}</td>

                            <td>{{ $notif->message }}</td>

                            <td>
                                @if($notif->is_active)
                                    <span class="badge bg-success">Ya</span>
                                @else
                                    <span class="badge bg-danger">Tidak</span>
                                @endif
                            </td>

                            <td>
                                {{ $notif->created_at->format('d M Y H:i') }}
                            </td>

                            <td>
                                <form action="{{ route('admin.notifications.destroy', $notif->id) }}"
                                      method="POST"
                                      onsubmit="return confirm('Yakin mau hapus notifikasi ini?')"
                                      style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6"
                                class="text-center text-muted">
                                Belum ada notifikasi
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>
    </div>
</div>


<!-- ============================= -->
<!-- MODAL BUAT NOTIFIKASI -->
<!-- ============================= -->

<div class="modal fade" id="notifModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title">
            <i class="bi bi-bell-fill me-2"></i>
            Buat Notifikasi
        </h5>
        <button type="button"
                class="btn-close btn-close-white"
                data-bs-dismiss="modal"></button>
      </div>

      <form action="{{ route('admin.notifications.send') }}"
            method="POST">
        @csrf

        <div class="modal-body">

            <div class="mb-3">
                <label class="form-label fw-bold">
                    Judul Notifikasi
                </label>
                <input type="text"
                       name="title"
                       class="form-control"
                       placeholder="Masukkan judul..."
                       required>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">
                    Isi Notifikasi
                </label>
                <textarea name="message"
                          class="form-control"
                          rows="4"
                          placeholder="Tulis isi notifikasi..."
                          required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">
                    FCM Token (Opsional)
                </label>
                <input type="text"
                       name="fcm_token"
                       class="form-control"
                       placeholder="Kosongkan jika ingin broadcast ke semua">
            </div>

        </div>

        <div class="modal-footer">
            <button type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">
                Batal
            </button>

            <button type="submit"
                    class="btn btn-primary">
                <i class="bi bi-send-fill me-2"></i>
                Kirim Notifikasi
            </button>
        </div>
      </form>

    </div>
  </div>
</div>

<link rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">

@endsection
