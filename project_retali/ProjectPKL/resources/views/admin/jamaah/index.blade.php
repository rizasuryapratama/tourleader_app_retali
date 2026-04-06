@extends('layouts.app')

@section('title', 'Data Absen Jamaah')

@section('content')
<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold">Data Absen Jamaah</h1>

        <div class="d-flex gap-2">
            <a href="{{ route('admin.sesiabsen.index') }}"
               class="btn btn-secondary">
                Sesi Absen
            </a>

            <a href="{{ route('jamaah.importForm') }}"
               class="btn btn-primary">
                + Tambah Absen
            </a>
        </div>
    </div>

    {{-- FLASH --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    {{-- LIST --}}
    @forelse($absen as $item)
        <div class="card mb-3 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-start">

                <div>
                   <h5 class="fw-bold mb-1">
                      {{ $item->kloter->nama }}
                    <span class="text-muted fw-normal">
                      ({{ $item->kloter->tanggal_label }})
                    </span>
                   </h5>





                    {{-- SESI --}}
                    <div class="text-muted small">
                        Sesi:
                        <strong>
                            {{ $item->sesiAbsen->judul }}
                            –
                            {{ $item->sesiAbsenItem->isi }}
                        </strong>
                    </div>

                    {{-- TOUR LEADER --}}
                    <div class="text-muted small">
                        Tour Leader:
                        <strong>
                            {{
                                $item->jamaah
                                    ->pluck('tourleader.name')
                                    ->filter()
                                    ->unique()
                                    ->implode(', ')
                            }}
                        </strong>
                    </div>

                    {{-- TANGGAL --}}
                    <div class="text-muted small">
                        Dibuat:
                        {{ $item->created_at->format('d M Y, H:i') }}
                    </div>
                </div>

                {{-- AKSI --}}
                <div class="d-flex gap-2">
                    <a href="{{ route('jamaah.detail', $item->id) }}"
                       class="btn btn-success btn-sm">
                        Detail
                    </a>

                    <form action="{{ route('jamaah.destroy', $item->id) }}"
                          method="POST"
                          onsubmit="return confirm('Hapus sesi absen ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="text-muted">
            Belum ada data sesi absen.
        </div>
    @endforelse

</div>
@endsection
