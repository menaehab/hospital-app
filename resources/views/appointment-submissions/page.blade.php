<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>جاري الطباعة...</title>

    <script src="{{ asset('js/qz-tray.js') }}"></script>

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #fef3c7;
            /* amber-100 */
            color: #92400e;
            /* amber-800 */
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            flex-direction: column;
            text-align: center;
        }

        .spinner {
            border: 6px solid #fcd34d;
            /* amber-300 */
            border-top: 6px solid #f59e0b;
            /* amber-500 */
            border-radius: 50%;
            width: 60px;
            height: 60px;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        h2 {
            font-size: 20px;
        }
    </style>
</head>

<body>
    <div class="spinner"></div>
    <h2>جاري تجهيز الطباعة... يرجى الانتظار</h2>

    <script>
        const submissionId = {{ $submission->id }};

        async function printSilently() {
            try {
                if (!qz.websocket.isActive()) {
                    await qz.websocket.connect();
                }

                const printerName = "Microsoft Print to PDF";
                const config = qz.configs.create(printerName);

                const response = await fetch(`/print/appointment-submission-content/${submissionId}`);
                const htmlContent = await response.text();

                const data = [{
                    type: 'html',
                    format: 'plain',
                    data: htmlContent
                }];

                await qz.print(config, data);
                await qz.websocket.disconnect();

                window.location.href = '/admin/appointments';

            } catch (err) {
                alert("حدث خطأ أثناء الطباعة");
                console.error(err);
                window.location.href = '/admin/appointments';

            }
        }

        printSilently();
    </script>

</body>

</html>
