# 🔐 Informasi Login - Buku Ramadhan 2025

## 🎯 Halaman Login

Aplikasi ini memiliki **4 halaman login terpisah** untuk setiap role:

### 1. 👨‍🎓 Login Siswa (Index/Default)

- **URL**: `http://localhost:8000/` atau `http://localhost:8000/siswa/login`
- **Warna**: Biru (Blue)
- **Credentials**:
    - **NISN**: `0012345678` (10 digit angka)
    - **Password**: `siswa123`
- **Alternatif Login Siswa Lain**:
    - NISN: `0012345679` | Password: `siswa123` (Siswa Demo 2)
    - NISN: `0012345680` | Password: `siswa123` (Siswa Demo 3)

### 2. 👨‍🏫 Login Guru

- **URL**: `http://localhost:8000/portal-guru-smkn1/login`
- **Warna**: Hijau (Green)
- **Credentials**:
    - Email: `irma.sukmarini@smkn1ciamis.sch.id`
    - Password: `guru123`
- **Credentials Lama (Demo)**:
    - Email: `guru@smkn1ciamis.sch.id`
    - Password: `guru123`

### 3. 🔧 Login Superadmin

- **URL**: `http://localhost:8000/portal-admin-smkn1/login`
- **Warna**: Ungu (Purple)
- **Credentials**:
    - Email: `superadmin@smkn1ciamis.sch.id`
    - Password: `superadmin123`

### 4. 🏫 Login Kesiswaan/Kepala Sekolah

- **URL**: `http://localhost:8000/portal-kesiswaan-smkn1/login`
- **Warna**: Oranye (Orange)
- **Credentials**:
    - Email: `kesiswaan@smkn1ciamis.sch.id`
    - Password: `kesiswaan123`

---

## 📋 Detail Akun Siswa

| Nama         | Email                     | NISN       | Password |
| ------------ | ------------------------- | ---------- | -------- |
| Siswa Demo 1 | siswa1@smkn1ciamis.sch.id | 0012345678 | siswa123 |
| Siswa Demo 2 | siswa2@smkn1ciamis.sch.id | 0012345679 | siswa123 |
| Siswa Demo 3 | siswa3@smkn1ciamis.sch.id | 0012345680 | siswa123 |

**Note**: Login siswa menggunakan **NISN** (10 digit), bukan email.

---

## 📋 Roles yang Tersedia

1. **Siswa** - Login dengan NISN (10 digit angka)
2. **Guru** - Login dengan email
3. **Superadmin** - Login dengan email
4. **Kesiswaan** - Login dengan email

---

## ⚙️ Setup Database

Jika database belum dibuat, jalankan perintah berikut:

```bash
# Buat database
mysql -u root -e "CREATE DATABASE IF NOT EXISTS buku_ramadhan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# Jalankan migration dan seeder
php artisan migrate:fresh --seed
```

---

## 🚀 Menjalankan Aplikasi

```bash
# Development server (Laravel)
php artisan serve

# Development server (Vite untuk assets)
npm run dev

# Akses aplikasi
http://localhost:8000
```

---

## 📝 Catatan Penting

- **Login Siswa**: Menggunakan NISN (10 digit angka), contoh: `0012345678`
- **Login Lainnya**: Menggunakan email
- Registrasi dinonaktifkan untuk semua panel
- Setiap role hanya bisa mengakses panel mereka masing-masing
- Panel Siswa adalah halaman default (index)
- Setiap panel memiliki warna tema yang berbeda untuk membedakan

---

## 🔄 Reset Password

Untuk mengubah password user, gunakan tinker:

```bash
php artisan tinker

# Contoh mengubah password siswa
$user = User::where('nisn', '0012345678')->first();
$user->password = Hash::make('password_baru');
$user->save();

# Contoh mengubah password guru/admin
$user = User::where('email', 'guru@smkn1ciamis.sch.id')->first();
$user->password = Hash::make('password_baru');
$user->save();
```

---

## 🛠️ Tech Stack

- **Laravel**: 12.52.0
- **Filament**: 3.3.48
- **PHP**: 8.2.12
- **MySQL**: 8.0+
- **Livewire**: 3.7.10
- **Vite**: 5.1.4

---

## 🔧 Troubleshooting

### Error: Column 'nisn' not found

```bash
# Jalankan migration untuk menambah kolom NISN
php artisan migrate

# Update data siswa dengan NISN
php artisan db:seed --class=UserSeeder
```

### Error: Vite manifest not found

```bash
# Install dependencies
npm install

# Jalankan Vite dev server
npm run dev
```

### Error: No application encryption key

```bash
# Generate app key
php artisan key:generate
```
