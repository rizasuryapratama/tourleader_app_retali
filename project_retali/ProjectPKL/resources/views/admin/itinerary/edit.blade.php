@extends('layouts.app')

@section('content')
<div class="container" style="max-width:1000px">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Edit Itinerary & Semua Harinya</h3>
        <a href="{{ route('admin.itinerary.index') }}" class="btn btn-light">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    {{-- SUCCESS --}}
    @if(session('ok'))
        <div class="alert alert-success">{{ session('ok') }}</div>
    @endif

    {{-- ERROR --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORM EDIT ITINERARY --}}
    <form action="{{ route('admin.itinerary.update', $itinerary) }}"
          method="POST" class="card p-4 mb-4">
        @csrf
        @method('PUT')

        <h5 class="mb-3">Informasi Utama Itinerary</h5>

        {{-- JUDUL --}}
        <div class="mb-3">
            <label class="form-label">Judul</label>
            <input type="text" class="form-control"
                   name="title"
                   value="{{ old('title', $itinerary->title) }}"
                   required>
        </div>

        {{-- TANGGAL --}}
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label">Tanggal Mulai</label>
                <input type="date" name="start_date" class="form-control"
                    value="{{ old('start_date', optional($itinerary->start_date)->toDateString()) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label">Tanggal Selesai</label>
                <input type="date" name="end_date" class="form-control"
                    value="{{ old('end_date', optional($itinerary->end_date)->toDateString()) }}">
            </div>
        </div>

        {{-- TOUR LEADER --}}
        {{-- TOUR LEADER (CHECKBOX) --}}
<div class="mb-4">
    <label class="form-label fw-bold">Tour Leader</label>

    <div class="row">
        @foreach($tourLeaders as $tl)
            <div class="col-md-6">
                <div class="form-check">
                    <input class="form-check-input"
                           type="checkbox"
                           name="tourleaders[]"
                           value="{{ $tl->id }}"
                           id="tl_{{ $tl->id }}"
                           @checked(
                               in_array(
                                   $tl->id,
                                   old(
                                       'tourleaders',
                                       $itinerary->tourLeaders->pluck('id')->toArray()
                                   )
                               )
                           )>

                    <label class="form-check-label" for="tl_{{ $tl->id }}">
                        {{ $tl->name }}
                    </label>
                </div>
            </div>
        @endforeach
    </div>

    <small class="text-muted">
        Centang satu atau lebih Tour Leader yang bertugas.
    </small>
</div>


        <button class="btn btn-primary w-100">
            <i class="fas fa-save"></i> Simpan Perubahan Itinerary
        </button>
    </form>

    {{-- SECTION DAY & ITEM (TIDAK DIUBAH) --}}
    <h4 class="mb-3">Edit Days & Kegiatan</h4>

    @foreach($itinerary->days as $day)
        @php
            $readDate = $day->date ? \Carbon\Carbon::parse($day->date)->format('Y-m-d') : '';
        @endphp

        <div class="card mb-4">
            <div class="card-header bg-light">
                <strong>Day {{ $day->day_number }}</strong>
            </div>

            <div class="card-body">

                {{-- UPDATE DAY --}}
                <form action="{{ route('admin.day.update', $day) }}" method="POST" class="mb-3">
                    @csrf @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-4">
    <label class="form-label">Kota</label>

    <select name="city" class="form-select">
        <option value="">-- Pilih Kota --</option>

        @foreach($cities as $city)
            <option value="{{ $city->name }}"
                @selected(old('city', $day->city) === $city->name)>
                {{ $city->name }}
            </option>
        @endforeach
    </select>
</div>

                        <div class="col-md-4">
                            <label class="form-label">Tanggal</label>
                            <input type="date" name="date" class="form-control"
                                   value="{{ $readDate }}">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button class="btn btn-primary w-100">Update Day</button>
                        </div>
                    </div>
                </form>

                <hr>

                {{-- ITEMS --}}
                <h6>Kegiatan</h6>

                @forelse($day->items as $item)
                    <div class="border rounded p-3 mb-2">

                        <form action="{{ route('admin.day.item.update', $item) }}" method="POST">
                            @csrf @method('PUT')

                            <div class="row g-3">
                                <div class="col-md-2">
                                    <label class="form-label">Jam</label>
                                    <input type="time" name="time" class="form-control"
                                           value="{{ substr($item->time,0,5) }}">
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Judul</label>
                                    <input type="text" name="title" class="form-control"
                                           value="{{ $item->title }}">
                                </div>

                                <div class="col-md-5">
                                    <label class="form-label">Isi</label>
                                    <textarea name="content" class="form-control" rows="2">{{ $item->content }}</textarea>
                                </div>

                                <div class="col-md-2 d-flex align-items-end">
                                    <button class="btn btn-success w-100 btn-sm">Simpan</button>
                                </div>
                            </div>
                        </form>

                        <form action="{{ route('admin.day.item.destroy', $item) }}"
                              method="POST" class="mt-1"
                              onsubmit="return confirm('Hapus kegiatan ini?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-danger btn-sm w-100">Hapus</button>
                        </form>

                    </div>
                @empty
                    <p class="text-muted fst-italic">Belum ada kegiatan.</p>
                @endforelse

            </div>
        </div>
    @endforeach

</div>
@endsection
