<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'Asylon') }}</title>
    <style>
        body { font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, 'Helvetica Neue', sans-serif; background: #f6f7fb; margin: 0; padding: 20px; color: #0f172a; }
        .wrapper { max-width: 640px; margin: 0 auto; }
        .card { background: #fff; border-radius: 14px; box-shadow: 0 18px 48px rgba(0,0,0,0.08); padding: 28px; border: 1px solid #ebedf3; }
        .header { text-align: center; margin-bottom: 18px; }
        .logo { height: 48px; margin-bottom: 8px; }
        .tagline { color: #2563eb; font-weight: 600; letter-spacing: 0.3px; }
        h1 { font-size: 22px; margin: 18px 0 10px; }
        p { line-height: 1.6; margin: 6px 0; }
        .btn { display: inline-block; padding: 12px 20px; background: #2563eb; color: #fff; border-radius: 10px; text-decoration: none; font-weight: 600; margin: 14px 0; }
        .meta { color: #6b7280; font-size: 14px; }
        .footer { color: #94a3b8; font-size: 13px; text-align: center; margin-top: 18px; }
        .pill { display: inline-block; padding: 4px 10px; background: #eef2ff; color: #4338ca; border-radius: 999px; font-size: 12px; font-weight: 600; }
        .panel { background: #f8fafc; border-radius: 12px; padding: 14px; border: 1px solid #e5e7eb; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <img src="{{ asset('assets/images/logo.png') }}" alt="{{ config('app.name', 'Asylon') }}" class="logo">
            <div class="tagline">{{ __('common.email_tagline') }}</div>
        </div>
        <div class="card">
            @yield('content')
        </div>
        <div class="footer">
            {{ __('common.email_footer') }}
        </div>
    </div>
</body>
</html>
