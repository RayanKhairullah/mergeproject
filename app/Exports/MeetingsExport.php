<?php

declare(strict_types=1);

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MeetingsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $meetings;

    public function __construct($meetings)
    {
        $this->meetings = $meetings;
    }

    public function collection()
    {
        return $this->meetings;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Judul Meeting',
            'Ruangan',
            'Tanggal Mulai',
            'Tanggal Selesai',
            'Durasi (Menit)',
            'Estimasi Peserta',
            'Status',
            'Pembuat',
            'Disetujui Oleh',
            'Tanggal Disetujui',
            'Catatan',
        ];
    }

    public function map($meeting): array
    {
        return [
            $meeting->id,
            $this->cleanString($meeting->title),
            $this->cleanString($meeting->room->name),
            $meeting->started_at->format('d/m/Y H:i'),
            $meeting->ended_at->format('d/m/Y H:i'),
            $meeting->duration,
            $meeting->estimated_participants,
            $meeting->status->label(),
            $this->cleanString($meeting->creator->name),
            $meeting->approver ? $this->cleanString($meeting->approver->name) : '-',
            $meeting->approved_at ? $meeting->approved_at->format('d/m/Y H:i') : '-',
            $this->cleanString($meeting->notes ?? '-'),
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
