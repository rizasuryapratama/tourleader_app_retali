@extends('layouts.app')

@section('title', 'Detail Absen Jamaah')

@section('content')
<div class="container-fluid">

    {{-- ================= HEADER ================= --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1">
                {{ $absen->kloter->nama }}
                <span class="text-muted fw-normal">
                    ({{ $absen->kloter->tanggal_label }})
                </span>
            </h4>

            <div class="text-muted">
                Tour Leader:
                <strong>{{ $tourleader->name }}</strong>
            </div>

            <div class="text-muted">
                Sesi Absen:
                {{ $absen->sesiAbsen->judul }} –
                {{ $absen->sesiAbsenItem->isi }}
            </div>

            
            <div class="text-muted">
                Jumlah Jamaah:
                <strong>{{ $jamaah->total() }}</strong>
            </div>
        </div>

        <a href="{{ route('jamaah.detail', $absen->id) }}"
           class="btn btn-secondary">
            &laquo; Kembali
        </a>
    </div>

    {{-- ================= TABLE ================= --}}
    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Nama Jamaah</th>
                        <th>No. Paspor</th>
                        <th>No HP</th>
                        <th>JK</th>
                        <th>Tgl Lahir</th>
                        <th>Kloter</th>
                        <th>Bus</th>
                        <th>Keterangan</th>
                        <th>Status Hadir</th>
                        <th>Catatan</th>
                    </tr>
                </thead>

                <tbody>
                @forelse($jamaah as $i => $j)
                    @php
                        // ✅ PAKAI RELASI YANG BENAR
                        $attendance = $j->latestAttendance;
                        $status = $attendance->status ?? 'BELUM_ABSEN';
                    @endphp

                    <tr>
                        {{-- nomor urut paging --}}
                        <td>{{ $j->urutan_absen }}</td>

                        <td>{{ $j->nama_jamaah }}</td>
                        <td>{{ $j->no_paspor ?? '-' }}</td>
                        <td>{{ $j->no_hp ?? '-' }}</td>
                        <td>{{ $j->jenis_kelamin ?? '-' }}</td>
                        <td>
                            {{ $j->tanggal_lahir
                                ? $j->tanggal_lahir->format('d-m-Y')
                                : '-' }}
                        </td>
                        <td>{{ $j->kode_kloter ?? '-' }}</td>
                        <td>{{ $j->nomor_bus ?? '-' }}</td>
                        <td>{{ $j->keterangan ?? '-' }}</td>

                        {{-- STATUS --}}
                        <td>
                            <span class="badge
                                {{ $status === 'HADIR' ? 'bg-success' :
                                   ($status === 'TIDAK_HADIR' ? 'bg-danger' : 'bg-secondary') }}">
                                {{ str_replace('_',' ', $status) }}
                            </span>
                        </td>

                        {{-- CATATAN --}}
                        <td>
                            @if($attendance && $attendance->catatan)
                                <button class="btn btn-sm btn-outline-primary"
                                    data-bs-toggle="modal"
                                    data-bs-target="#catatanModal{{ $j->id }}">
                                    Lihat Catatan
                                </button>
                            @else
                                <span class="text-muted fst-italic">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" class="text-center text-muted">
                            Tidak ada data jamaah
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-3">
            {{ $jamaah->links() }}
        </div>
    </div>

    {{-- ================= MODAL CATATAN ================= --}}
    @foreach($jamaah as $j)
        @php
            $attendance = $j->latestAttendance;
            $status = $attendance->status ?? 'BELUM_ABSEN';
        @endphp

        @if($attendance && $attendance->catatan)
        <div class="modal fade"
             id="catatanModal{{ $j->id }}"
             tabindex="-1"
             aria-hidden="true">

            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    {{-- HEADER --}}
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">
                            Catatan Jamaah
                        </h5>
                        <button type="button"
                                class="btn-close"
                                data-bs-dismiss="modal"></button>
                    </div>

                    {{-- BODY --}}
                    <div class="modal-body">
                        <div class="mb-2">
                            <strong>Nama Jamaah:</strong><br>
                            {{ $j->nama_jamaah }}
                        </div>

                        <div class="mb-2">
                            <strong>Status:</strong><br>
                            <span class="badge
                                {{ $status === 'HADIR' ? 'bg-success' :
                                   ($status === 'TIDAK_HADIR' ? 'bg-danger' : 'bg-secondary') }}">
                                {{ str_replace('_',' ', $status) }}
                            </span>
                        </div>

                        <div class="mb-2">
                            <strong>Catatan:</strong>
                            <div class="border rounded p-2 bg-light mt-1">
                                {{ $attendance->catatan }}
                            </div>
                        </div>
                    </div>

                    {{-- FOOTER --}}
                    <div class="modal-footer">
                        <button class="btn btn-secondary"
                                data-bs-dismiss="modal">
                            Tutup
                        </button>
                    </div>

                </div>
            </div>
        </div>
        @endif
    @endforeach

</div>
@endsection
