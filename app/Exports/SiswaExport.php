<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder; // Tambahkan ini
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class SiswaExport extends DefaultValueBinder implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithCustomValueBinder
{
    protected $records;

    public function __construct($records)
    {
        $this->records = $records;
    }

    public function collection()
    {
        return $this->records;
    }

    /**
     * Memaksa kolom tertentu menjadi STRING murni (Solusi NIK & NISN)
     */
    public function bindValue(Cell $cell, $value)
    {
        // Daftar kolom yang berisi NIK/NISN/NIS (A, B, L, U, AD)
        $kolomAngkaPanjang = ['A', 'B', 'L', 'U', 'AD'];

        if (in_array($cell->getColumn(), $kolomAngkaPanjang)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    public function headings(): array
    {
        return [
            'NISN', 'NIS', 'Nama Siswa', 'L/P', 'Tempat Lahir', 'Tanggal Lahir', 'Agama ID', 'Agama', 'Tingkat', 'Kelas',
            'Nama Ibu', 'NIK Ibu', 'Tahun Lahir Ibu', 'Pendidikan Ibu ID', 'Pendidikan Ibu', 'Pekerjaan Ibu ID', 'Pekerjaan Ibu', 'Penghasilan Ibu ID', 'Penghasilan Ibu',
            'Nama Ayah', 'NIK Ayah', 'Tahun Lahir Ayah', 'Pendidikan Ayah ID', 'Pendidikan Ayah', 'Pekerjaan Ayah ID', 'Pekerjaan Ayah', 'Penghasilan Ayah ID', 'Penghasilan Ayah',
            'Nama Wali', 'NIK Wali', 'Tahun Lahir Wali', 'Pendidikan Wali ID', 'Pendidikan Wali', 'Pekerjaan Wali ID', 'Pekerjaan Wali', 'Penghasilan Wali ID', 'Penghasilan Wali',
        ];
    }

    public function map($siswa): array
    {
        return [
            $siswa->nisn,
            $siswa->nipd,
            $siswa->nama,
            $siswa->jenis_kelamin,
            $siswa->tempat_lahir,
            $siswa->tanggal_lahir?->format('d/m/Y'),
            $siswa->agama_id,
            $siswa->agama?->nama ?? $siswa->agama_id_str, // Pakai Relasi
            $siswa->tingkat_pendidikan_id,
            $siswa->nama_rombel,
            
            // Data Ibu
            $siswa->nama_ibu,
            $siswa->nik_ibu,
            $siswa->tahun_lahir_ibu,
            $siswa->pendidikan_ibu_id,
            $siswa->pendidikanIbu?->nama ?? $siswa->pendidikan_ibu_id_str,
            $siswa->pekerjaan_ibu_id,
            $siswa->pekerjaanIbu?->nama ?? $siswa->pekerjaan_ibu_id_str,
            $siswa->penghasilan_ibu_id,
            $siswa->penghasilanIbu?->nama ?? $siswa->penghasilan_ibu_id_str,

            // Data Ayah
            $siswa->nama_ayah,
            $siswa->nik_ayah,
            $siswa->tahun_lahir_ayah,
            $siswa->pendidikan_ayah_id,
            $siswa->pendidikanAyah?->nama ?? $siswa->pendidikan_ayah_id_str,
            $siswa->pekerjaan_ayah_id,
            $siswa->pekerjaanAyah?->nama ?? $siswa->pekerjaan_ayah_id_str,
            $siswa->penghasilan_ayah_id,
            $siswa->penghasilanAyah?->nama ?? $siswa->penghasilan_ayah_id_str,

            // Data Wali
            $siswa->nama_wali,
            $siswa->nik_wali,
            $siswa->tahun_lahir_wali,
            $siswa->pendidikan_wali_id,
            $siswa->pendidikanWali?->nama ?? $siswa->pendidikan_wali_id_str,
            $siswa->pekerjaan_wali_id,
            $siswa->pekerjaanWali?->nama ?? $siswa->pekerjaan_wali_id_str,
            $siswa->penghasilan_wali_id,
            $siswa->penghasilanWali?->nama ?? $siswa->penghasilan_wali_id_str,
        ];
    }
}