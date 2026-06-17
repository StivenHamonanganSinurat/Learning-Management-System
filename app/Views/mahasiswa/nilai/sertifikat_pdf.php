<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sertifikat Kelulusan - <?= esc($nilai['nama_mk']) ?></title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
            padding: 0;
            background-color: #ffffff;
        }
        .cert-container {
            width: 100%;
            height: 100%;
            padding: 30px;
            box-sizing: border-box;
            border: 15px double #b8860b;
            text-align: center;
            position: relative;
        }
        .cert-header {
            margin-top: 30px;
        }
        .cert-title {
            font-size: 44px;
            color: #b8860b;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 2px;
            margin-bottom: 5px;
        }
        .cert-subtitle {
            font-size: 18px;
            font-style: italic;
            letter-spacing: 1px;
            color: #555555;
            margin-top: 5px;
        }
        .cert-presented {
            font-size: 20px;
            margin: 25px 0 10px 0;
            color: #444444;
        }
        .cert-name {
            font-size: 32px;
            font-weight: bold;
            color: #111111;
            text-decoration: underline;
            margin-bottom: 5px;
        }
        .cert-nim {
            font-size: 18px;
            color: #666666;
            margin-bottom: 25px;
        }
        .cert-body {
            font-size: 20px;
            line-height: 1.6;
            margin: 0 auto;
            width: 80%;
            color: #333333;
        }
        .cert-meta {
            margin-top: 50px;
            width: 100%;
        }
        .cert-table {
            width: 100%;
            margin-top: 40px;
        }
        .cert-col {
            width: 50%;
            text-align: center;
            vertical-align: top;
        }
        .cert-signature-line {
            width: 200px;
            margin: 0 auto;
            border-bottom: 2px solid #b8860b;
            margin-top: 60px;
        }
        .cert-signer {
            font-size: 16px;
            font-weight: bold;
            color: #111111;
            margin-top: 5px;
        }
        .cert-signer-title {
            font-size: 14px;
            color: #555555;
        }
    </style>
</head>
<body>
    <div class="cert-container">
        <div class="cert-header">
            <div class="cert-title">Sertifikat Kelulusan</div>
            <div class="cert-subtitle">STIKES NAULI HUSADA SIBOLGA</div>
        </div>

        <div class="cert-presented">Sertifikat ini dengan bangga diberikan kepada:</div>
        
        <div class="cert-name"><?= esc($nilai['nama_mahasiswa']) ?></div>
        <div class="cert-nim">NIM: <?= esc($nilai['nim']) ?></div>

        <div class="cert-body">
            Telah dinyatakan <strong>LULUS</strong> dalam menempuh mata kuliah:<br>
            <span style="font-size: 24px; font-weight: bold; color: #b8860b; display: block; margin: 10px 0;">
                <?= esc($nilai['nama_mk']) ?> (<?= esc($nilai['kode_mk']) ?>)
            </span>
            Pada tahun ajaran <?= esc($nilai['tahun_ajaran']) ?> (Semester <?= ucfirst(esc($nilai['semester'])) ?>) dengan memperoleh predikat kelulusan akhir <strong>Grade "<?= esc($nilai['grade']) ?>"</strong>.
        </div>

        <table class="cert-table">
            <tr>
                <td class="cert-col">
                    <div style="font-size: 16px; color: #555555;">Tanggal Terbit: <?= esc($tanggal) ?></div>
                </td>
                <td class="cert-col">
                    <div class="cert-signer-title">Mengetahui,</div>
                    <div class="cert-signature-line"></div>
                    <div class="cert-signer">Ketua Program Studi</div>
                    <div class="cert-signer-title">STIKES Nauli Husada Sibolga</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
