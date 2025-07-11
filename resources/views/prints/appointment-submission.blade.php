<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>تقرير المواعيد</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 40px;
            direction: rtl;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }

        .logo {
            width: 60px;
        }

        .info {
            text-align: right;
        }

        .info p {
            margin: 2px 0;
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            font-size: 14px;
            text-align: center;
        }

        th {
            background-color: #f0f0f0;
        }

        .footer {
            margin-top: 40px;
            font-size: 13px;
            text-align: center;
        }
    </style>
</head>

<body>

    <!-- Header -->
    <div class="header">
        <div class="info">
            <p>الدكتور: {{ $submission->doctor->name }}</p>
            <p>المحاسب: {{ $submission->accountant->name }}</p>
            <p>التاريخ: {{ date('Y-m-d') }}</p>
        </div>
        <img src="{{ Storage::url($logo) }}" alt="Logo" class="logo">
    </div>

    <!-- Table -->
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>كود المريض</th>
                <th>الاسم</th>
                <th>تاريخ الحجز</th>
                <th>نوع الزيارة</th>
                <th>المبلغ</th>
                <th>ملاحظات</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($submission->appointments as $index => $appointment)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $appointment->patient?->code }}</td>
                    <td>{{ $appointment->patient?->name }}</td>
                    <td>{{ $appointment->created_at->format('Y-m-d H:i A') }}</td>
                    <td>{{ $appointment->visitType->service_type }}</td>
                    <td>{{ ($appointment->visitType->doctor_fee_type == 'fixed' ? $appointment->visitType->doctor_fee_value : ($appointment->visitType->doctor_fee_value * $appointment->visitType->price) / 100) . ' ' . __('keywords.currency') }}
                    </td>
                    <td>{{ $appointment->notes }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

</body>

</html>
