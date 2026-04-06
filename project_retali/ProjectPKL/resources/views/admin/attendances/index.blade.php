{{-- resources/views/admin/attendances/index.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container py-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h1 class="h4 fw-bold mb-0">Riwayat Absensi Tour Leader</h1>
                <small class="text-muted">Data kehadiran berdasarkan lokasi & waktu</small>
            </div>
        </div>

        {{-- Alert Success --}}
        @if (session('success'))
            <div class="alert alert-success shadow-sm border-0">
                {{ session('success') }}
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-0">

                <div class="table-responsive">
                    <table class="table align-middle mb-0">

                        <thead class="bg-light">
                            <tr>
                                <th class="px-4">Tanggal</th>
                                <th>Nama</th>
                                <th>Kloter</th>
                                <th>Foto</th>
                                <th>Koordinat</th>
                                <th class="text-center">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse($rows as $r)
                                <tr class="border-top">

                                    {{-- Tanggal --}}
                                    <td class="px-4">
                                        {{ \Carbon\Carbon::parse($r->created_at)->format('Y-m-d H:i') }}
                                    </td>

                                    {{-- Nama --}}
                                    <td>
                                        {{ $r->tourleader->name ?? '-' }}
                                    </td>

                                    {{-- Kloter --}}
                                    <td>
                                        @if ($r->tourleader && $r->tourleader->kloter)
                                            <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill">
                                                {{ $r->tourleader->kloter->nama }}
                                            </span>
                                            <div class="small text-muted">
                                                {{ $r->tourleader->kloter->tanggal }}
                                            </div>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- Foto --}}
                                    <td>
                                        @if (!empty($r->photo_path))
                                            <a href="{{ asset('storage/' . $r->photo_path) }}" target="_blank">
                                                <img src="{{ asset('storage/' . $r->photo_path) }}" alt="foto"
                                                    style="height:56px;border-radius:8px;">
                                            </a>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- Koordinat --}}
                                    <td>
                                        @if (!empty($r->lat) && !empty($r->lng))
                                            <span class="text-muted">
                                                {{ $r->lat }}, {{ $r->lng }}
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>

                                    {{-- AKSI --}}
                                    <td class="text-center">
                                        <div class="d-flex flex-column align-items-center gap-2">

                                            {{-- Lihat Maps --}}
                                            @if (!empty($r->lat) && !empty($r->lng))
                                                <a class="btn btn-sm btn-light border rounded-pill px-3 shadow-sm action-map"
                                                    target="_blank"
                                                    href="https://www.google.com/maps?q={{ $r->lat }},{{ $r->lng }}">
                                                    <i class="bi bi-geo-alt-fill text-primary me-1"></i>
                                                    Lihat Maps
                                                </a>
                                            @endif

                                            {{-- Hapus --}}
                                            <form action="{{ route('admin.attendances.destroy', $r->id) }}" method="POST"
                                                onsubmit="return confirm('Yakin mau hapus absensi ini?')">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                    class="btn btn-sm btn-light border rounded-pill px-3 shadow-sm action-delete">
                                                    <i class="bi bi-trash-fill text-danger me-1"></i>
                                                    Hapus
                                                </button>
                                            </form>

                                        </div>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">
                                        Belum ada data.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>

                    </table>
                </div>

            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-3">
            {{ $rows->links() }}
        </div>

    </div>


    {{-- Hover Effect Style --}}
    <style>
        .action-map:hover {
            background-color: #e9f2ff;
            border-color: #0d6efd;
            transform: translateY(-1px);
            transition: 0.2s ease;
        }

        .action-delete:hover {
            background-color: #ffeaea;
            border-color: #dc3545;
            transform: translateY(-1px);
            transition: 0.2s ease;
        }
    </style>
@endsection
