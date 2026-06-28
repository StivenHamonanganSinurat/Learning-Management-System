# LMS Stikes Nauli Husada

Learning Management System untuk Stikes Nauli Husada — dibangun dengan PHP 8.2+ dan framework CodeIgniter 4.

---

## 🚀 Fitur Sistem (Berdasarkan Role)

### 1. Admin (Pengelola)
* **Dashboard**: Statistik ringkas jumlah mahasiswa, dosen, kelas, prodi, dan mata kuliah.
* **Manajemen User**: CRUD lengkap untuk Dosen dan Mahasiswa.
* **Manajemen Akademik**: CRUD Program Studi, Mata Kuliah, Kelas, dan Enrollment Mahasiswa ke kelas.
* **Manajemen Jadwal & Ruangan**: Penjadwalan kelas pintar dengan deteksi tabrakan waktu/ruangan secara otomatis.

### 2. Dosen (Pengajar)
* **Dashboard**: Menampilkan statistik kelas yang diajar.
* **Materi**: Unggah materi pembelajaran berupa file (PDF, Dokumen), video, atau artikel/teks.
* **Tugas**: Pembuatan tugas, batas waktu pengerjaan, dan penilaian tugas mahasiswa.
* **Kuis**: Pembuatan bank soal pilihan ganda, durasi kuis, nilai otomatis, dan batasan pengerjaan (*quiz attempt*).
* **Absensi**: Input absensi kehadiran kelas per mahasiswa.
* **Rekap Nilai**: Input & kalkulasi otomatis nilai UTS, UAS, tugas, dan kuis.
* **Komunikasi**: Forum diskusi kelas & pengumuman resmi.

### 3. Mahasiswa (Belajar)
* **Dashboard**: Progress bar belajar, badge gamifikasi, poin, dan reminder kelas terdekat.
* **Materi & Tugas**: Unduh materi dan kumpulkan tugas sebelum batas waktu.
* **Kuis**: Mengerjakan kuis dengan *countdown timer* real-time.
* **Absensi & Nilai**: Melihat histori kehadiran dan transkrip nilai/UTS/UAS.
* **Gamifikasi**: Fitur Leaderboard kelas, pengumpulan Poin, dan klaim Badge prestasi.
* **Sertifikat**: Unduh Sertifikat Kelulusan otomatis (PDF) untuk mata kuliah dengan Grade A/B/C.

---

## 🛠️ Tech Stack & Prasyarat Sistem

* **Bahasa & Framework**: PHP 8.2+ & CodeIgniter 4.7
* **Database**: MySQL / MariaDB (XAMPP phpMyAdmin)
* **UI/UX**: AdminLTE 3 & Bootstrap 4
* **Generator PDF**: DOMPDF Library

---

## 💻 Panduan Instalasi Lokal (XAMPP MySQL)

Ikuti langkah-langkah di bawah ini untuk menjalankan aplikasi di komputer lokal:

### Langkah 1: Persiapan Database
1. Aktifkan **Apache** dan **MySQL** di XAMPP Control Panel Anda.
2. Buka browser dan akses **`http://localhost/phpmyadmin/`**.
3. Buat database baru bernama **`lms_stikes`**.

### Langkah 2: Konfigurasi Kode Sumber
1. Salin folder proyek `lms_stikes` ke dalam direktori **`C:\xampp\htdocs\`** Anda.
2. Buka berkas [`.env`](file:///C:/xampp/htdocs/lms_stikes/.env) di root proyek dan sesuaikan konfigurasi database Anda seperti di bawah ini:
   ```env
   CI_ENVIRONMENT = development
   app.baseURL = 'http://localhost/lms_stikes/public/'

   database.default.hostname = localhost
   database.default.database = lms_stikes
   database.default.username = root
   database.default.password = 
   database.default.DBDriver = MySQLi
   database.default.port = 3306
   ```

### Langkah 3: Import Database & Install Dependensi
1. Buka **phpMyAdmin** di browser Anda, klik database **`lms_stikes`** yang telah dibuat.
2. Pilih tab **Import** di bagian atas menu phpMyAdmin.
3. Klik **Choose File** / **Pilih Berkas**, lalu pilih berkas **`lms_stikes.sql`** yang terletak di root direktori proyek ini.
4. Gulir ke bawah dan klik tombol **Import** / **Kirim**. Seluruh struktur tabel dan data sampel pengujian akan terisi secara otomatis.
5. Buka Command Prompt atau PowerShell di folder proyek (`C:\xampp\htdocs\lms_stikes`), lalu jalankan perintah berikut untuk menginstal pustaka yang dibutuhkan:
   ```bash
   composer install
   ```

### Langkah 4: Menjalankan Aplikasi
Akses aplikasi melalui browser Anda dengan URL:
👉 **`http://localhost/lms_stikes/public/`**

---

## 🔑 Akun & Detail Login Pengujian

Gunakan akun di bawah ini untuk menguji masing-masing role:

| No | Role | Alamat Email | Password | Hak Akses Utama |
|---|---|---|---|---|
| 1 | **Admin** | `admin@lms.test` | `password123` | Mengelola User, Prodi, Kelas, & Ruangan |
| 2 | **Dosen** | `dosen@lms.test` | `password123` | Mengajar, Menilai, Mengabsen, Mengelola Kuis |
| 3 | **Mahasiswa** | `mahasiswa@lms.test` | `password123` | Belajar, Absen, Tugas, Kuis, Gamifikasi, Unduh Sertifikat |

---

## 📁 Struktur Direktori Utama

```
lms_stikes/
├── app/
│   ├── Config/           # Konfigurasi aplikasi (Routes, Database, Session)
│   ├── Controllers/      # Controller Logika Bisnis (Admin, Dosen, Mahasiswa)
│   ├── Database/
│   │   ├── Migrations/   # Skema Tabel MySQL
│   │   └── Seeds/        # Data Sampel Pengujian (TestDataSeeder)
│   ├── Helpers/          # GamifikasiHelper (Sistem Poin & Badge)
│   ├── Models/           # Model data (User, Kelas, Tugas, Nilai, dll)
│   └── Views/            # Tampilan Antarmuka (AdminLTE / Blade-like PHP)
├── public/               # File aset publik (CSS, JS, Gambar, Vendor)
├── writable/             # Cache, Session lokal, dan Upload Berkas (Materi/Tugas)
└── .env                  # Berkas konfigurasi rahasia lokal
```
