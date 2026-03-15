<!DOCTYPE html>
<html>
<head>
    <title>Data Siswa</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .header { text-align: center; }
    </style>
</head>
<body>
    <div class="header">
        <h2>DAFTAR PESERTA DIDIK</h2>
        <p>Tanggal Cetak: {{ now()->format('d/m/Y H:i') }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>NISN</th>
                <th>Nama</th>
                <th>L/P</th>
                <th>Kelas</th>
            </tr>
        </thead>
        <!-- Bagian isi tabel di siswa.blade.php -->
        <tbody>
            @if(isset($siswas) && count($siswas) > 0)
                @foreach($siswas as $index => $row)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $row->nisn }}</td>
                    <td>{{ $row->nama }}</td>
                    <td>{{ $row->jenis_kelamin }}</td>
                    <td>{{ $row->nama_rombel }}</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="5">Data tidak ditemukan.</td>
                </tr>
            @endif
        </tbody>
    </table>
</body>
</html>