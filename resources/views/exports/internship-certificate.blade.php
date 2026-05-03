<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Internship Certificate</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; }
        .certificate-container {
            border: 10px double #14b8a6;
            padding: 50px;
            text-align: center;
            position: relative;
        }
        .header { font-size: 40px; font-weight: bold; color: #14b8a6; margin-bottom: 20px; }
        .sub-header { font-size: 20px; margin-bottom: 40px; }
        .name { font-size: 30px; font-weight: bold; border-bottom: 2px solid #333; display: inline-block; padding: 0 50px; margin: 20px 0; }
        .content { font-size: 18px; line-height: 1.6; margin: 40px 0; }
        .footer { margin-top: 60px; }
        .signature { display: inline-block; width: 200px; border-top: 1px solid #333; margin-top: 50px; }
    </style>
</head>
<body>
    <div class="certificate-container">
        <div class="header">CERTIFICATE OF COMPLETION</div>
        <div class="sub-header">PROUDLY PRESENTED TO</div>
        
        <div class="name">{{ $user->name }}</div>
        
        <div class="content">
            This is to certify that the above person has successfully completed an internship program at <br>
            <strong>Pelindo Internship Program</strong> in the Department of <strong>{{ $internship->division->name ?? $internship->department }}</strong><br>
            serving as <strong>{{ $internship->position }}</strong><br>
            from {{ $internship->start_date->format('d M Y') }} to {{ $internship->end_date->format('d M Y') }}.
        </div>

        <div class="footer">
            <div style="float: left; width: 50%;">
                Date: {{ $date }}<br>
                Certificate ID: PEL/INT/{{ $internship->id }}/{{ date('Y') }}
            </div>
            <div style="float: right; width: 50%;">
                <div class="signature">Management</div>
            </div>
            <div style="clear: both;"></div>
        </div>
    </div>
</body>
</html>
