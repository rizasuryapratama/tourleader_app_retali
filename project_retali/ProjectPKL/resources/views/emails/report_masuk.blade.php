<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            background-color: #f4f7f6;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
        }
        .wrapper {
            width: 100%;
            padding: 40px 0;
        }
        .card {
            background-color: #ffffff;
            max-width: 500px;
            margin: 0 auto;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }
        .header {
            background-color: #842D62;
            padding: 40px 20px;
            text-align: center;
        }
        .logo {
            max-width: 130px;
            height: auto;
            margin-bottom: 15px;
        }
        .content {
            padding: 40px;
            color: #444;
        }
        .welcome-text {
            font-size: 18px;
            color: #111;
            margin-bottom: 20px;
        }
        .user-name {
            color: #842D62;
            font-weight: 700;
        }
        .message-container {
            background-color: #fdf2f8;
            border-radius: 12px;
            padding: 24px;
            margin: 25px 0;
            border-left: 5px solid #842D62;
        }
        .message-text {
            color: #4a4a4a;
            font-style: italic;
            font-size: 16px;
            line-height: 1.7;
            margin: 0;
        }
        .footer {
            text-align: center;
            padding: 30px;
            font-size: 12px;
            color: #999;
            background-color: #fafafa;
        }
        .divider {
            border-top: 1px solid #eee;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="card">
            <div class="header">
                {{-- Mengambil logo dari public/images/logo-retali.png --}}
                <img src="{{ $message->embed(public_path('images/logo-retali.png')) }}" alt="Retali Logo" class="logo">
                <h2 style="margin:0; color: #ffffff; font-size: 18px; letter-spacing: 2px; font-weight: 400; opacity: 0.9;">
                    RETALI OPERATION
                </h2>
            </div>

            <div class="content">
                <p class="welcome-text">Assalammualaikum, <span class="user-name">{{ $username }}</span> 👋</p>
                <p style="font-size: 15px; color: #666; line-height: 1.6;">
                    Anda menerima pembaruan informasi penting dari Retali Zero Complaint:
                </p>

                <div class="message-container">
                    <p class="message-text">"{{ $pesan }}"</p>
                </div>

                <p style="font-size: 14px; color: #888; margin-top: 20px;">
                    Silakan cek aplikasi mobile Anda untuk informasi lebih lanjut mengenai pembaruan ini.
                </p>
            </div>

            <div class="footer">
                <div class="divider"></div>
                <p style="margin-bottom: 5px; font-weight: 600;">Retali Project Team</p>
                <p style="margin-top: 0;">&copy; {{ date('Y') }} All Rights Reserved.</p>
                <p style="font-size: 11px; opacity: 0.7; margin-top: 15px;">
                    Email ini dihasilkan secara otomatis oleh sistem. Mohon tidak membalas email ini.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
