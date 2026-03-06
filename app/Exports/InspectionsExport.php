<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InspectionsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $inspections;

    public function __construct($inspections)
    {
        $this->inspections = $inspections;
    }

    public function collection()
    {
        return $this->inspections;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Kendaraan',
            'Waktu Inspeksi',
            'Kilometer',
            'Kondisi Ban',
            'Kondisi Body',
            'Kondisi Kaca',
            'Catatan',
            'Inspektor',
            'Tanggal',
        ];
    }

    public function map($inspection): array
    {
        return [
            $inspection->id,
            $this->cleanString($inspection->vehicle->license_plate),
            ucfirst($inspection->inspection_time),
            number_format($inspection->mileage_check),
            $this->cleanString($inspection->tire_condition),
            $this->cleanString($inspection->body_condition),
            $this->cleanString($inspection->glass_condition),
            $this->cleanString($inspection->additional_notes ?? '-'),
            $this->cleanString($inspection->user->name),
            $inspection->created_at->format('d/m/Y H:i'),
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

        return $string;
    }
}
