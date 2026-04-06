@extends('layouts.app')

@section('content')
    <div class="content-header">
        <h1 class="page-title">Riwayat Absensi Muthawif</h1>
        <p class="text-muted">Data kehadiran berdasarkan lokasi & waktu</p>
    </div>

    <div class="card">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Nama</th>
                            <th>Kloter</th>
                            <th>Foto</th>
                            <th>Koordinat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($attendances as $item)
                            <tr>
                                <td>
                                    {{ $item->created_at->format('Y-m-d H:i') }}
                                </td>

                                <td>
                                    {{ $item->muthawif->nama ?? '-' }}
                                </td>

                                <td>
                                    @if ($item->muthawif && $item->muthawif->kloter)
                                        <span class="badge bg-primary">
                                            {{ $item->muthawif->kloter->nama }}
                                        </span>
                                        <br>
                                        <small class="text-muted">
                                            {{ $item->muthawif->kloter->tanggal }}
                                        </small>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    @if ($item->foto)
                                        <a href="{{ asset('storage/' . $item->foto) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $item->foto) }}" width="60"
                                                class="rounded shadow-sm">
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>

                                <td>
                                    {{ $item->latitude }},
                                    {{ $item->longitude }}
                                </td>

                                <td>
                                    @if ($item->latitude && $item->longitude)
                                        <a href="https://www.google.com/maps?q={{ $item->latitude }},{{ $item->longitude }}"
                                            target="_blank" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-map-marker-alt"></i>
                                            Lihat Maps
                                        </a>
                                    @endif

                                    <form action="{{ route('admin.absensi.muthawif.destroy', $item->id) }}" method="POST"
                                        style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Hapus data ini?')">
                                            <i class="fas fa-trash"></i>
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">
                                    Belum ada data absensi
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
@endsection
