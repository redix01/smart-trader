<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $subjectLine }}</title>
</head>
<body style="margin:0; padding:0; background-color:#eef2ff; font-family:Arial, Helvetica, sans-serif; color:#0f172a;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#eef2ff; margin:0; padding:32px 12px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:640px; background-color:#ffffff; border-radius:22px; overflow:hidden; box-shadow:0 20px 45px rgba(15, 23, 42, 0.12);">
                    <tr>
                        <td style="padding:0; background:linear-gradient(135deg, {{ $headerColor }} 0%, #0f172a 160%);">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding:26px 28px;">
                                @if(!empty($accentLabel))
                                    <tr>
                                        <td>
                                            <span style="display:inline-block; padding:7px 12px; border-radius:999px; background:rgba(255,255,255,0.16); color:#ffffff; font-size:10px; font-weight:700; letter-spacing:0.18em; text-transform:uppercase;">
                                                {{ $accentLabel }}
                                            </span>
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td style="padding-top:14px;">
                                        <table role="presentation" cellpadding="0" cellspacing="0">
                                            <tr>
                                                <td style="vertical-align:middle;">
                                                    @if(\App\Helpers\WebsiteSettingsHelper::hasImageLogo())
                                                        <img src="{{ \App\Helpers\WebsiteSettingsHelper::getLogoUrl() }}" alt="{{ $siteName }}" style="display:block; max-height:46px; width:auto;">
                                                    @elseif(\App\Helpers\WebsiteSettingsHelper::hasTextLogo())
                                                        <div style="display:inline-block; padding:11px 15px; border-radius:14px; background:rgba(255,255,255,0.12); color:#ffffff; font-size:19px; font-weight:700;">
                                                            {{ \App\Helpers\WebsiteSettingsHelper::getTextLogo() }}
                                                        </div>
                                                    @else
                                                        <div style="display:inline-block; padding:11px 15px; border-radius:14px; background:rgba(255,255,255,0.12); color:#ffffff; font-size:19px; font-weight:700;">
                                                            {{ $siteName }}
                                                        </div>
                                                    @endif
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-top:18px; color:#ffffff;">
                                        <div style="font-size:25px; line-height:1.2; font-weight:700;">{{ $subjectLine }}</div>
                                        <div style="margin-top:8px; font-size:13px; line-height:1.7; color:rgba(255,255,255,0.82);">
                                            {{ $siteTagline ?: 'Official communication from our admin team.' }}
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:36px 32px 24px;">
                            <div style="font-size:15px; line-height:1.8; color:#334155;">
                                @foreach (preg_split("/\n{2,}/", trim($messageBody)) as $paragraph)
                                    @if(trim($paragraph) !== '')
                                        <p style="margin:0 0 18px;">{!! nl2br(e(trim($paragraph))) !!}</p>
                                    @endif
                                @endforeach
                            </div>

                            <div style="margin-top:28px; padding:20px 22px; border-radius:18px; background-color:#f8fafc; border:1px solid #e2e8f0;">
                                <div style="font-size:12px; font-weight:700; letter-spacing:0.14em; text-transform:uppercase; color:#64748b;">Recipient</div>
                                <div style="margin-top:8px; font-size:14px; color:#0f172a;">{{ $recipientEmail }}</div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:0 32px 32px;">
                            <div style="height:1px; background:#e2e8f0;"></div>
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="padding-top:22px;">
                                <tr>
                                    <td style="font-size:13px; line-height:1.7; color:#64748b;">
                                        <strong style="display:block; margin-bottom:6px; color:#0f172a;">{{ $siteName }}</strong>
                                        {{ $footerNote ?: 'This email was sent from the admin team. If you need help, reply to this message.' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding-top:18px; font-size:12px; color:#94a3b8;">
                                        &copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
