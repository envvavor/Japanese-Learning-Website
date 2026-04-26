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
        .btn { display: inline-block; background: #6366f1; color: #ffffffff; font-weight: 800; font-size: 16px; padding: 14px 32px; border-radius: 16px; text-decoration: none; margin: 24px 0; border-bottom: 4px solid #4f46e5; }
        .url-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 12px 16px; font-size: 12px; color: #64748b; word-break: break-all; margin-top: 8px; }
        .expiry { font-size: 13px; color: #ef4444; font-weight: 700; margin-top: 16px; }
        .footer { margin-top: 32px; padding-top: 24px; border-top: 2px dashed #f1f5f9; font-size: 12px; color: #94a3b8; text-align: center; }
    </style>
</head>
<body>
    <div class="card">
        <div class="logo">学ぶ Manabu</div>
        <div class="subtitle">Reset Kata Sandi</div>

        <h2>Reset Kata Sandimu 🔑</h2>
        <p>Kami menerima permintaan reset kata sandi untuk akun Manabu yang terhubung ke email ini. Klik tombol di bawah untuk membuat kata sandi baru:</p>

        <div style="text-align:center;">
            <a href="{{ $resetUrl }}" class="btn">🔐 Reset Kata Sandi</a>
        </div>

        <p>Atau salin tautan ini ke browser kamu:</p>
        <div class="url-box">{{ $resetUrl }}</div>

        <p class="expiry">⏱ Tautan ini berlaku selama <strong>60 menit</strong>.</p>

        <p style="margin-top:16px;">Jika kamu tidak meminta reset kata sandi, abaikan email ini. Kata sandimu tidak akan berubah.</p>

        <div class="footer">
            &copy; {{ date('Y') }} Manabu — Belajar Bahasa Jepang Menyenangkan 🎌
        </div>
    </div>
</body>
</html>
