<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>تقرير المواعيد</title>
    <style>
        @font-face {
            font-family: 'arabic';
            src: url('{{ asset('fonts/tajawal.ttf') }}') format('truetype');
        }

        body {
            font-family: 'arabic', Tajawal, sans-serif;
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
            width: 120px;
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

        .total {
            margin-top: 20px;
            font-weight: bold;
            font-size: 16px;
        }

        .footer {
            margin-top: 40px;
            font-size: 13px;
            text-align: center;
            color: #444;
        }

        .mr-2 {
            margin-right: 2px;
        }
    </style>
</head>

<body>
    <div dir="rtl">

        <!-- Header -->
        <div class="header">
            <div class="info">
                <p><span class="mr-2">{{ __('keywords.the_doctor') }}</span>
                    {{ $submission->doctor->name }}
                </p>
                <p><span class="mr-2">{{ __('keywords.accountant') }}</span>
                    {{ $submission->accountant->name }}
                </p>
                <p><span class="mr-2">{{ __('keywords.date') }}</span> {{ date('Y-m-d') }}</p>
            </div>
            @if ($logo)
                <img src="{{ asset('storage/' . $logo) }}" alt="Logo" class="logo">
            @else
                <img src="{{ asset('images') }}/logo-with-background.jpg" alt="Logo" class="logo">
            @endif
        </div>

        <!-- Table -->
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('keywords.patient_code') }}</th>
                    <th>{{ __('keywords.patient_name') }}</th>
                    <th>{{ __('keywords.appointment_number') }}</th>
                    <th>{{ __('keywords.appointment_date') }}</th>
                    <th>{{ __('keywords.session_duration') }}</th>
                    <th>{{ __('keywords.visit_type') }}</th>
                    <th>{{ __('keywords.amount') }}</th>
                    <th>{{ __('keywords.notes') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($submission->appointments as $index => $appointment)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $appointment->patient?->code }}</td>
                        <td>{{ $appointment->patient?->name }}</td>
                        <td>{{ $appointment->number }}</td>
                        <td>{{ $appointment->created_at->format('Y-m-d H:i A') }}</td>
                        <td>
                            @if ($appointment->start_time && $appointment->end_time)
                                {{ \Carbon\Carbon::parse($appointment->start_time)->diff(\Carbon\Carbon::parse($appointment->end_time))->format('%H:%I:%S') }}
                            @else
                                {{ __('keywords.not_found') }}
                            @endif
                        </td>
                        <td>{{ $appointment->visitType->service_type }}</td>
                        <td>
                            {{ $appointment->visitType->doctor_fee_type == 'fixed'
                                ? $appointment->visitType->doctor_fee_value
                                : ($appointment->visitType->doctor_fee_value * $appointment->visitType->price) / 100 }}
                            {{ __('keywords.currency') }}
                        </td>
                        <td>
                            @if ($appointment->notes)
                                {{ $appointment->notes }}
                            @else
                                {{ __('keywords.not_found') }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Total -->
        <div class="total">
            <p>{{ __('keywords.total') }}</p>
            <p>{{ number_format($submission->total_amount, 2) }} {{ __('keywords.currency') }}</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p>{{ __('keywords.printed_by') }}</p>
            <p>{{ Auth::user()->name }}</p>
        </div>

    </div>
</body>

</html>
