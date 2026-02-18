<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? __('Reporting portal disabled') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #0d6efd;
            --muted: #6b7280;
            --bg: #f8fafc;
        }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: radial-gradient(circle at 20% 20%, rgba(13,110,253,0.08), transparent 25%),
                        radial-gradient(circle at 80% 30%, rgba(13,110,253,0.05), transparent 20%),
                        var(--bg);
            color: #111827;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.08);
            max-width: 560px;
            width: 100%;
            padding: 32px;
            border: 1px solid rgba(0,0,0,0.04);
        }
        .logo {
            width: 140px;
            height: auto;
            display: block;
            margin: 0 auto 18px;
        }
        h1 {
            font-size: 24px;
            margin: 0 0 8px;
            text-align: center;
        }
        .subheading {
            color: var(--muted);
            font-size: 15px;
            text-align: center;
            margin: 0 0 18px;
        }
        .message {
            background: #f1f5f9;
            border-radius: 12px;
            padding: 16px 18px;
            font-size: 15px;
            line-height: 1.6;
            color: #0f172a;
            text-align: center;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            color: var(--muted);
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="card" role="alert" aria-live="polite">
        <img class="logo" src="{{ asset('assets/images/logo.png') }}" alt="{{ config('app.name', 'Asylon') }} logo">
        <h1>{{ $heading ?? __('Reporting portal disabled') }}</h1>
        @if (!empty($subheading))
            <p class="subheading">{{ $subheading }}</p>
        @endif
        <div class="message">
            {{ $message ?? __('This reporting portal is disabled for your organization. Please contact your administrator.') }}
        </div>
        <div class="footer">
            {{ __('Speak Up. Stay Safe.') }}
        </div>
    </div>
</body>
</html>
