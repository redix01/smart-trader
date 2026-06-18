<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subjectLine }}</title>
</head>
<body style="margin:0;padding:24px;background:#f4f4f5;font-family:Arial,Helvetica,sans-serif;color:#18181b;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:680px;margin:0 auto;background:#ffffff;border:1px solid #e4e4e7;border-radius:14px;overflow:hidden;">
        <tr>
            <td style="padding:30px 32px;background:{{ $headerColor }};color:#ffffff;">
                <img src="{{ asset('img/logo.png') }}" alt="{{ config('app.name') }}" style="display:block;max-height:52px;width:auto;margin:0 0 24px;">
                @if($headerLabel)
                    <div style="margin:0 0 10px;font-size:11px;letter-spacing:0.14em;text-transform:uppercase;color:rgba(255,255,255,0.72);font-weight:700;">{{ $headerLabel }}</div>
                @endif
                <h1 style="margin:0;font-size:28px;line-height:1.25;font-weight:800;">{{ $heading }}</h1>
            </td>
        </tr>
        <tr>
            <td style="padding:32px;">
                <div style="font-size:15px;line-height:1.8;color:#3f3f46;">
                    {!! nl2br(e($messageBody)) !!}
                </div>

                @if($actionLabel && $actionUrl)
                    <p style="margin:28px 0 0;">
                        <a href="{{ $actionUrl }}" style="display:inline-block;padding:13px 20px;background:{{ $accentColor }};color:#ffffff;text-decoration:none;border-radius:9px;font-size:14px;font-weight:800;">{{ $actionLabel }}</a>
                    </p>
                @endif
            </td>
        </tr>
        <tr>
            <td style="padding:22px 32px;background:#fafafa;border-top:1px solid #e4e4e7;">
                <p style="margin:0;font-size:12px;line-height:1.7;color:#71717a;">{{ $footerText }}</p>
            </td>
        </tr>
    </table>
</body>
</html>
