@extends('layouts.app')

@section('content')
<div class="container">

  {{-- HEADER --}}
  <div class="d-flex align-items-start justify-content-between mb-3">
    <div>
      <h5 class="mb-1">Hasil tugas — {{ $task->title }}</h5>
      <div class="text-muted small">
        Nama Tourleader : <strong>{{ $submission->tourleader->name ?? '-' }}</strong><br>
        Nama Petugas : <strong>{{ $submission->nama_petugas }}</strong><br>
        Kloter : <strong>{{ $submission->kloter }}</strong>
      </div>
    </div>

    <a href="{{ route('admin.ceklis.result', $task->id) }}"
       class="btn btn-outline-secondary btn-sm">
       Kembali
    </a>
  </div>

  {{-- TABLE HASIL --}}
  <div class="card">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-bordered table-sm mb-0 align-middle">
          <thead class="table-dark">
            <tr>
              <th style="width:60px">No</th>
              <th>Nama Soal</th>
              <th style="width:200px">Status</th>
              <th>Catatan</th>
            </tr>
          </thead>
          <tbody>
            @forelse ($submission->answers as $i => $answer)
              <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $answer->question->question_text }}</td>
                <td>
                  @php
                    $label = $statusLabels[$answer->value] ?? $answer->value;
                    $badge = match($answer->value) {
                      'sudah' => 'success',
                      'tidak' => 'danger',
                      'rekan' => 'warning',
                      default => 'secondary'
                    };
                  @endphp
                  <span class="badge bg-{{ $badge }}">
                    {{ $label }}
                  </span>
                </td>
                <td>
                  {{ $answer->note ?: '-' }}
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="4" class="text-center text-muted">
                  Tidak ada data jawaban
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>
@endsection
