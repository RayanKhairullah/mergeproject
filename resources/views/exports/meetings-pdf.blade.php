<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Laporan Meeting</title>
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            font-size: 10px;
            margin: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 { 
            margin: 0;
            font-size: 18px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0;
            font-size: 11px;
        }
        .info {
            margin-bottom: 20px;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
            margin-top: 10px;
        }
        th, td { 
            border: 1px solid #333; 
            padding: 6px 4px; 
            text-align: left;
            vertical-align: top;
        }
        th { 
            background-color: #e0e0e0; 
            font-weight: bold;
            font-size: 9px;
        }
        td {
            font-size: 9px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .status-badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 8px;
            font-weight: bold;
        }
        .status-draft { background-color: #e0e0e0; }
        .status-pending { background-color: #fff3cd; }
        .status-published { background-color: #d1f2eb; }
        .status-completed { background-color: #d4edda; }
        .status-rejected { background-color: #f8d7da; }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 9px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Laporan Data Meeting</h1>
        <p>PT Pelindo - Sistem Manajemen Internal</p>
    </div>

    <div class="info">
        <p><strong>Tanggal Cetak:</strong> {{ now()->translatedFormat('l, d F Y - H:i') }} WIB</p>
        <p><strong>Total Data:</strong> {{ $meetings->count() }} meeting</p>
        @if($filters)
            <p><strong>Filter:</strong> {{ $filters }}</p>
        @endif
    </div>
    
    <table>
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="20%">Judul Meeting</th>
                <th width="10%">Ruangan</th>
                <th width="12%">Waktu</th>
                <th width="5%">Durasi</th>
                <th width="5%">Peserta</th>
                <th width="8%">Status</th>
                <th width="12%">Pembuat</th>
                <th width="25%">Catatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($meetings as $index => $meeting)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $meeting->title }}</td>
                    <td>{{ $meeting->room->name }}</td>
                    <td>{{ $meeting->started_at->format('d/m/Y H:i') }}</td>
                    <td class="text-center">{{ $meeting->duration }} min</td>
                    <td class="text-center">{{ $meeting->estimated_participants }}</td>
                    <td class="text-center">
                        <span class="status-badge status-{{ strtolower($meeting->status->value) }}">
                            {{ $meeting->status->label() }}
                        </span>
                    </td>
                    <td>{{ $meeting->creator->name }}</td>
                    <td>{{ Str::limit(strip_tags($meeting->notes), 100) ?: '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak oleh: {{ auth()->user()->name }}</p>
        <p>{{ now()->translatedFormat('d F Y') }}</p>
    </div>
</body>
</html>
