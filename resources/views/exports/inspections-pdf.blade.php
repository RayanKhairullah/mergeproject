<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Kesiapan Kendaraan</title>
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
    <h1>Laporan Kesiapan Kendaraan</h1>
    <p>Tanggal Cetak: {{ now()->format('d/m/Y H:i') }}</p>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Kendaraan</th>
                <th>Waktu</th>
                <th>KM</th>
                <th>Ban</th>
                <th>Body</th>
                <th>Kaca</th>
                <th>Inspektor</th>
                <th>Tanggal</th>
            </tr>
        </thead>
        <tbody>
            @foreach($inspections as $inspection)
                <tr>
                    <td class="text-center">{{ $inspection->id }}</td>
                    <td>{{ $inspection->vehicle->license_plate }}</td>
                    <td>{{ ucfirst($inspection->inspection_time) }}</td>
                    <td class="text-right">{{ number_format($inspection->mileage_check) }}</td>
                    <td>{{ $inspection->tire_condition }}</td>
                    <td>{{ $inspection->body_condition }}</td>
                    <td>{{ $inspection->glass_condition }}</td>
                    <td>{{ $inspection->user->name }}</td>
                    <td>{{ $inspection->created_at->format('d/m/Y') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
