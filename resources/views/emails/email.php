<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>@yield('title')</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #1f2937;
            background-color: #f9fafb;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        }

        .content {
            padding: 40px 30px;
        }

        .welcome-text {
            font-size: 16px;
            color: #374151;
            margin-bottom: 24px;
            line-height: 1.7;
        }

        .otp-section {
            text-align: center;
            margin: 40px 0;
        }

        .otp-label {
            font-size: 13px;
            font-weight: 600;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 16px;
            display: block;
        }

        .otp-code {
            background: linear-gradient(135deg, #4338ca 0%, #581c87 100%);
            color: #ffffff;
            font-size: 36px;
            font-weight: 700;
            letter-spacing: 4px;
            padding: 24px;
            border-radius: 10px;
            font-family: 'Courier New', monospace;
            word-spacing: 8px;
            display: inline-block;
            min-width: 280px;
        }

        .expiry-info {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 20px;
            font-style: italic;
        }

        .security-note {
            border-radius: 6px;
            padding: 16px;
            margin: 32px 0;
            font-size: 13px;
            color: #6b21a8;
            line-height: 1.6;
        }

        .security-note strong {
            color: #581c87;
        }

        .footer-section {
            background-color: #f9fafb;
            padding: 24px 30px;
            border-top: 1px solid #e5e7eb;
            font-size: 12px;
            color: #6b7280;
            line-height: 1.6;
        }



        .divider {
            height: 1px;
            background-color: #e5e7eb;
            margin: 24px 0;
        }

        @media (max-width: 600px) {
            .content {
                padding: 24px 20px;
            }

            .otp-code {
                font-size: 28px;
                letter-spacing: 2px;
                min-width: 100%;
                word-spacing: 4px;
            }

            .welcome-text {
                font-size: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="email-container">
        <div class="content">
			@yield('content')

            <div class="security-note">
                <strong>{{ trans('auth.security.warning') }}</strong> {{ trans('auth.security.never_share') }}
            </div>

            <div class="divider"></div>

            <p style="font-size: 13px; color: #6b7280; line-height: 1.6;">
                {{ trans('auth.security.ignore_if_not_requested') }}
            </p>
        </div>

        <div class="footer-section">
            <p>
				&copy; {{ trans('auth.footer.copyright', ['year' => date('Y'), 'appName' => config('app.name')]) }}
            </p>
        </div>
    </div>
</body>
</html>
