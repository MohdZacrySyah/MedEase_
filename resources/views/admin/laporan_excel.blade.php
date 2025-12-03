<table>
    <thead>
        <tr>
            <th>ID Pasien</th>
            <th>Nama Pasien</th>
            <th>Nama Dokter</th>
            <th>Layanan</th>
            <th>Tanggal Kunjungan</th>
            <th>Alamat</th>
            <th>Tanggal Lahir</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($kunjunganData as $data)
            <tr>
                <td>{{ $data->pasien_id }}</td>
                <td>{{ $data->nama_pasien }}</td>
                <td>{{ $data->nama_dokter ?? 'N/A' }}</td>
                <td>{{ $data->layanan }}</td>
                <td>{{ \Carbon\Carbon::parse($data->tanggal_kunjungan)->format('Y-m-d H:i:s') }}</td>
                <td>{{ $data->alamat }}</td>
                <td>{{ $data->tanggal_lahir }}</td>
            </tr>
        @endforeach
    </tbody>
</table>