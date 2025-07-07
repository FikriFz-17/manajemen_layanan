<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Notifikasi Laporan</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">

        <!-- Logo -->
        <div style="text-align: center; margin-bottom: 20px;">
            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/c/c2/LOGO_KABUPATEN_KEBUMEN.png/960px-LOGO_KABUPATEN_KEBUMEN.png" alt="Logo Diskominfo Kebumen" style="height: 80px;">
        </div>

        <h2 style="color: #2d3748;">Notifikasi Penanganan Laporan</h2>

        <p>Halo <strong>{{ $data['nama'] }}</strong>,</p>

        <p>Laporan Anda dengan detail berikut telah selesai ditangani:</p>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
            <tr>
                <td><strong>No. Resi</strong></td>
                <td>: {{ $data['resi'] }}</td>
            </tr>
            <tr>
                <td><strong>Masalah</strong></td>
                <td>: {{ $data['masalah'] }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal Pengajuan</strong></td>
                <td>: {{ \Carbon\Carbon::parse($data['tanggal_pengajuan'])->format('d M Y') }}</td>
            </tr>
            <tr>
                <td><strong>Tanggal Selesai</strong></td>
                <td>: {{ \Carbon\Carbon::parse($data['tanggal_selesai'])->format('d M Y') }}</td>
            </tr>
            <tr>
                <td><strong>Status</strong></td>
                <td>: {{ $data['status'] }}</td>
            </tr>
        </table>

        <p><strong>Deskripsi Penyelesaian:</strong></p>
        <p style="background-color: #f9f9f9; padding: 10px; border-left: 4px solid #75ba75;">
            {{ $data['penyelesaian'] }}
        </p>

        <p>Terima kasih atas perhatian dan kerjasamanya.</p>

        <p>Salam, <br><strong>DISKOMINFO Kabupaten Kebumen</strong></p>
    </div>
</body>
</html>
