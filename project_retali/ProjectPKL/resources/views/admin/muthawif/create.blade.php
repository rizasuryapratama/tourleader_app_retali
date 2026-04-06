@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header" style="background-color: #2c3e50; color: white;">
                    <h4 class="mb-0">Tambah Muthawif Baru</h4>
                </div>

                <div class="card-body p-4">
                    <form action="{{ route('muthawif.store') }}" method="POST">
                        @csrf

                        {{-- Nama --}}
                        <div class="mb-3">
                            <label for="nama" class="form-label">
                                Nama <span class="text-danger">*</span>
                            </label>
                            <input type="text"
                                class="form-control @error('nama') is-invalid @enderror"
                                id="nama"
                                name="nama"
                                value="{{ old('nama') }}"
                                required
                                placeholder="Masukkan nama muthawif">
                            @error('nama')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="mb-3">
                            <label for="email" class="form-label">
                                Email <span class="text-danger">*</span>
                            </label>
                            <input type="email"
                                class="form-control @error('email') is-invalid @enderror"
                                id="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                placeholder="email@example.com">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Password --}}
                        <div class="mb-3">
                            <label for="password" class="form-label">
                                Password <span class="text-danger">*</span>
                            </label>
                            <input type="password"
                                class="form-control @error('password') is-invalid @enderror"
                                id="password"
                                name="password"
                                required
                                placeholder="Minimal 6 karakter">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Konfirmasi Password --}}
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">
                                Konfirmasi Password <span class="text-danger">*</span>
                            </label>
                            <input type="password"
                                class="form-control"
                                id="password_confirmation"
                                name="password_confirmation"
                                required
                                placeholder="Ulangi password">
                        </div>

                        {{-- Kloter --}}
                        <div class="mb-3">
                            <label for="kloter_id" class="form-label">
                                Kloter <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('kloter_id') is-invalid @enderror"
                                id="kloter_id"
                                name="kloter_id"
                                required>
                                <option value="">-- Pilih Kloter --</option>
                                @foreach($kloters as $kloter)
                                    <option value="{{ $kloter->id }}"
                                        {{ old('kloter_id') == $kloter->id ? 'selected' : '' }}>
                                        {{ $kloter->nama }}
                                        ({{ \Carbon\Carbon::parse($kloter->tgl_berangkat)->format('d M Y') }} -
                                         {{ \Carbon\Carbon::parse($kloter->tgl_pulang)->format('d M Y') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('kloter_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Button --}}
                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn text-white" style="background-color: #2c3e50;">
                                Simpan
                            </button>
                            <a href="{{ route('muthawif.index') }}" class="btn btn-secondary">
                                Batal
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
