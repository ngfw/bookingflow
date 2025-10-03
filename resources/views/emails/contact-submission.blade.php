<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #ec4899 0%, #8b5cf6 100%);
            color: white;
            padding: 20px;
            border-radius: 8px 8px 0 0;
        }
        .content {
            background: #f9fafb;
            padding: 30px;
            border-radius: 0 0 8px 8px;
        }
        .field {
            margin-bottom: 20px;
        }
        .label {
            font-weight: bold;
            color: #666;
            font-size: 14px;
            text-transform: uppercase;
        }
        .value {
            margin-top: 5px;
            padding: 10px;
            background: white;
            border-left: 3px solid #ec4899;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #999;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 style="margin: 0;">New Contact Form Submission</h1>
        </div>
        <div class="content">
            <p>You have received a new message from your website contact form.</p>

            <div class="field">
                <div class="label">From:</div>
                <div class="value">{{ $submission->name }}</div>
            </div>

            <div class="field">
                <div class="label">Email:</div>
                <div class="value">
                    <a href="mailto:{{ $submission->email }}">{{ $submission->email }}</a>
                </div>
            </div>

            @if($submission->phone)
            <div class="field">
                <div class="label">Phone:</div>
                <div class="value">{{ $submission->phone }}</div>
            </div>
            @endif

            <div class="field">
                <div class="label">Subject:</div>
                <div class="value">{{ $submission->subject }}</div>
            </div>

            <div class="field">
                <div class="label">Message:</div>
                <div class="value">{{ $submission->message }}</div>
            </div>

            <div class="field">
                <div class="label">Submitted:</div>
                <div class="value">{{ $submission->created_at->format('F j, Y \a\t g:i A') }}</div>
            </div>
        </div>

        <div class="footer">
            <p>This email was sent from your website contact form.</p>
            <p>You can reply directly to this email to respond to {{ $submission->name }}.</p>
        </div>
    </div>
</body>
</html>
