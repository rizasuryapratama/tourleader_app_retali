@extends('layouts.app')

@section('content')
<div class="container py-4">

  {{-- HEADER --}}
  <div class="d-flex align-items-start justify-content-between mb-3">
    <div>
      <h1 class="h4 mb-1">Hasil Tugas: {{ $task->title }}</h1>
      <div class="text-muted small">
        Periode:
        {{ $task->opens_at->format('d M Y H:i') }}
        –
        {{ $task->closes_at->format('d M Y H:i') }}
      </div>
    </div>

    <a href="{{ route('admin.tasks.index') }}"
       class="btn btn-outline-secondary">
       Kembali
    </a>
  </div>

  <div class="row g-3">

  {{-- ================= BELUM MENGERJAKAN ================= --}}
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <strong>Belum mengerjakan</strong>
      </div>

      <div class="table-responsive">
        <table class="table mb-0 align-middle">
          <thead class="table-light">
            <tr>
              <th style="width:64px">No</th>
              <th>Nama Tour Leader</th>
              <th style="width:160px">Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($notDone as $i => $tl)
              <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $tl->name }}</td>
                <td>
                  <span class="badge bg-danger">Belum dikerjakan</span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="text-center text-muted">
                  Semua sudah mengerjakan
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  {{-- ================= SUDAH MENGERJAKAN ================= --}}
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <strong>Sudah mengerjakan</strong>
      </div>

      <div class="table-responsive">
        <table class="table mb-0 align-middle">
          <thead class="table-light">
            <tr>
              <th style="width:64px">No</th>
              <th>Nama Tour Leader</th>
              <th style="width:160px">Status</th>
              <th style="width:120px">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($done as $i => $tl)
              <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $tl->name }}</td>
                <td>
                  <span class="badge bg-success">Sudah dikerjakan</span>
                </td>
                <td>
                  <a href="{{ route('admin.tasks.result.detail', [$task->id, $tl->id]) }}"
                     class="btn btn-sm btn-primary">
                     Lihat Hasil
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center text-muted">
                  Belum ada yang mengerjakan
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

</div>
@endsection
