# 🔐 Informasi Login - Buku Ramadhan 2025

## 🎯 Halaman Login

Aplikasi ini memiliki **4 halaman login terpisah** untuk setiap role:

### 1. 👨‍🎓 Login Siswa (Index/Default)
- **URL**: `http://localhost/` atau `http://localhost/siswa/login`
- **Warna**: Biru (Blue)
- **Credentials**:
  - Email: `siswa@smkn1ciamis.sch.id`
  - Password: `siswa123`

### 2. 👨‍🏫 Login Guru
- **URL**: `http://localhost/guru/login`
- **Warna**: Hijau (Green)
- **Credentials**:
  - Email: `guru@smkn1ciamis.sch.id`
  - Password: `guru123`

### 3. 🔧 Login Superadmin
- **URL**: `http://localhost/superadmin/login`
- **Warna**: Ungu (Purple)
- **Credentials**:
  - Email: `superadmin@smkn1ciamis.sch.id`
  - Password: `superadmin123`

### 4. 🏫 Login Kesiswaan/Kepala Sekolah
- **URL**: `http://localhost/kesiswaan/login`
- **Warna**: Oranye (Orange)
- **Credentials**:
  - Email: `kesiswaan@smkn1ciamis.sch.id`
  - Password: `kesiswaan123`

---

## 📋 Roles yang Tersedia

1. **Siswa** - Untuk akses siswa
2. **Guru** - Untuk akses guru
3. **Superadmin** - Untuk administrator sistem
4. **Kesiswaan** - Untuk staff kesiswaan dan kepala sekolah

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
# Development server
php artisan serve

# Akses aplikasi
http://localhost:8000
```

---

## 📝 Catatan

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
$user = User::where('email', 'siswa@smkn1ciamis.sch.id')->first();
$user->password = Hash::make('password_baru');
$user->save();
```

---

## 🛠️ Tech Stack

- **Laravel**: 12.52.0
- **Filament**: 3.3.48
- **PHP**: 8.2+
- **MySQL**: 8.0+
- **Livewire**: 3.7.10
