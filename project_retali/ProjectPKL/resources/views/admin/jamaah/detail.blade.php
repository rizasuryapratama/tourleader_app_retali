@extends('layouts.app')

@section('title', 'Detail Absen')

@section('content')
<div class="container-fluid">

   <div class="card mb-4">
    <div class="card-body d-flex justify-content-between align-items-start">
        <div>
            <h4 class="fw-bold mb-1">
              {{ $absen->kloter->nama }}
                 <span class="text-muted fw-normal">
                    ({{ $absen->kloter->tanggal_label }})
                 </span>
            </h4>


            <div class="text-muted">
                Tourleader :
                {{ $tourleaders->pluck('tourleader.name')->filter()->implode(', ') }}
            </div>

            <div class="text-muted">
                Sesi absen :
                {{ $absen->sesiAbsen->judul }} –
                {{ $absen->sesiAbsenItem->isi }}
            </div>
        </div>

        <a href="{{ route('jamaah.index') }}" class="btn btn-light shadow-sm">
            Kembali
        </a>
    </div>
</div>


    {{-- TABLE --}}
    <div class="card">
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th width="60">No</th>
                        <th>Nama Tourleader</th>
                        <th>Status</th>
                        <th width="140"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tourleaders as $i => $row)
                        <tr>
                            <td>{{ $i + 1 }}</td>

                            <td>{{ $row['tourleader']->name }}</td>

                            {{-- STATUS --}}
                            <td>
                                @if($row['done'])
                                    <span class="text-success fw-semibold">
                                        Sudah dikerjakan
                                    </span>
                                @else
                                    <span class="text-muted">
                                        Belum dikerjakan
                                    </span>
                                @endif
                            </td>

                            {{-- AKSI --}}
                            <td>
                                <a href="{{ route('jamaah.detailTourleader', [
                                        $absen->id,
                                        $row['tourleader']->id
                                    ]) }}"
                                   class="btn btn-sm btn-outline-primary">
                                    Lihat absen
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
