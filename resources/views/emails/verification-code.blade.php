<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f1f5f9; margin: 0; padding: 40px 16px; }
        .card { max-width: 480px; margin: 0 auto; background: #fff; border-radius: 24px; padding: 40px; border: 2px solid #e2e8f0; border-bottom: 8px solid #e2e8f0; }
        .logo { font-size: 28px; font-weight: 900; color: #1cb0f6; letter-spacing: 2px; margin-bottom: 8px; }
        .subtitle { font-size: 13px; color: #94a3b8; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 32px; }
        h2 { font-size: 20px; font-weight: 800; color: #1e293b; margin-bottom: 8px; }
        p { font-size: 14px; color: #64748b; line-height: 1.6; }
        .code-box { background: #f8fafc; border: 2px dashed #6366f1; border-radius: 16px; padding: 24px; text-align: center; margin: 28px 0; }
        .code { font-size: 48px; font-weight: 900; letter-spacing: 12px; color: #6366f1; font-family: monospace; }
        .expiry { font-size: 12px; color: #94a3b8; margin-top: 8px; }
        .footer { margin-top: 32px; padding-top: 24px; border-top: 2px dashed #f1f5f9; font-size: 12px; color: #94a3b8; text-align: center; }
        .warning { background: #fef9ec; border: 2px solid #fde68a; border-radius: 12px; padding: 12px 16px; font-size: 13px; color: #92400e; margin-top: 16px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">学ぶ Manabu</div>
        <div class="subtitle">Kode Verifikasi Akun</div>

        <h2>Halo, {{ $userName }}! 👋</h2>
        <p>Terima kasih telah mendaftar di Manabu. Masukkan kode verifikasi di bawah ini untuk mengaktifkan akunmu:</p>

        <div class="code-box">
            <div class="code">{{ $code }}</div>
            <div class="expiry">⏱ Berlaku selama <strong>10 menit</strong></div>
        </div>

        <div class="warning">
            ⚠️ Jangan bagikan kode ini kepada siapapun. Tim Manabu tidak akan pernah memintamu kode verifikasi.
        </div>

        <p style="margin-top:20px;">Jika kamu tidak mendaftar di Manabu, abaikan email ini.</p>

        <div class="footer">
            &copy; {{ date('Y') }} Manabu — Belajar Bahasa Jepang Menyenangkan 🎌
        </div>
    </div>
</body>
</html>
