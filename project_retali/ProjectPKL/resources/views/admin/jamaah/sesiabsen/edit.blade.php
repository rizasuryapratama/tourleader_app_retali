@extends('layouts.app')

@section('content')
<div class="container-fluid">

    <h4 class="fw-bold mb-4">Edit Sesi Absen</h4>

    <form action="{{ route('admin.sesiabsen.update', $sesiAbsen->id) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- JUDUL -->
        <div class="card mb-4">
            <div class="card-body">
                <label class="fw-semibold mb-2">Judul Sesi</label>
                <input type="text"
                       name="judul"
                       class="form-control"
                       value="{{ $sesiAbsen->judul }}"
                       required>
            </div>
        </div>

        <!-- ITEMS -->
        <div class="card mb-4">
            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="fw-semibold">Isi Sesi Absen</span>

                    <button type="button"
                            class="btn btn-primary btn-sm"
                            id="addItem">
                        <i class="bi bi-plus-circle me-1"></i>
                        Tambah Isi
                    </button>
                </div>

                <div id="itemWrapper">

                    @foreach($sesiAbsen->items as $index => $item)
                        <div class="input-group mb-3 item-row">

                            <input type="hidden"
                                   name="items[{{ $index }}][id]"
                                   value="{{ $item->id }}">

                            <input type="text"
                                   name="items[{{ $index }}][isi]"
                                   class="form-control"
                                   value="{{ $item->isi }}"
                                   required>

                            <button type="button"
                                    class="btn btn-danger removeItem">
                                <i class="bi bi-trash"></i>
                            </button>

                        </div>
                    @endforeach

                </div>

            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('admin.sesiabsen.index') }}"
               class="btn btn-light">
                Kembali
            </a>

            <button type="submit"
                    class="btn btn-success">
                Simpan Perubahan
            </button>
        </div>
    </form>
</div>

<script>
let index = {{ $sesiAbsen->items->count() }};

document.getElementById('addItem').addEventListener('click', function () {

    let wrapper = document.getElementById('itemWrapper');

    let newRow = document.createElement('div');
    newRow.classList.add('input-group','mb-3','item-row');

    newRow.innerHTML = `
        <input type="text"
               name="items[${index}][isi]"
               class="form-control"
               placeholder="Isi baru..."
               required>

        <button type="button"
                class="btn btn-danger removeItem">
            <i class="bi bi-trash"></i>
        </button>
    `;

    wrapper.appendChild(newRow);
    index++;
});


document.addEventListener('click', function(e){
    if(e.target.closest('.removeItem')){
        e.target.closest('.item-row').remove();
    }
});
</script>

@endsection
