# Calakan - Android App

Aplikasi Android WebView wrapper untuk **Calakan** (Catatan Amaliyah Kegiatan Ramadan) SMKN 1 Ciamis.

## Informasi Aplikasi

| Property     | Value                                         |
| ------------ | --------------------------------------------- |
| Package      | `id.smkn1ciamis.calakan`                        |
| Min SDK      | 24 (Android 7.0 Nougat)                       |
| Target SDK   | 34 (Android 14)                               |
| URL Produksi | `https://ramadhan.smkn1ciamis.id/siswa/login` |
| Bahasa       | Java 17                                       |
| Build System | Gradle 8.5 + AGP 8.2.2                        |

## Fitur

- **WebView** dengan JavaScript, DOM Storage, dan Cookie support
- **Pull-to-refresh** (SwipeRefreshLayout)
- **Splash screen** dengan animasi fade-in
- **Upload file** dari gallery dan kamera
- **Error handling** dengan halaman custom (tidak ada koneksi / gagal memuat)
- **Back button** navigasi di WebView, konfirmasi keluar aplikasi
- **Adaptive icon** untuk Android 8.0+
- **Network security config** untuk development dan production
- **ProGuard / R8** minification untuk release build

## Prasyarat

- **Android Studio** Hedgehog (2023.1.1) atau lebih baru
- **Java Development Kit (JDK) 17**
- **Android SDK 34** terinstall via SDK Manager
- **Git** (opsional, untuk clone)

## Cara Build

### 1. Buka di Android Studio

1. Buka Android Studio
2. Pilih **File → Open**
3. Navigasi ke folder `android-app/` dan klik **OK**
4. Tunggu Gradle sync selesai (akan download dependencies otomatis)

### 2. Build Debug APK

**Via Android Studio:**

- Klik **Build → Build Bundle(s) / APK(s) → Build APK(s)**
- APK akan tersedia di: `app/build/outputs/apk/debug/app-debug.apk`

**Via Terminal:**

```bash
cd android-app
./gradlew assembleDebug
```

Output: `app/build/outputs/apk/debug/app-debug.apk`

### 3. Build Release APK (Signed)

#### a. Buat Keystore

```bash
keytool -genkey -v -keystore calakan-release-key.jks -keyalg RSA -keysize 2048 -validity 10000 -alias calakan
```

#### b. Buat file `local.properties` (jangan commit ke git!)

```properties
sdk.dir=C\:\\Users\\NAMA_USER\\AppData\\Local\\Android\\Sdk
storeFile=../calakan-release-key.jks
storePassword=password_anda
keyAlias=calakan
keyPassword=password_anda
```

#### c. Update `app/build.gradle` untuk signing config

Tambahkan di dalam block `android {}`:

```groovy
signingConfigs {
    release {
        def props = new Properties()
        def localFile = rootProject.file("local.properties")
        if (localFile.exists()) {
            props.load(localFile.newDataInputStream())
            storeFile file(props['storeFile'] ?: '')
            storePassword props['storePassword'] ?: ''
            keyAlias props['keyAlias'] ?: ''
            keyPassword props['keyPassword'] ?: ''
        }
    }
}

buildTypes {
    release {
        signingConfig signingConfigs.release
        minifyEnabled true
        shrinkResources true
        proguardFiles getDefaultProguardFile('proguard-android-optimize.txt'), 'proguard-rules.pro'
    }
}
```

#### d. Build Release APK

```bash
./gradlew assembleRelease
```

Output: `app/build/outputs/apk/release/app-release.apk`

### 4. Install di Device

**Via ADB:**

```bash
adb install app/build/outputs/apk/debug/app-debug.apk
```

**Via Android Studio:**

- Sambungkan device via USB (aktifkan Developer Options & USB Debugging)
- Klik tombol **Run ▶** di toolbar

## Struktur Proyek

```
android-app/
├── .gitignore
├── build.gradle                    # Root build file
├── gradle.properties               # Gradle settings
├── settings.gradle                 # Project settings
├── gradle/wrapper/
│   └── gradle-wrapper.properties   # Gradle wrapper config
├── app/
│   ├── build.gradle                # App build config
│   ├── proguard-rules.pro          # ProGuard rules
│   └── src/main/
│       ├── AndroidManifest.xml     # App manifest
│       ├── java/id/smkn1ciamis/calakan/
│       │   ├── MainActivity.java   # WebView activity
│       │   └── SplashActivity.java # Splash screen
│       └── res/
│           ├── drawable/           # Vector drawables
│           ├── layout/             # XML layouts
│           ├── mipmap-anydpi-v26/  # Adaptive icons
│           ├── values/             # Colors, strings, themes
│           └── xml/                # Network & file configs
└── README.md
```

## Mengubah URL

Untuk mengubah URL target, edit `BASE_URL` di [app/build.gradle](app/build.gradle):

```groovy
buildConfigField "String", "BASE_URL", "\"https://url-baru-anda.com/siswa/login\""
```

## Menggenerate Launcher Icon dengan Gambar Kustom

1. Buka Android Studio
2. Klik kanan pada folder `res` → **New → Image Asset**
3. Pilih **Launcher Icons (Adaptive and Legacy)**
4. Pilih **Image** sebagai foreground, upload gambar logo
5. Set background color ke `#2563EB`
6. Klik **Next → Finish**

Ini akan otomatis mengenerate icon di semua ukuran mipmap.

## Troubleshooting

| Masalah                        | Solusi                                                         |
| ------------------------------ | -------------------------------------------------------------- |
| Gradle sync gagal              | Pastikan JDK 17 terinstall dan dikonfigurasi di Android Studio |
| SDK not found                  | Buat file `local.properties` dengan path ke Android SDK        |
| Build error "missing resource" | Jalankan **Build → Clean Project** lalu **Build → Rebuild**    |
| WebView blank                  | Periksa koneksi internet dan pastikan URL bisa diakses         |
| Camera tidak berfungsi         | Pastikan izin kamera sudah diberikan di Settings device        |

## Lisensi

© 2026 Calakan — SMKN 1 Ciamis. Internal use only.
