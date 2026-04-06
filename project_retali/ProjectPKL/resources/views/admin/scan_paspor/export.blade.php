<table>
    <thead>
        <tr>
            <th>No</th>
            <th>No Paspor</th>
            <th>Nama Pemilik</th>
            <th>Telepon</th>
            <th>Kloter</th>
            <th>Tour Leader</th>
            <th>Waktu Scan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($scans as $i => $scan)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $scan->passport_number }}</td>
                <td>{{ $scan->owner_name ?? '-' }}</td>
                <td>{{ $scan->owner_phone ?? '-' }}</td>
                <td>{{ $scan->kloter ?? '-' }}</td>
                <td>{{ $scan->tourleader->name ?? '-' }}</td>
                <td>{{ $scan->scanned_at->format('d-m-Y H:i') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
