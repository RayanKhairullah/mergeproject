<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Rupa-rupa</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; }
        h1 { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <h1>Laporan Rupa-rupa Kendaraan</h1>
    <p>Tanggal Cetak: {{ now()->format('d/m/Y H:i') }}</p>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Kendaraan</th>
                <th>Tipe</th>
                <th>Nominal</th>
                <th>Sumber Dana</th>
                <th>Pelapor</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenses as $expense)
                <tr>
                    <td class="text-center">{{ $expense->id }}</td>
                    <td>{{ $expense->vehicle->license_plate }}</td>
                    <td>{{ $expense->expense_type }}</td>
                    <td class="text-right">Rp {{ number_format($expense->nominal, 0, ',', '.') }}</td>
                    <td>{{ str_replace('_', ' ', $expense->funding_source) }}</td>
                    <td>{{ $expense->reporter_name ?? $expense->user->name }}</td>
                    <td>{{ $expense->created_at->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
