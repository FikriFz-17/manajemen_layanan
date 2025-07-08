<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Verifikasi Email - LAPOR KOMINFO</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="background-color: #f3f4f6; margin: 0; padding: 40px 0; font-family: Arial, sans-serif;">

  <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; padding: 30px; text-align: center; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.05);">
    <!-- Logo KOMINFO -->
    <div style="margin-bottom: 20px;">
      <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c2/LOGO_KABUPATEN_KEBUMEN.png/960px-LOGO_KABUPATEN_KEBUMEN.png" alt="Logo KOMINFO" style="width: 80px; height: 80px; object-fit: contain;">
    </div>

    <!-- Judul -->
    <h2 style="color: #1f2937; font-size: 22px; font-weight: bold; margin-bottom: 10px;">
      Verifikasi Email Anda
    </h2>

    <!-- Pesan -->
    <p style="color: #4b5563; font-size: 16px; line-height: 1.6; margin-bottom: 30px;">
      Hai <span style="color: #2563eb; font-weight: 600;">{{ $user->nama }}</span>,<br>
      Terima kasih telah mendaftar di <strong>LAPOR KOMINFO</strong>.<br>
      Klik tombol di bawah ini untuk memverifikasi alamat email Anda.
    </p>

    <!-- Tombol Verifikasi -->
    <a href="{{ $url }}" style="display: inline-block; background-color: #2563eb; color: white; font-weight: bold; text-decoration: none; padding: 14px 28px; border-radius: 8px; margin-bottom: 30px;">
      ✉️ Verifikasi Sekarang
    </a>

    <!-- Warning Expired -->
    <p style="font-size: 13px; color: #dc2626; margin-bottom: 30px;">
        ⚠️ Tautan verifikasi ini hanya berlaku selama 1 menit. Setelah itu, Anda perlu meminta ulang tautan baru.
    </p>

    <!-- Link alternatif -->
    <div style="text-align: left; font-size: 13px; color: #6b7280; background-color: #f9fafb; border: 1px solid #e5e7eb; padding: 12px 16px; border-radius: 8px; word-wrap: break-word;">
      Jika tombol tidak berfungsi, salin dan buka tautan ini di browser Anda:<br>
      <a href="{{ $url }}" style="color: #2563eb; word-break: break-all;">{{ $url }}</a>
    </div>

    <!-- Footer -->
    <p style="font-size: 12px; color: #9ca3af; margin-top: 40px;">
      © 2025 Dinas Komunikasi dan Informatika Kabupaten Kebumen
    </p>
  </div>
</body>
</html>
