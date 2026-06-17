# LMS Stikes Nauli Husada

Learning Management System untuk Stikes Nauli Husada — dibangun dengan CodeIgniter 4.

## 🚀 Fitur

### Admin
- Dashboard statistik lengkap
- CRUD User (Dosen & Mahasiswa)
- CRUD Program Studi & Mata Kuliah
- Manajemen Kelas & Enrollment
- Manajemen Jadwal & Ruangan Kelas (anti-konflik)

### Dosen
- Manajemen Materi (upload file)
- Manajemen Tugas (buat & nilai)
- Manajemen Kuis (soal pilihan ganda, timer)
- Absensi Online
- Input & Rekap Nilai
- Forum Diskusi & Pengumuman
- Kelola Jadwal Perkuliahan

### Mahasiswa
- Dashboard progress & notifikasi
- Lihat & Download Materi
- Submit Tugas
- Ikut Kuis (timer + auto-submit)
- Lihat Nilai & Transkrip
- Forum Diskusi
- Jadwal Kuliah & Reminder
- Gamifikasi (Poin, Badge, Leaderboard)
- Generate Sertifikat PDF

## 🛠️ Tech Stack

- **Backend**: PHP 8.2+, CodeIgniter 4.7
- **Database**: PostgreSQL (Supabase) / MySQL
- **Frontend**: AdminLTE 3, Bootstrap 4
- **PDF**: DOMPDF
- **Hosting**: Vercel (via vercel-php runtime)

## 📋 Setup Lokal

```bash
# Clone repository
git clone https://github.com/StivenHamonanganSinurat/Learning-Management-System.git
cd Learning-Management-System

# Install dependencies
composer install

# Copy environment file
cp env .env

# Edit .env (database credentials)
# Jalankan migration
php spark migrate

# Jalankan seeder (data testing)
php spark db:seed TestDataSeeder

# Jalankan server
php spark serve
```

## 🔑 Akun Testing

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@lms.test | password123 |
| Dosen | dosen@lms.test | password123 |
| Mahasiswa | mahasiswa@lms.test | password123 |

## 📁 Struktur Project

```
lms_stikes/
├── api/                  # Vercel PHP entry point
├── app/
│   ├── Config/           # Konfigurasi (Routes, Database, Session)
│   ├── Controllers/      # Admin, Dosen, Mahasiswa controllers
│   ├── Database/
│   │   ├── Migrations/   # Schema database
│   │   └── Seeds/        # Data testing
│   ├── Helpers/          # GamifikasiHelper
│   ├── Models/           # Eloquent-style models
│   └── Views/            # AdminLTE templates
├── public/               # Assets & entry point
├── vercel.json           # Vercel deployment config
└── .env.example          # Template konfigurasi
```

## 📄 Lisensi

MIT License
