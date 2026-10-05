<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>New message</title>
    <style>
        .header {
            background-color:
                {{ $accent }}
            ;
        }

        .avatar {
            background-color:
                {{ $tint }}
            ;
            color:
                {{ $accent }}
            ;
        }

        .email a {
            color:
                {{ $accent }}
            ;
        }

        .btn {
            background-color:
                {{ $accent }}
            ;
        }
    </style>
</head>

<body>

    <div class="preheader">{{ \Illuminate\Support\Str::limit($contact->message, 90) }}</div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" class="wrapper">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" class="card">

                    <tr>
                        <td class="header">
                            <div class="eyebrow">{{ $siteName }}</div>
                            <div class="title">New contact message</div>
                        </td>
                    </tr>

                    <tr>
                        <td class="sender">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td width="52" valign="top">
                                        <div class="avatar">{{ strtoupper(mb_substr($contact->name, 0, 1)) }}</div>
                                    </td>
                                    <td valign="middle">
                                        <div class="name">{{ $contact->name }}</div>
                                        <div class="email">
                                            <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td class="section">
                            <div class="label">Message</div>
                            <div class="message">{!! nl2br(e($contact->message)) !!}</div>
                        </td>
                    </tr>

                    <tr>
                        <td align="left" class="cta">
                            <a class="btn"
                                href="mailto:{{ $contact->email }}?subject={{ rawurlencode('Re: ' . ($contact->subject ?: 'Your message')) }}">
                                Reply to {{ \Illuminate\Support\Str::before($contact->name, ' ') }}
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td class="footer">
                            <div class="footer-text">
                                Received {{ $contact->created_at->format('M j, Y \a\t g:i A') }}<br>
                                Sent from the contact form on {{ $siteName }}.
                            </div>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

</body>

</html>