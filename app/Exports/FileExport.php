<?php

namespace App\Exports;

use App\Models\File;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\Storage;
use App\Services\GoogleDriveService;

class FileExport implements FromCollection, WithHeadings, WithMapping, WithEvents, ShouldAutoSize
{
    protected $records;

    public function __construct($records)
    {
        $this->records = $records;
        // Panggil konfigurasi Google Drive sekali di awal
        GoogleDriveService::applyConfig();
    }

    public function collection()
    {
        return $this->records;
    }

    public function headings(): array
    {
        return [
            'Nama Pemilik',
            'Role',
            'Kategori',
            'Keterangan File',
            'Nama File Asli',
            'Ukuran (KB)',
            'Tanggal Unggah',
            'Link Google Drive', // Kolom H
        ];
    }

    public function map($file): array
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('google');

        try {
            // Mendapatkan URL dari driver
            $url = $disk->url($file->file_path);

            /**
             * Jika driver mengembalikan link download (uc?export=download), 
             * kita ubah secara manual menjadi link preview agar tidak otomatis terunduh saat diklik.
             */
            if (str_contains($url, 'export=download')) {
                $url = str_replace('uc?export=download&id=', 'file/d/', $url);
                $url = explode('&', $url)[0] . '/view';
            }
        } catch (\Exception $e) {
            $url = 'Link tidak tersedia';
        }

        return [
            $file->user->nama ?? '-',
            $file->user->getRoleNames()->first() ?? '-',
            $file->category->nama ?? '-',
            $file->file_name,
            $file->original_name,
            number_format($file->size / 1024, 2),
            $file->created_at->format('d/m/Y H:i'),
            $url, 
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                // Memproses kolom H untuk dijadikan Hyperlink yang bisa diklik
                for ($row = 2; $row <= $highestRow; $row++) {
                    $cell = 'H' . $row;
                    $url = $sheet->getCell($cell)->getValue();

                    if (filter_var($url, FILTER_VALIDATE_URL)) {
                        // Set sebagai Hyperlink aktif
                        $sheet->getCell($cell)->getHyperlink()->setUrl($url);

                        // Berikan gaya visual link (Biru & Garis Bawah)
                        $sheet->getStyle($cell)->applyFromArray([
                            'font' => [
                                'color' => ['rgb' => '0000FF'],
                                'underline' => 'single',
                            ],
                        ]);
                    }
                }
            },
        ];
    }
}