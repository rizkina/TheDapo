<center>
    <h1>Arsip Dokumen</h1>
</center>
{{-- <h2>Daftar Arsip Dokumen</h2> --}}
<table border="1" width="100%" style="border-collapse: collapse;">
    <thead>
        <tr>
            <th>Kategori</th>
            <th>Keterangan</th>
            <th>Pemilik</th>
            <th>Tanggal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($files as $row)
        <tr>
            <td>{{ $row->category->nama }}</td>
            <td>{{ $row->file_name }}</td>
            <td>{{ $row->user->nama }}</td>
            <td>{{ $row->created_at->format('d/m/Y') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>