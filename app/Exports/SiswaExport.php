<?php

namespace App\Exports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SiswaExport implements FromCollection, WithHeadings, WithMapping
{
    protected $records;

    // Kita terima data dari Filament (bisa hasil filter atau pilihan centang)
    public function __construct($records)
    {
        $this->records = $records;
    }

    public function collection()
    {
        return $this->records;
    }

    // Header Excel
    public function headings(): array
    {
        return ['NISN', 'NIS', 'Nama Siswa', 'L/P', 'Tempat Lahir', 'Tanggal Lahir', 'Kelas'];
    }

    // Mapping data ke kolom
    public function map($siswa): array
    {
        return [
            $siswa->nisn,
            $siswa->nipd,
            $siswa->nama,
            $siswa->jenis_kelamin,
            $siswa->tempat_lahir,
            $siswa->tanggal_lahir?->format('d/m/Y'),
            $siswa->nama_rombel,
        ];
    }
}