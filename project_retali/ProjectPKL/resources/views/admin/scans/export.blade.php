<table border="1">
    <thead>
        <tr style="background:#f1f1f1;">
            <th>No</th>
            <th>Kode Koper</th>
            <th>Nama Pemilik</th>
            <th>Nomor Telepon</th>
            <th>Tour Leader</th>
            <th>Kloter</th>
            <th>Waktu Scan</th>
        </tr>
    </thead>
    <tbody>
        @foreach($scans as $i => $scan)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $scan->koper_code }}</td>

            <td>{{ $scan->owner_name ?? '-' }}</td>
            <td>{{ $scan->owner_phone ?? '-' }}</td>

            <td>{{ optional($scan->tourleader)->name ?? '-' }}</td>

            <td>{{ $scan->kloter ?? '-' }}</td>

            <td>
                {{ $scan->scanned_at
                    ? $scan->scanned_at->format('Y-m-d H:i:s')
                    : '-' }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
