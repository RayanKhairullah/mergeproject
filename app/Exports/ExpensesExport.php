<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ExpensesExport implements FromCollection, WithHeadings, WithMapping
{
    protected $expenses;

    public function __construct($expenses)
    {
        $this->expenses = $expenses;
    }

    public function collection()
    {
        return $this->expenses;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Kendaraan',
            'Tipe',
            'Nominal',
            'Sumber Dana',
            'Jenis BBM',
            'Liter',
            'Catatan',
            'Pelapor',
            'Tanggal',
        ];
    }

    public function map($expense): array
    {
        return [
            $expense->id,
            $this->cleanString($expense->vehicle->license_plate),
            $expense->expense_type,
            'Rp '.number_format($expense->nominal, 0, ',', '.'),
            $this->cleanString(str_replace('_', ' ', $expense->funding_source)),
            $expense->fuel_type ?? '-',
            $expense->fuel_liters ?? '-',
            $this->cleanString($expense->notes ?? '-'),
            $this->cleanString($expense->reporter_name ?? $expense->user->name),
            $expense->created_at->format('d/m/Y H:i'),
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
