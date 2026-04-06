@extends('layouts.app')

@section('content')
<div class="container py-4">

    {{-- HEADER --}}
    <div class="d-flex align-items-start justify-content-between mb-3">
        <div>
            <h1 class="h4 mb-1">Detail Hasil Tugas</h1>
            <div class="text-muted small">
                <div><strong>Tugas:</strong> {{ $task->title }}</div>
                <div><strong>Tour Leader:</strong> {{ $tourleader->name }}</div>
            </div>
        </div>
        <a href="{{ route('admin.tasks.result', $task->id) }}"
           class="btn btn-outline-secondary">
            Kembali
        </a>
    </div>

    {{-- TABLE DETAIL --}}
    <div class="card">
        <div class="card-header">
            <strong>Daftar Soal</strong>
        </div>

        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width:70px">No</th>
                        <th>Soal</th>
                        <th style="width:180px">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($questions as $i => $q)
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $q->question_text }}</td>
                            <td>
                                @if (in_array($q->id, $answeredIds))
                                    <span class="badge bg-success">
                                        Sudah
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        Belum
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">
                                Tidak ada soal pada tugas ini
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
