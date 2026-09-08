Tentu. Untuk kasus ini, **CodeIgniter 4 + MariaDB + SMTP + generator PDF** sudah cukup. Alurnya bisa dibuat seperti ini:

 **Peserta mengisi form → data disimpan ke MariaDB → sertifikat PDF dibuat otomatis dengan nama peserta → PDF dikirim ke email → admin bisa melihat daftar hadir.**

 Saya sarankan menggunakan:

 - **CodeIgniter 4** — framework
- **MariaDB** — database
- **Dompdf** — membuat sertifikat PDF
- **PHPMailer atau Email bawaan CI4** — mengirim email
- **Bootstrap 5** — tampilan form/admin, opsional

 ## 1\. Struktur fitur

 Nantinya aplikasi bisa memiliki:

```
/daftar-hadir
    ├── Form pendaftaran
    ├── Simpan peserta
    ├── Generate sertifikat
    └── Kirim sertifikat ke email

/admin
    ├── Login admin
    ├── Daftar peserta
    ├── Detail peserta
    ├── Download sertifikat
    └── Kirim ulang sertifikat
```

 Form peserta:

```
Nama Lengkap : [________________________]

Email        : [________________________]

Instansi     : [________________________]

Telpon       : [________________________]

              [ DAFTAR & KIRIM SERTIFIKAT ]
```

 Setelah berhasil:

```
Terima kasih, pendaftaran berhasil.

Sertifikat telah dikirim ke:
nama@email.com
```

---

 # 2\. Database MariaDB

 Buat database:

```
CREATE DATABASE daftar_hadir
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;
```

 Kemudian tabel peserta:

```
USE daftar_hadir;

CREATE TABLE peserta (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(150) NOT NULL,
    email VARCHAR(150) NOT NULL,
    instansi VARCHAR(150) NOT NULL,
    telepon VARCHAR(30) NOT NULL,
    sertifikat VARCHAR(255) DEFAULT NULL,
    created_at DATETIME NULL,
    updated_at DATETIME NULL,

    UNIQUE KEY unique_email (email)
);
```

 Saya sengaja menambahkan kolom:

```
sertifikat
```

 untuk menyimpan nama/path file sertifikat yang sudah dibuat.

 Contohnya:

```
sertifikat/
sertifikat_Budi_Santoso.pdf
```

---

 # 3\. Buat project CodeIgniter 4

 Kalau belum punya project:

```
composer create-project codeigniter4/appstarter daftar-hadir
```

 Masuk ke folder:

```
cd daftar-hadir
```

 Jalankan:

```
php spark serve
```

 Kemudian buka:

```
http://localhost:8080
```

---

 # 4\. Konfigurasi MariaDB

 Edit:

```
.env
```

 Aktifkan dan sesuaikan:

```
database.default.hostname = localhost
database.default.database = daftar_hadir
database.default.username = root
database.default.password =
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = 3306
```

 Walaupun database yang digunakan MariaDB, CodeIgniter 4 biasanya menggunakan driver `MySQLi`.

---

 # 5\. Buat Model Peserta

 Buat:

```
app/Models/PesertaModel.php
```

 Isinya:

```
<?php

namespace App\Models;

use CodeIgniter\Model;

class PesertaModel extends Model
{
    protected $table = 'peserta';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'nama_lengkap',
        'email',
        'instansi',
        'telepon',
        'sertifikat'
    ];

    protected $useTimestamps = true;
}
```

---

 # 6\. Buat Controller

 Buat:

```
app/Controllers/Peserta.php
```

 Untuk tahap awal, controller menangani:

```
GET  /daftar-hadir
POST /daftar-hadir
```

 Contoh:

```
<?php

namespace App\Controllers;

use App\Models\PesertaModel;

class Peserta extends BaseController
{
    public function index()
    {
        return view('peserta/form');
    }

    public function simpan()
    {
        $rules = [
            'nama_lengkap' => 'required|min_length[3]',
            'email'       => 'required|valid_email',
            'instansi'    => 'required',
            'telepon'     => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $model = new PesertaModel();

        $data = [
            'nama_lengkap' => $this->request->getPost('nama_lengkap'),
            'email'        => $this->request->getPost('email'),
            'instansi'     => $this->request->getPost('instansi'),
            'telepon'      => $this->request->getPost('telepon'),
        ];

        $model->insert($data);

        return redirect()
            ->to('/daftar-hadir')
            ->with('success', 'Pendaftaran berhasil.');
    }
}
```

---

 # 7\. Buat form

 Buat:

```
app/Views/peserta/form.php
```

 Contoh sederhana:

```
<!DOCTYPE html>
<html>
<head>
    <title>Daftar Hadir</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >
</head>

<body>

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-6">

            <div class="card">

                <div class="card-header">
                    <h4 class="mb-0">Daftar Hadir</h4>
                </div>

                <div class="card-body">

                    <?php if (session()->getFlashdata('success')): ?>

                        <div class="alert alert-success">
                            <?= session()->getFlashdata('success') ?>
                        </div>

                    <?php endif; ?>

                    <form action="<?= base_url('daftar-hadir') ?>" method="post">

                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label>Nama Lengkap</label>

                            <input
                                type="text"
                                name="nama_lengkap"
                                class="form-control"
                                value="<?= old('nama_lengkap') ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label>Email</label>

                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                value="<?= old('email') ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label>Instansi</label>

                            <input
                                type="text"
                                name="instansi"
                                class="form-control"
                                value="<?= old('instansi') ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label>Telpon</label>

                            <input
                                type="text"
                                name="telepon"
                                class="form-control"
                                value="<?= old('telepon') ?>"
                                required
                            >
                        </div>

                        <button class="btn btn-primary w-100">
                            Daftar & Kirim Sertifikat
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>
```

---

 # 8\. Buat routing

 Edit:

```
app/Config/Routes.php
```

 Tambahkan:

```
$routes->get('daftar-hadir', 'Peserta::index');
$routes->post('daftar-hadir', 'Peserta::simpan');
```

 Sekarang:

```
http://localhost:8080/daftar-hadir
```

 akan menampilkan form.

---

 # 9\. Generate sertifikat PDF

 Untuk sertifikat, salah satu pilihan yang praktis adalah **Dompdf**.

 Install:

```
composer require dompdf/dompdf
```

 Kemudian kita bisa membuat template HTML seperti:

```
============================================

                 SERTIFIKAT

          Diberikan kepada

              BUDI SANTOSO

       Atas partisipasinya sebagai
              PESERTA

       Seminar Teknologi 2026

        Jakarta, 8 September 2026

============================================
```

 Nama:

```
BUDI SANTOSO
```

 akan diambil otomatis dari:

```
$peserta['nama_lengkap']
```

---

 # 10\. Template sertifikat

 Misalnya buat:

```
app/Views/sertifikat/template.php
```

 Contoh:

```
<!DOCTYPE html>
<html>
<head>

<style>

@page {
    margin: 0;
}

body {
    margin: 0;
    font-family: DejaVu Sans, sans-serif;
}

.sertifikat {
    width: 1123px;
    height: 794px;

    text-align: center;

    border: 15px solid #c9a227;

    box-sizing: border-box;

    padding-top: 120px;
}

.judul {
    font-size: 48px;
    font-weight: bold;
    color: #333;
}

.diberikan {
    margin-top: 50px;
    font-size: 22px;
}

.nama {
    margin-top: 25px;
    font-size: 42px;
    font-weight: bold;
    color: #b38b00;
}

.keterangan {
    margin-top: 30px;
    font-size: 20px;
}

</style>

</head>

<body>

<div class="sertifikat">

    <div class="judul">
        SERTIFIKAT
    </div>

    <div class="diberikan">
        Diberikan kepada
    </div>

    <div class="nama">
        <?= esc($peserta['nama_lengkap']) ?>
    </div>

    <div class="keterangan">
        Atas partisipasinya sebagai peserta<br>
        dalam kegiatan Seminar Teknologi 2026
    </div>

    <br><br>

    <div>
        Jakarta, <?= date('d F Y') ?>
    </div>

</div>

</body>
</html>
```

 Nanti desainnya tentu bisa dibuat jauh lebih bagus, misalnya menggunakan background sertifikat, logo, tanda tangan, QR Code, nomor sertifikat, dan sebagainya.

---

 # 11\. Proses generate PDF

 Di controller kita bisa membuat fungsi:

```
private function generateSertifikat($peserta)
{
    $dompdf = new \Dompdf\Dompdf();

    $html = view('sertifikat/template', [
        'peserta' => $peserta
    ]);

    $dompdf->loadHtml($html);

    $dompdf->setPaper('A4', 'landscape');

    $dompdf->render();

    $filename = 'sertifikat_' .
        url_title($peserta['nama_lengkap'], '_', true) .
        '.pdf';

    $path = WRITEPATH . 'sertifikat/';

    if (!is_dir($path)) {
        mkdir($path, 0777, true);
    }

    file_put_contents(
        $path . $filename,
        $dompdf->output()
    );

    return $filename;
}
```

 Misalnya peserta:

```
Budi Santoso
```

 hasilnya:

```
writable/sertifikat/sertifikat_Budi_Santoso.pdf
```

---

 # 12\. Konfigurasi email

 Bagian ini penting.

 Jangan menggunakan fungsi `mail()` PHP biasa untuk sistem seperti ini.

 Lebih baik menggunakan SMTP.

 Misalnya menggunakan:

```
Gmail
Google Workspace
Mailgun
Brevo
SMTP hosting
```

 Untuk hosting, biasanya provider memberikan:

```
SMTP Host
SMTP Port
SMTP Username
SMTP Password
Encryption
```

 Misalnya konfigurasi CodeIgniter:

```
email.protocol = smtp
email.SMTPHost = smtp.example.com
email.SMTPUser = email@example.com
email.SMTPPass = password
email.SMTPPort = 587
email.SMTPCrypto = tls
email.mailType = html
```

 **Jangan memasukkan password SMTP langsung ke source code.**

---

 # 13\. Kirim sertifikat sebagai attachment

 Setelah PDF dibuat, kita bisa mengirim:

```
$email = service('email');

$email->setFrom(
    'email@example.com',
    'Panitia Seminar'
);

$email->setTo($peserta['email']);

$email->setSubject(
    'Sertifikat Seminar Teknologi 2026'
);

$email->setMessage(
    view('email/sertifikat', [
        'peserta' => $peserta
    ])
);

$email->attach(
    WRITEPATH . 'sertifikat/' . $filename
);

if (!$email->send()) {

    log_message(
        'error',
        $email->printDebugger(['headers'])
    );

}
```

 Dengan begitu peserta akan mendapatkan email seperti:

```
Subject:
Sertifikat Seminar Teknologi 2026

Halo Budi Santoso,

Terima kasih telah mengikuti kegiatan
Seminar Teknologi 2026.

Sertifikat Anda kami lampirkan pada email ini.

Terima kasih.

Panitia
```

 Attachment:

```
sertifikat_Budi_Santoso.pdf
```

---

 # 14\. Alur controller akhirnya

 Jadi fungsi `simpan()` nantinya kurang lebih seperti:

```
public function simpan()
{
    $rules = [
        'nama_lengkap' => 'required|min_length[3]',
        'email'       => 'required|valid_email',
        'instansi'    => 'required',
        'telepon'     => 'required'
    ];

    if (!$this->validate($rules)) {
        return redirect()
            ->back()
            ->withInput()
            ->with('errors', $this->validator->getErrors());
    }

    $model = new PesertaModel();

    $data = [
        'nama_lengkap' => $this->request->getPost('nama_lengkap'),
        'email'        => $this->request->getPost('email'),
        'instansi'     => $this->request->getPost('instansi'),
        'telepon'      => $this->request->getPost('telepon'),
    ];

    $id = $model->insert($data, true);

    $peserta = $model->find($id);

    // Generate PDF
    $filename = $this->generateSertifikat($peserta);

    // Simpan nama file sertifikat
    $model->update($id, [
        'sertifikat' => $filename
    ]);

    // Kirim email
    $this->kirimEmailSertifikat(
        $peserta,
        $filename
    );

    return redirect()
        ->to('/daftar-hadir')
        ->with(
            'success',
            'Pendaftaran berhasil. Sertifikat telah dikirim ke email Anda.'
        );
}
```

 Jadi satu kali klik:

```
SUBMIT
   ↓
Validasi
   ↓
Simpan MariaDB
   ↓
Generate PDF
   ↓
Simpan PDF
   ↓
Kirim email
   ↓
Selesai
```

---

 # 15\. Ada satu hal yang saya sarankan ditambahkan

 Jangan hanya menggunakan:

```
Nama
Email
Instansi
Telpon
```

 Untuk sistem sertifikat, sebaiknya tambahkan:

```
Nomor Sertifikat
```

 Contoh:

```
CERT/TECH/2026/00001
CERT/TECH/2026/00002
CERT/TECH/2026/00003
```

 Database:

```
ALTER TABLE peserta
ADD nomor_sertifikat VARCHAR(50) UNIQUE AFTER id;
```

 Kemudian nomor tersebut juga dicetak di sertifikat.

 Ini akan sangat berguna kalau suatu saat Anda ingin membuat halaman:

```
https://website.com/verifikasi/CERT-TECH-2026-00001
```

 Peserta atau HRD bisa scan QR Code pada sertifikat dan melihat:

```
Sertifikat Valid

Nama       : Budi Santoso
Instansi   : PT ABC
Kegiatan   : Seminar Teknologi 2026
Tanggal    : 8 September 2026
Status     : VALID
```

 Menurut saya fitur **verifikasi sertifikat + QR Code** sangat layak dimasukkan sejak awal.

---

 # 16\. Struktur project yang saya rekomendasikan

 Akhirnya struktur project menjadi kira-kira:

```
app/
│
├── Controllers/
│   ├── Peserta.php
│   └── Admin.php
│
├── Models/
│   └── PesertaModel.php
│
├── Views/
│   ├── peserta/
│   │   └── form.php
│   │
│   ├── sertifikat/
│   │   └── template.php
│   │
│   ├── email/
│   │   └── sertifikat.php
│   │
│   └── admin/
│       ├── login.php
│       ├── dashboard.php
│       └── peserta.php
│
└── Config/
    └── Routes.php

writable/
│
└── sertifikat/
    ├── sertifikat_Budi_Santoso.pdf
    ├── sertifikat_Ahmad.pdf
    └── ...
```

---

 ## 17\. Fitur yang sebaiknya dibuat

 Kalau ini akan digunakan untuk acara sungguhan, saya akan membuatnya dengan fitur:

 - ✅ Form daftar hadir
- ✅ Validasi nama, email, instansi, telepon
- ✅ Anti-submit berulang
- ✅ MariaDB
- ✅ Generate sertifikat PDF otomatis
- ✅ Nama otomatis masuk ke sertifikat
- ✅ Nomor sertifikat otomatis
- ✅ QR Code untuk verifikasi
- ✅ Email otomatis
- ✅ Sertifikat sebagai attachment
- ✅ Halaman sukses setelah daftar
- ✅ Admin login
- ✅ Dashboard jumlah peserta
- ✅ Daftar peserta
- ✅ Search peserta
- ✅ Download sertifikat
- ✅ Kirim ulang sertifikat
- ✅ Export Excel/CSV
- ✅ Halaman verifikasi sertifikat
- ✅ Log status email (`terkirim/gagal`)
- ✅ Tampilan mobile-friendly

 **Kalau Anda mau, saya bisa lanjutkan dengan membuatkan tutorial lengkap dari nol sampai jadi**, termasuk **struktur folder CI4, SQL MariaDB, Model, Controller, Routes, Bootstrap form, template sertifikat PDF yang bagus, SMTP, pengiriman email attachment, nomor sertifikat + QR Code, dan halaman admin**. Jadi Anda tinggal mengikuti langkah 1 → 2 → 3 dan copy-paste kodenya.
