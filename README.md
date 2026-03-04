# Calakan — Catatan Amaliyah Kegiatan Ramadan

**Aplikasi Buku Ramadhan Digital SMKN 1 Ciamis**

[![GitHub](https://img.shields.io/badge/GitHub-smkn--1--ciamis--project%2Fbuku--ramadhan--2025-181717?logo=github)](https://github.com/smkn-1-ciamis-project/buku-ramadhan-2025)
[![License: MIT](https://img.shields.io/badge/License-MIT-green)](LICENSE)
[![Version](https://img.shields.io/badge/version-v2.6.2-blue)](TEAM.md)

Calakan adalah aplikasi web full-stack untuk pencatatan kegiatan ibadah Ramadhan siswa secara digital. Dibangun dengan Laravel 12 dan Filament v3, dilengkapi dashboard mobile-first untuk siswa, sistem verifikasi dua tingkat (Guru dan Kesiswaan), serta aplikasi Android berupa WebView wrapper.

> Versi saat ini: **v2.6.2** — 3 Maret 2026
> Repository: [github.com/smkn-1-ciamis-project/buku-ramadhan-2025](https://github.com/smkn-1-ciamis-project/buku-ramadhan-2025)

---

## Daftar Isi

- [Fitur Utama](#fitur-utama)
- [Tech Stack](#tech-stack)
- [Arsitektur](#arsitektur)
- [Sistem Role & Panel](#sistem-role--panel)
- [Alur Verifikasi](#alur-verifikasi)
- [Database Schema](#database-schema)
- [API Endpoints](#api-endpoints)
- [Repository Pattern](#repository-pattern)
- [Instalasi & Setup](#instalasi--setup)
- [Konfigurasi](#konfigurasi)
- [Android App](#android-app)
- [Tim Pengembang](#tim-pengembang)
- [Riwayat Versi](#riwayat-versi)
- [Lisensi](#lisensi)

---

## Fitur Utama

### Untuk Siswa

- **Formulir Harian Ramadhan** — Pengisian catatan ibadah harian (hari ke-1 s/d 30)
- **Checkin Sholat** — Pencatatan sholat wajib (Subuh, Dzuhur, Ashar, Maghrib, Isya, Tarawih) dan sunnah (Rawatib, Tahajud, Dhuha)
- **Al-Quran Digital** — 114 surah lengkap dengan terjemahan, pilihan 9 qari, putar otomatis, dan pencarian surah/ayat
- **Jadwal Sholat & Arah Kiblat** — Jadwal imsakiyah dan waktu sholat harian
- **Dashboard Mobile-First** — Antarmuka responsif yang dioptimalkan untuk perangkat mobile
- **Dukungan Multi-Agama** — Dashboard terpisah untuk siswa Muslim dan non-Muslim (Kristen, Katolik, Hindu, Buddha, Konghucu)
- **Riwayat Pembaruan** — Changelog versi dapat diakses langsung dari aplikasi

### Untuk Guru (Wali Kelas)

- **Dashboard Kelas** — Monitoring progress pengisian formulir per siswa
- **Verifikasi Formulir** — Approve/reject formulir harian siswa dengan catatan
- **Rekap Siswa** — Ringkasan kepatuhan pengisian per siswa
- **Ekspor Data** — Export rekap siswa ke format spreadsheet

### Untuk Kesiswaan

- **Dashboard Sekolah** — Statistik keseluruhan submission, kepatuhan, dan verifikasi
- **Validasi Formulir** — Validasi tingkat kedua setelah verifikasi guru
- **Manajemen Data** — Kelola data guru, siswa, kelas, dan pengaturan formulir
- **Ekspor Validasi** — Export laporan validasi per kelas

### Untuk Superadmin

- **Dashboard Admin** — Overview lengkap seluruh data sekolah
- **Manajemen Pengguna** — CRUD untuk guru, siswa, kesiswaan, dan role
- **Pengaturan Formulir** — Konfigurasi dinamis field formulir per agama
- **Pengaturan API** — Konfigurasi URL API eksternal (jadwal sholat, arah kiblat)
- **Jadwal Ramadhan** — Pengaturan tanggal awal dan akhir Ramadhan
- **Activity Log** — Log aktivitas pengguna lengkap dengan informasi perangkat

---

## Tech Stack

| Komponen       | Teknologi                      |
| -------------- | ------------------------------ |
| Backend        | PHP 8.2+, Laravel 12           |
| Admin Panel    | Filament v3                    |
| Frontend Siswa | Blade, Alpine.js, Tailwind CSS |
| Database       | MySQL / MariaDB                |
| Cache          | Redis (via Predis)             |
| PDF Export     | barryvdh/laravel-dompdf        |
| Spreadsheet    | phpoffice/phpspreadsheet       |
| Android        | Java 17, WebView, Gradle 8.5   |

---

## Arsitektur

Aplikasi mengikuti pola **Controller > Service > Repository > Model** untuk memisahkan tanggung jawab dan memudahkan testing.

```
app/
├── Filament/               # 4 Panel admin (Siswa, Guru, Kesiswaan, Superadmin)
│   ├── Guru/               # Panel wali kelas
│   ├── Kesiswaan/          # Panel kesiswaan/kepala sekolah
│   ├── Siswa/              # Panel autentikasi siswa
│   └── Superadmin/         # Panel admin utama
├── Http/
│   ├── Controllers/        # API & page controllers
│   └── Middleware/          # Auth, rate limit, single session
├── Models/                 # Eloquent models (UUID-based)
├── Repositories/
│   ├── Contracts/          # Interface definitions
│   ├── Eloquent/           # Database implementations
│   └── Api/                # External API implementations
├── Services/               # Business logic layer
├── Traits/                 # Reusable traits (UUID, password modal)
├── Listeners/              # Event listeners (auth logging)
└── Providers/              # Service & Filament providers
```

### Optimisasi Database

- **Batch Queries** — Semua dashboard menggunakan `DashboardStatsService` dengan query batch (`JOIN` + `GROUP BY`) untuk menghindari N+1
- **Redis Caching** — Cache untuk data surah (24 jam), ayat (12 jam), settings (1 jam), dan submission (3 menit)
- **Performance Indexes** — Index pada kolom yang sering di-query (`user_id`, `kelas_id`, `status`, `hari_ke`, `created_at`)

---

## Sistem Role & Panel

Setiap role memiliki panel Filament terpisah dengan akses yang ketat:

| Panel         | Role                      | Akses                                                |
| ------------- | ------------------------- | ---------------------------------------------------- |
| `/siswa`      | Siswa                     | Dashboard ibadah, formulir, checkin sholat, Al-Quran |
| `/guru`       | Guru (Wali Kelas)         | Verifikasi, rekap siswa, monitoring kelas            |
| `/kesiswaan`  | Kesiswaan, Kepala Sekolah | Validasi, manajemen data, ekspor laporan             |
| `/superadmin` | Super Admin               | Full akses: semua fitur + pengaturan sistem          |

Fitur keamanan:

- **Single Session Enforcement** — Satu akun hanya bisa login di satu perangkat
- **Forced Password Change** — Admin bisa memaksa user mengganti password saat login pertama
- **Rate Limiting** — Throttle pada semua endpoint API
- **Activity Logging** — Pencatatan login, logout, dan aktivitas penting dengan info IP dan perangkat

---

## Alur Verifikasi

Formulir harian siswa melalui dua tingkat persetujuan:

```
Siswa mengisi formulir
        |
        v
  [Status: pending]
        |
        v
Guru wali kelas memverifikasi
        |
   +---------+---------+
   |                   |
   v                   v
[verified]         [rejected]
   |               (+ catatan guru)
   v
Kesiswaan memvalidasi
   |
   +---------+---------+
   |                   |
   v                   v
[validated]        [rejected]
                   (+ catatan kesiswaan)
```

---

## Database Schema

Tabel-tabel utama:

| Tabel              | Deskripsi                                                                                            |
| ------------------ | ---------------------------------------------------------------------------------------------------- |
| `users`            | Data pengguna (UUID), dengan field `nisn`, `agama`, `kelas_id`, `jenis_kelamin`, `active_session_id` |
| `role_users`       | Definisi role (Siswa, Guru, Kesiswaan, Super Admin) dengan pengaturan visibilitas menu               |
| `kelas`            | Data kelas/rombel dengan `wali_id` (relasi ke guru)                                                  |
| `form_submissions` | Formulir harian Ramadhan per siswa per hari, dengan field verifikasi guru dan validasi kesiswaan     |
| `prayer_checkins`  | Checkin sholat harian per siswa (wajib + sunnah)                                                     |
| `form_settings`    | Konfigurasi dinamis field formulir per agama                                                         |
| `app_settings`     | Pengaturan aplikasi (URL API, jadwal Ramadhan, dsb)                                                  |
| `activity_logs`    | Log aktivitas pengguna dengan metadata perangkat                                                     |

Relasi utama:

- `User` belongsTo `Kelas` (siswa → kelas)
- `Kelas` belongsTo `User` sebagai wali (kelas → guru wali)
- `Kelas` hasMany `User` sebagai siswa
- `Kelas` hasManyThrough `FormSubmission` via `User`
- `FormSubmission` belongsTo `User` (siswa), `verifier` (guru), `validator` (kesiswaan)

---

## API Endpoints

Semua endpoint API menggunakan session-based authentication dengan throttle middleware.

### Formulir

| Method | Endpoint                 | Deskripsi                     |
| ------ | ------------------------ | ----------------------------- |
| `GET`  | `/api/formulir`          | Daftar formulir siswa         |
| `GET`  | `/api/formulir/{hariKe}` | Detail formulir hari tertentu |
| `POST` | `/api/formulir`          | Submit formulir harian        |

### Checkin Sholat

| Method | Endpoint                              | Deskripsi                          |
| ------ | ------------------------------------- | ---------------------------------- |
| `GET`  | `/api/prayer-checkins/today`          | Checkin sholat hari ini            |
| `GET`  | `/api/prayer-checkins/first-unfilled` | Sholat berikutnya yang belum diisi |
| `GET`  | `/api/prayer-checkins/date/{date}`    | Checkin pada tanggal tertentu      |
| `POST` | `/api/prayer-checkins`                | Submit checkin sholat              |

### Al-Quran

| Method | Endpoint                    | Deskripsi                                |
| ------ | --------------------------- | ---------------------------------------- |
| `GET`  | `/api/quran/surahs`         | Daftar 114 surah (cached 24 jam)         |
| `GET`  | `/api/quran/surah/{number}` | Detail surah dengan ayat (cached 12 jam) |
| `GET`  | `/api/quran/reciters`       | Daftar 9 qari tersedia                   |

### Lainnya

| Method | Endpoint                     | Deskripsi                     |
| ------ | ---------------------------- | ----------------------------- |
| `GET`  | `/api/form-settings/{agama}` | Pengaturan formulir per agama |
| `GET`  | `/api/app-settings`          | Pengaturan aplikasi           |
| `POST` | `/api/change-password`       | Ganti password                |

### Export (Guru & Kesiswaan)

| Method | Endpoint                               | Deskripsi                              |
| ------ | -------------------------------------- | -------------------------------------- |
| `GET`  | `/guru-exports/rekap-siswa`            | Rekap semua siswa (Guru)               |
| `GET`  | `/guru-exports/rekap-siswa/{siswa}`    | Rekap detail per siswa (Guru)          |
| `GET`  | `/kesiswaan-exports/validasi/{kelas?}` | Laporan validasi per kelas (Kesiswaan) |

---

## Repository Pattern

Seluruh akses data melalui repository interface yang di-bind di `AppServiceProvider`:

| Interface                           | Implementation                     | Deskripsi                             |
| ----------------------------------- | ---------------------------------- | ------------------------------------- |
| `FormSubmissionRepositoryInterface` | `EloquentFormSubmissionRepository` | CRUD formulir dengan caching          |
| `PrayerCheckinRepositoryInterface`  | `EloquentPrayerCheckinRepository`  | Batch upsert checkin sholat           |
| `FormSettingRepositoryInterface`    | `EloquentFormSettingRepository`    | Pengaturan formulir dinamis           |
| `UserRepositoryInterface`           | `EloquentUserRepository`           | Data pengguna & profil                |
| `QuranRepositoryInterface`          | `ApiQuranRepository`               | Proxy API Al-Quran dengan Redis cache |
| `ActivityLogRepositoryInterface`    | `EloquentActivityLogRepository`    | Pencatatan activity log               |
| `KelasRepositoryInterface`          | `EloquentKelasRepository`          | Data kelas dengan caching             |

Service layer:

| Service                  | Fungsi                               |
| ------------------------ | ------------------------------------ |
| `FormSubmissionService`  | Logika bisnis formulir               |
| `PrayerCheckinService`   | Logika bisnis checkin sholat         |
| `UserService`            | Logika bisnis pengguna               |
| `DashboardStatsService`  | Statistik dashboard batch (N+1 free) |
| `ImportService`          | Import data dari spreadsheet         |
| `KesiswaanExportService` | Export laporan kesiswaan             |
| `RekapExportService`     | Export rekap siswa                   |
| `TemplateService`        | Template formulir per agama          |

---

## Instalasi & Setup

### Prasyarat

- PHP >= 8.2
- Composer
- MySQL / MariaDB
- Redis
- Node.js (untuk asset compilation)

### Langkah Instalasi

```bash
# 1. Clone repository
git clone https://github.com/smkn-1-ciamis-project/buku-ramadhan-2025.git buku-ramadhan
cd buku-ramadhan

# 2. Install dependencies
composer install

# 3. Konfigurasi environment
cp .env.example .env
php artisan key:generate

# 4. Konfigurasi database di .env
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=buku_ramadhan
# DB_USERNAME=root
# DB_PASSWORD=

# 5. Konfigurasi Redis di .env
# CACHE_STORE=redis
# REDIS_HOST=127.0.0.1
# REDIS_PORT=6379

# 6. Jalankan migrasi dan seeder
php artisan migrate --seed

# 7. Buat storage link
php artisan storage:link

# 8. Jalankan server
php artisan serve
```

### Akses Panel

Setelah seeding, akses panel melalui:

| Panel      | URL                                |
| ---------- | ---------------------------------- |
| Siswa      | `http://localhost:8000/siswa`      |
| Guru       | `http://localhost:8000/guru`       |
| Kesiswaan  | `http://localhost:8000/kesiswaan`  |
| Superadmin | `http://localhost:8000/superadmin` |

---

## Konfigurasi

### Environment Variables

| Variable         | Deskripsi       | Default     |
| ---------------- | --------------- | ----------- |
| `APP_NAME`       | Nama aplikasi   | `Calakan`   |
| `DB_CONNECTION`  | Driver database | `mysql`     |
| `CACHE_STORE`    | Driver cache    | `redis`     |
| `SESSION_DRIVER` | Driver session  | `redis`     |
| `REDIS_HOST`     | Host Redis      | `127.0.0.1` |

### Pengaturan Aplikasi (via Superadmin Panel)

- **API Settings** — URL endpoint untuk jadwal sholat, arah kiblat, dan data Al-Quran
- **Jadwal Ramadhan** — Tanggal awal dan akhir Ramadhan (menentukan hari ke-1 s/d 30)
- **Form Settings** — Konfigurasi field formulir per agama (Islam, Kristen, Katolik, Hindu, Buddha, Konghucu)
- **Menu Visibility** — Pengaturan visibilitas menu per role

---

## Android App

Direktori `android-app/` berisi WebView wrapper untuk distribusi via APK.

| Spesifikasi  | Detail                   |
| ------------ | ------------------------ |
| Package Name | `id.smkn1ciamis.calakan` |
| Versi        | 1.1.0                    |
| Min SDK      | 24 (Android 7.0)         |
| Target SDK   | 34 (Android 14)          |
| Build Tool   | Gradle 8.5 + AGP 8.2.2   |
| Java         | 17                       |

Fitur native:

- Pull-to-refresh
- Splash screen
- Upload file (galeri + kamera)
- Halaman error kustom
- Navigasi tombol back
- Adaptive icons
- Cookie persistence

Untuk build APK, lihat [android-app/README.md](android-app/README.md).

---

## Tim Pengembang

Calakan dibangun dari nol oleh tim siswa SMKN 1 Ciamis.

| Nama                      | Role                                | GitHub                                            |
| ------------------------- | ----------------------------------- | ------------------------------------------------- |
| **Muhammad Fikri Haikal** | Project Lead & Full Stack Developer | [fikrihaikal17](https://github.com/fikrihaikal17) |
| **Galuh Surya Putra**     | Frontend Developer                  | [Ptragaluhhh28](https://github.com/Ptragaluhhh28) |

Detail lengkap profil dan kontak tim: **[TEAM.md](TEAM.md)**

---

## Riwayat Versi

### v2.6.2 — 3 Maret 2026

- Al-Quran digital lengkap 114 surah dengan terjemahan
- Pilihan 9 qari/pembaca Al-Quran
- Putar semua ayat otomatis, lanjut ke surah berikutnya
- Pencarian surah dan pencarian ayat dalam surah
- Menu Jadwal Sholat dan Arah Kiblat digabung menjadi satu
- Peningkatan performa dan kecepatan aplikasi

### v2.5.7 — 2 Maret 2026

- Versi aplikasi dapat diklik untuk melihat riwayat pembaruan
- Peningkatan performa dashboard
- Keamanan login ditingkatkan
- Perbaikan bug minor

### v2.5.0 — 26 Februari 2026

- Perbaikan layout dashboard mobile
- Data dan profil siswa lebih akurat
- Perbaikan bug dan stabilitas

### v2.0 — 22 Februari 2026

- Peluncuran v2.0
- Jadwal sholat dan imsakiyah otomatis
- Kuis harian dan ayat Al-Quran harian
- Absensi harian dan formulir Ramadhan
- Dukungan siswa non-Muslim (4 agama)

---

## Lisensi

Proyek ini dilisensikan di bawah **MIT License** — lihat file **[LICENSE](LICENSE)** untuk detail lengkap.

Copyright (c) 2026 SMKN 1 Ciamis.
