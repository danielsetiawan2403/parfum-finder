<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Akun kamu telah dihapus</title>
</head>
<body style="margin:0; padding:24px 12px; background:#f3f4f6; font-family:Arial, Helvetica, sans-serif; color:#1f2937; line-height:1.55;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0"
                       style="max-width:520px; background:#ffffff; border-radius:12px; overflow:hidden;">
                    <tr>
                        <td style="background:#dc3545; color:#ffffff; padding:18px 24px; font-size:18px; font-weight:bold;">
                            Parfum Finder
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px; font-size:15px;">
                            <h1 style="margin:0 0 14px; font-size:20px; color:#111827;">Akun kamu telah dihapus</h1>

                            <p style="margin:0 0 12px;">Halo {{ $recipientName }},</p>

                            <p style="margin:0 0 12px;">
                                Admin Parfum Finder telah menghapus akunmu. Berikut alasan penghapusannya:
                            </p>

                            {{-- Alasan dari admin; e() mencegah HTML disisipkan, nl2br menjaga baris baru --}}
                            <div style="margin:16px 0; padding:12px 16px; background:#fff3f3; border-left:4px solid #dc3545; border-radius:4px;">
                                {!! nl2br(e($reason)) !!}
                            </div>

                            <p style="margin:0 0 12px;">
                                Dengan dihapusnya akun ini, kamu tidak bisa lagi login ke Parfum Finder.
                                Ulasan dan riwayat chat konsultasimu juga ikut dihapus.
                            </p>

                            <p style="margin:0 0 12px;">
                                Kalau menurutmu penghapusan ini keliru, silakan hubungi admin Parfum Finder
                                dan sampaikan keberatanmu.
                            </p>

                            <p style="margin:24px 0 0;">Salam,<br>Tim Parfum Finder</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:14px 24px; background:#f9fafb; font-size:12px; color:#6b7280;">
                            Email ini dikirim otomatis karena akun dengan alamat email ini dihapus oleh admin.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
