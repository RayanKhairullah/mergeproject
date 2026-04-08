<?php

declare(strict_types=1);

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BanquetsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $banquets;

    public function __construct($banquets)
    {
        $this->banquets = $banquets;
    }

    public function collection()
    {
        return $this->banquets;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Judul Acara',
            'Tempat',
            'Tipe Tamu',
            'Tanggal Acara',
            'Estimasi Tamu',
            'Biaya',
            'Status',
            'Pembuat',
            'Disetujui Oleh',
            'Tanggal Disetujui',
            'Deskripsi',
        ];
    }

    public function map($banquet): array
    {
        return [
            $banquet->id,
            $this->cleanString($banquet->title),
            $this->cleanString($banquet->venue->name),
            $banquet->guest_type,
            $banquet->scheduled_at->format('d/m/Y H:i'),
            $banquet->estimated_guests,
            $banquet->cost ? 'Rp '.number_format((float) $banquet->cost, 0, ',', '.') : '-',
            $banquet->status->label(),
            $this->cleanString($banquet->creator->name),
            $banquet->approver ? $this->cleanString($banquet->approver->name) : '-',
            $banquet->approved_at ? $banquet->approved_at->format('d/m/Y H:i') : '-',
            $this->cleanString($banquet->description ?? '-'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    private function cleanString(?string $string): string
    {
        if ($string === null) {
            return '-';
        }

        // Remove any non-UTF-8 characters
        $string = mb_convert_encoding($string, 'UTF-8', 'UTF-8');

        // Remove any control characters
        $string = preg_replace('/[\x00-\x1F\x7F]/u', '', $string);

        // Remove HTML tags
        $string = strip_tags($string);

        return $string;
    }
}
