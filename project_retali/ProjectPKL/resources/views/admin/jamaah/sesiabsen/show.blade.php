@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h4 class="fw-bold mb-3">Detail Sesi Absen</h4>

    <div class="card mb-4">
        <div class="card-body">
            <p class="fw-semibold mb-1">Judul Sesi</p>
            <p>{{ $sesiAbsen->judul }}</p>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <p class="fw-semibold mb-2">Isi Sesi Absen</p>

            <ul class="list-group">
                @foreach ($sesiAbsen->items as $item)
                    <li class="list-group-item">
                        {{ $item->isi }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <a href="{{ route('admin.sesiabsen.index') }}" class="btn btn-light">
        Kembali
    </a>

</div>
@endsection
