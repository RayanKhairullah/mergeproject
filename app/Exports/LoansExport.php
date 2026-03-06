<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LoansExport implements FromCollection, WithHeadings, WithMapping
{
    protected $loans;

    public function __construct($loans)
    {
        $this->loans = $loans;
    }

    public function collection()
    {
        return $this->loans;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Kendaraan',
            'Peminjam',
            'Tanggal Pinjam',
            'Tanggal Kembali',
            'KM Awal',
            'KM Akhir',
            'Total KM',
            'Tujuan',
            'Destinasi',
            'Status',
        ];
    }

    public function map($loan): array
    {
        return [
            $loan->id,
            $this->cleanString($loan->vehicle->license_plate),
            $this->cleanString($loan->user->name),
            $loan->loan_date->format('d/m/Y H:i'),
            $loan->return_date?->format('d/m/Y H:i') ?? '-',
            number_format($loan->start_mileage),
            $loan->end_mileage ? number_format($loan->end_mileage) : '-',
            $loan->end_mileage ? number_format($loan->end_mileage - $loan->start_mileage) : '-',
            $this->cleanString($loan->purpose),
            $this->cleanString($loan->destination ?? '-'),
            $loan->return_date ? 'Dikembalikan' : 'Dipinjam',
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
