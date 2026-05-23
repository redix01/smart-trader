<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subjectLine }}</title>
</head>
<body style="margin:0;padding:24px;background:#f4f4f5;font-family:Arial,Helvetica,sans-serif;color:#18181b;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:640px;margin:0 auto;background:#ffffff;border:1px solid #e4e4e7;border-radius:12px;overflow:hidden;">
        <tr>
            <td style="padding:28px 28px 16px;background:#09090b;color:#ffffff;">
                <div style="font-size:12px;letter-spacing:0.12em;text-transform:uppercase;color:#a1a1aa;">Acuity</div>
                <h1 style="margin:12px 0 0;font-size:24px;line-height:1.3;">{{ $heading }}</h1>
            </td>
        </tr>
        <tr>
            <td style="padding:28px;">
                <p style="margin:0 0 20px;font-size:15px;line-height:1.7;color:#3f3f46;">{{ $messageLine }}</p>

                @if(!empty($details))
                    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;margin:0 0 24px;">
                        @foreach($details as $label => $value)
                            <tr>
                                <td style="padding:10px 0;border-top:1px solid #e4e4e7;font-size:13px;font-weight:700;color:#71717a;width:42%;">{{ $label }}</td>
                                <td style="padding:10px 0;border-top:1px solid #e4e4e7;font-size:14px;color:#18181b;">{{ $value }}</td>
                            </tr>
                        @endforeach
                    </table>
                @endif

                @if($actionLabel && $actionUrl)
                    <p style="margin:0 0 24px;">
                        <a href="{{ $actionUrl }}" style="display:inline-block;padding:12px 18px;background:#18181b;color:#ffffff;text-decoration:none;border-radius:8px;font-size:14px;font-weight:700;">{{ $actionLabel }}</a>
                    </p>
                @endif

                <p style="margin:0;font-size:13px;line-height:1.7;color:#71717a;">This is an automated account update email.</p>
            </td>
        </tr>
    </table>
</body>
</html>
