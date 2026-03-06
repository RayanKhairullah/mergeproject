<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Peminjaman</title>
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
    <h1>Laporan Peminjaman Kendaraan</h1>
    <p>Tanggal Cetak: {{ now()->format('d/m/Y H:i') }}</p>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Kendaraan</th>
                <th>Peminjam</th>
                <th>Tanggal Pinjam</th>
                <th>Tanggal Kembali</th>
                <th>KM Awal</th>
                <th>KM Akhir</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($loans as $loan)
                <tr>
                    <td class="text-center">{{ $loan->id }}</td>
                    <td>{{ $loan->vehicle->license_plate }}</td>
                    <td>{{ $loan->user->name }}</td>
                    <td>{{ $loan->loan_date->format('d/m/Y H:i') }}</td>
                    <td>{{ $loan->return_date?->format('d/m/Y H:i') ?? '-' }}</td>
                    <td class="text-right">{{ number_format($loan->start_mileage) }}</td>
                    <td class="text-right">{{ $loan->end_mileage ? number_format($loan->end_mileage) : '-' }}</td>
                    <td>{{ $loan->return_date ? 'Dikembalikan' : 'Dipinjam' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
