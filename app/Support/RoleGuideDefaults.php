<?php

namespace App\Support;

class RoleGuideDefaults
{
  /**
   * @return array<int, array{name: string, phone: string}>
   */
  public static function defaultContacts(string $role): array
  {
    return match (strtolower(trim($role))) {
      'siswa' => [
        ['name' => 'Admin 1', 'phone' => '081246995873'],
        ['name' => 'Admin 2', 'phone' => '0895421692700'],
      ],
      'guru' => [
        ['name' => 'Admin Guru', 'phone' => '081246995873'],
      ],
      'kesiswaan' => [
        ['name' => 'Admin Kesiswaan', 'phone' => '081246995873'],
      ],
      default => [],
    };
  }

  /**
   * @return array<int, array{title: string, desc: string}>
   */
  public static function defaultFlowSteps(string $role): array
  {
    return match (strtolower(trim($role))) {
      'siswa' => [
        ['title' => 'Login Akun Siswa', 'desc' => 'Masukkan NISN 10 digit dan password yang aktif.'],
        ['title' => 'Ganti Password Wajib', 'desc' => 'Jika diminta sistem, ubah password lalu simpan aman.'],
        ['title' => 'Masuk Dashboard', 'desc' => 'Sistem arahkan otomatis ke dashboard sesuai agama.'],
        ['title' => 'Isi Formulir Harian', 'desc' => 'Lengkapi data kegiatan harian lalu simpan.'],
        ['title' => 'Pantau Verifikasi', 'desc' => 'Cek status validasi dari guru dan kesiswaan.'],
      ],
      'guru' => [
        ['title' => 'Login Panel Guru', 'desc' => 'Masuk dengan email dan password akun guru yang terdaftar.'],
        ['title' => 'Cek Ringkasan Dashboard', 'desc' => 'Lihat jumlah pending, diverifikasi, dan ditolak per kelas.'],
        ['title' => 'Review Data Siswa', 'desc' => 'Buka data siswa kelas wali, cek identitas dan kelengkapan.'],
        ['title' => 'Verifikasi Formulir Harian', 'desc' => 'Periksa isi laporan harian, lalu tetapkan status yang sesuai.'],
        ['title' => 'Tulis Catatan Perbaikan', 'desc' => 'Jika ditolak, berikan alasan spesifik agar siswa mudah memperbaiki.'],
        ['title' => 'Bantu Kendala Akses', 'desc' => 'Gunakan reset password atau reset sesi login saat dibutuhkan.'],
        ['title' => 'Pantau Rekap Kelas', 'desc' => 'Follow up siswa yang belum lengkap dan koordinasi dengan kesiswaan.'],
      ],
      'kesiswaan' => [
        ['title' => 'Login Panel Kesiswaan', 'desc' => 'Masuk dengan akun kesiswaan atau kepala sekolah yang aktif.'],
        ['title' => 'Cek Dashboard Monitoring', 'desc' => 'Pantau statistik global dan kelas dengan antrean tertinggi.'],
        ['title' => 'Prioritaskan Kelas Pending', 'desc' => 'Urutkan validasi dari kelas dengan beban pending terbesar.'],
        ['title' => 'Validasi Final Berjenjang', 'desc' => 'Validasi setelah tahap guru, tolak bila data belum layak.'],
        ['title' => 'Audit Data Master', 'desc' => 'Pastikan data siswa, guru, dan kelas sinkron untuk pelaporan.'],
        ['title' => 'Kelola Akses Formulir', 'desc' => 'Aktifkan atau nonaktifkan formulir sesuai kebijakan operasional.'],
        ['title' => 'Finalisasi dan Laporan', 'desc' => 'Selesaikan rekap akhir lalu koordinasi tindak lanjut lintas unit.'],
      ],
      default => [],
    };
  }

  public static function forRole(string $role): string
  {
    return match (strtolower(trim($role))) {
      'siswa' => self::siswa(),
      'guru' => self::guru(),
      'kesiswaan' => self::kesiswaan(),
      default => '',
    };
  }

  private static function siswa(): string
  {
    return <<<'HTML'
<h2>Panduan Siswa Super Lengkap (Alur Awal Sampai Akhir)</h2>
<p>Panduan ini dibuat untuk siswa agar bisa menggunakan Calakan dengan benar dari proses login, pengisian formulir harian, sampai pemantauan status data. Bacalah berurutan agar tidak ada langkah yang terlewat.</p>

<h3>1. Tujuan Panel Siswa</h3>
<ul>
  <li>Mencatat aktivitas harian selama periode Ramadan sesuai ketentuan sekolah.</li>
  <li>Menjadi data resmi yang akan diverifikasi guru dan dipantau kesiswaan.</li>
  <li>Membantu siswa disiplin mengisi laporan harian secara konsisten.</li>
</ul>

<h3>2. Persiapan Sebelum Login</h3>
<ol>
  <li>Pastikan NISN kamu valid dan terdiri dari <strong>10 digit angka</strong>.</li>
  <li>Siapkan password akun siswa yang aktif.</li>
  <li>Gunakan perangkat pribadi jika memungkinkan (HP/laptop sendiri).</li>
  <li>Gunakan koneksi internet stabil agar proses simpan data tidak terputus.</li>
</ol>

<h3>3. Langkah Login yang Benar</h3>
<ol>
  <li>Buka halaman login siswa.</li>
  <li>Isi kolom NISN (hanya angka, tepat 10 digit).</li>
  <li>Isi password akun.</li>
  <li>Klik tombol <strong>Masuk</strong>.</li>
  <li>Tunggu sampai diarahkan ke dashboard siswa.</li>
</ol>

<h3>4. Aturan Validasi Login (Wajib Dipahami)</h3>
<ul>
  <li>Jika NISN tidak 10 digit, sistem otomatis menolak login.</li>
  <li>Jika kombinasi NISN/password salah 3 kali, akun terkunci sementara 60 detik.</li>
  <li>Saat terkunci, akan muncul popup hitung mundur. Tunggu sampai selesai.</li>
  <li>Jika akun masih aktif di perangkat lain, login baru akan ditolak sementara.</li>
  <li>Sesi lama umumnya punya batas durasi; setelah kedaluwarsa, login baru bisa lanjut.</li>
</ul>

<h3>5. Wajib Ganti Password Setelah Login Pertama</h3>
<ul>
  <li>Jika akun kamu ditandai <strong>wajib ubah password</strong>, setelah login sistem akan meminta kamu membuat password baru.</li>
  <li>Langkah ini <strong>wajib</strong> diselesaikan sebelum lanjut menggunakan panel secara normal.</li>
  <li>Buat password yang kuat: minimal 8 karakter, kombinasi huruf besar, huruf kecil, angka, dan simbol.</li>
  <li>Jangan gunakan tanggal lahir, nama sendiri, atau NISN sebagai password baru.</li>
</ul>

<h3>6. Password Baru Wajib Disimpan dengan Aman</h3>
<ol>
  <li>Setelah berhasil mengganti password, segera simpan password baru di tempat aman (catatan pribadi yang tidak dibagikan).</li>
  <li>Jangan hanya menghafal tanpa cadangan, agar tidak terkunci saat lupa.</li>
  <li>Jangan simpan di chat grup, status, atau tempat yang bisa dilihat orang lain.</li>
  <li>Jika perlu, gunakan password manager atau catatan offline pribadi.</li>
</ol>

<h3>7. Setelah Login Berhasil, Kamu Akan Masuk ke Halaman Sesuai Agama</h3>
<ul>
  <li>Siswa Muslim: Dashboard Muslim, Formulir Harian, dan menu Al-Quran.</li>
  <li>Siswa Kristen/Katolik: otomatis diarahkan ke dashboard/form varian Kristen.</li>
  <li>Siswa Hindu: otomatis diarahkan ke dashboard/form varian Hindu.</li>
  <li>Siswa Buddha: otomatis diarahkan ke dashboard/form varian Buddha.</li>
  <li>Siswa Konghucu: otomatis diarahkan ke dashboard/form varian Konghucu.</li>
</ul>

<h3>8. Struktur Menu Utama Siswa</h3>
<ol>
  <li><strong>Dashboard</strong>: ringkasan progres, informasi hari aktif, dan status pengisian.</li>
  <li><strong>Formulir Harian</strong>: tempat input kegiatan ibadah/aktivitas harian.</li>
  <li><strong>Al-Quran</strong> (khusus Muslim): bacaan atau konten pendukung harian.</li>
</ol>

<h3>9. Alur Pengisian Harian yang Direkomendasikan</h3>
<ol>
  <li>Login setiap hari (disarankan di waktu yang sama agar konsisten).</li>
  <li>Buka Formulir Harian untuk hari berjalan.</li>
  <li>Isi semua kolom wajib satu per satu, jangan lompat bagian penting.</li>
  <li>Periksa kembali isian sebelum simpan.</li>
  <li>Klik simpan, lalu pastikan tidak ada pesan error.</li>
  <li>Setelah tersimpan, cek kembali apakah data tampil sebagai data hari itu.</li>
</ol>

<h3>10. Standar Isi Data Agar Valid</h3>
<ul>
  <li>Data harus sesuai kegiatan nyata pada hari tersebut.</li>
  <li>Jangan isi asal atau menyalin data hari lain jika kegiatan berbeda.</li>
  <li>Jika ada kolom catatan, isi singkat, jelas, dan relevan.</li>
  <li>Lengkapi seluruh kolom yang diwajibkan sistem sebelum simpan.</li>
</ul>

<h3>11. Tentang Status Verifikasi Data</h3>
<ul>
  <li>Data yang kamu isi akan dicek/ diverifikasi oleh guru.</li>
  <li>Setelah itu bisa masuk validasi lanjutan oleh kesiswaan (sesuai alur sekolah).</li>
  <li>Jika ada isian kurang tepat, data bisa ditolak dan perlu diperbaiki.</li>
</ul>

<h3>12. Kondisi Khusus yang Sering Terjadi</h3>
<ol>
  <li><strong>Formulir tidak bisa dibuka (403)</strong>: kemungkinan formulir agama kamu sedang dinonaktifkan oleh pengaturan kesiswaan.</li>
  <li><strong>Muncul popup akun aktif di perangkat lain</strong>: logout perangkat lama atau minta reset sesi ke guru.</li>
  <li><strong>Akun terkunci 60 detik</strong>: tunggu sampai timer habis, lalu login ulang dengan data benar.</li>
  <li><strong>Tidak masuk ke dashboard yang sesuai</strong>: cek data agama akun ke admin/guru.</li>
</ol>

<h3>13. Keamanan Akun Siswa</h3>
<ul>
  <li>Jangan bagikan password ke teman.</li>
  <li>Jangan simpan akun di perangkat umum tanpa logout.</li>
  <li>Ganti password jika sekolah meminta pembaruan keamanan.</li>
  <li>Jika merasa akun dipakai orang lain, segera lapor guru/admin.</li>
</ul>

<h3>14. Langkah Cepat Jika Lupa Password / Tidak Bisa Login</h3>
<ol>
  <li>Pastikan NISN benar 10 digit.</li>
  <li>Coba login ulang perlahan (hindari salah berkali-kali berturut-turut).</li>
  <li>Jika tetap gagal, hubungi wali kelas/guru untuk reset password.</li>
  <li>Jika kendala sesi perangkat, minta reset sesi login siswa.</li>
</ol>

<h3>15. Etika Penggunaan Sistem</h3>
<ul>
  <li>Isi data tepat waktu, jangan menunda banyak hari.</li>
  <li>Gunakan bahasa sopan jika ada kolom keterangan.</li>
  <li>Utamakan kejujuran data karena ini bagian evaluasi pembinaan.</li>
</ul>

<h3>16. Checklist Harian Siswa</h3>
<ol>
  <li>Saya login dengan NISN dan password yang benar.</li>
  <li>Saya membuka formulir sesuai hari berjalan.</li>
  <li>Saya sudah mengisi semua kolom wajib.</li>
  <li>Saya sudah menyimpan dan memastikan data tersimpan.</li>
  <li>Saya siap memperbaiki data jika ada catatan verifikasi.</li>
</ol>

<h3>17. Checklist Mingguan Siswa</h3>
<ol>
  <li>Cek kembali data beberapa hari terakhir, pastikan tidak ada yang kosong.</li>
  <li>Pastikan status data tidak banyak tertunda.</li>
  <li>Laporkan segera jika ada bug/masalah akses ke guru/admin.</li>
</ol>

<p><strong>Catatan penting:</strong> jika ada perbedaan prosedur dengan arahan resmi sekolah, ikuti arahan guru/kesiswaan karena itu menjadi acuan operasional utama.</p>
HTML;
  }

  private static function guru(): string
  {
    return <<<'HTML'
<h2>Panduan Lengkap Guru</h2>
<p>Panel guru digunakan untuk verifikasi data siswa, penanganan kendala akses siswa, dan monitoring progres kelas wali. Panduan ini disusun agar proses verifikasi lebih cepat, konsisten, dan mudah ditindaklanjuti.</p>

<h3>Ringkasan Singkat</h3>
<ol>
  <li>Login panel guru.</li>
  <li>Cek dashboard untuk melihat prioritas kelas.</li>
  <li>Periksa data siswa dan formulir harian.</li>
  <li>Tetapkan status: verifikasi atau tolak dengan catatan.</li>
  <li>Bantu kendala login siswa melalui fitur reset.</li>
  <li>Pantau rekap dan koordinasi tindak lanjut dengan kesiswaan.</li>
</ol>

<h3>1. Akses Login Guru</h3>
<ul>
  <li>Masuk menggunakan email dan password akun guru.</li>
  <li>Jika email/password salah, pastikan huruf besar kecil sesuai.</li>
  <li>Hindari mencoba berulang terlalu cepat agar tidak dianggap percobaan gagal beruntun.</li>
  <li>Pastikan akun memiliki role guru agar bisa masuk panel ini.</li>
</ul>

<h3>2. Area Kerja Utama Guru</h3>
<ul>
  <li><strong>Dashboard</strong>: ringkasan statistik dan progres kelas.</li>
  <li><strong>Manajemen Siswa</strong>: pengelolaan siswa di kelas wali.</li>
  <li><strong>Verifikasi Formulir</strong>: proses cek dan validasi input siswa.</li>
  <li><strong>Rekap Siswa</strong>: monitoring hasil verifikasi dan progres.</li>
</ul>

<h3>3. SOP Verifikasi Formulir Harian</h3>
<ol>
  <li>Buka menu verifikasi dan filter berdasarkan kelas atau tanggal.</li>
  <li>Periksa kelengkapan isian wajib dan konsistensi antar kolom.</li>
  <li>Pastikan data masuk akal sesuai konteks aktivitas harian siswa.</li>
  <li>Tetapkan keputusan: <strong>verifikasi</strong> jika layak, <strong>tolak</strong> jika perlu perbaikan.</li>
  <li>Jika menolak, tulis alasan operasional yang jelas, singkat, dan spesifik.</li>
  <li>Ulangi proses sampai antrean harian selesai agar tidak menumpuk.</li>
</ol>

<h3>4. Standar Catatan Penolakan yang Baik</h3>
<ul>
  <li>Gunakan bahasa sederhana dan langsung ke poin masalah.</li>
  <li>Sebutkan bagian mana yang harus diperbaiki siswa.</li>
  <li>Hindari catatan umum seperti "perbaiki lagi" tanpa detail.</li>
  <li>Contoh baik: "Kolom kegiatan sore belum diisi, mohon lengkapi lalu kirim ulang."</li>
</ul>

<h3>5. SOP Manajemen Siswa (Kelas Wali)</h3>
<ul>
  <li>Guru hanya dapat melihat siswa di kelas yang diwalikan.</li>
  <li>Gunakan filter agama dan jenis kelamin untuk audit cepat data.</li>
  <li>Reset password ke NISN jika siswa lupa password.</li>
  <li>Reset sesi login jika siswa terkunci karena konflik perangkat.</li>
  <li>Pastikan perubahan bantuan akses dicatat agar mudah ditelusuri saat audit.</li>
</ul>

<h3>6. Penanganan Masalah Umum di Lapangan</h3>
<ol>
  <li><strong>Siswa gagal login berulang:</strong> cek NISN, lalu bantu reset password bila perlu.</li>
  <li><strong>Siswa terkunci karena sesi aktif:</strong> lakukan reset sesi login dari panel guru.</li>
  <li><strong>Formulir terlihat kosong terus:</strong> pastikan siswa sudah klik simpan dan refresh data.</li>
  <li><strong>Data tampak tidak wajar:</strong> tolak dengan catatan detail dan minta perbaikan hari itu juga.</li>
</ol>

<h3>7. Tindak Lanjut dan Eskalasi</h3>
<ul>
  <li>Jika ada kasus lintas kelas, koordinasi dengan kesiswaan.</li>
  <li>Jika ada masalah akses/konfigurasi panel, lapor ke superadmin.</li>
  <li>Gunakan rekap siswa untuk dasar pembinaan dan evaluasi periodik.</li>
</ul>

<h3>8. Checklist Harian Guru</h3>
<ol>
  <li>Dashboard sudah dicek untuk menentukan prioritas kelas.</li>
  <li>Semua formulir baru sudah ditinjau minimal sekali.</li>
  <li>Data bermasalah sudah diberi status dan catatan perbaikan.</li>
  <li>Kendala login siswa sudah ditangani dengan reset yang tepat.</li>
  <li>Rekap kelas sudah dipantau dan siswa tertunda sudah ditindaklanjuti.</li>
</ol>

<h3>9. Checklist Mingguan Guru</h3>
<ol>
  <li>Evaluasi tren penolakan per kelas untuk melihat pola masalah.</li>
  <li>Koordinasi dengan kesiswaan untuk kelas yang progresnya lambat.</li>
  <li>Laporkan kendala teknis berulang ke superadmin untuk perbaikan sistem.</li>
</ol>

<p><strong>Catatan penting:</strong> bila ada perbedaan antara praktik lapangan dan kebijakan terbaru sekolah, ikuti instruksi resmi pimpinan dan kesiswaan.</p>
HTML;
  }

  private static function kesiswaan(): string
  {
    return <<<'HTML'
<h2>Panduan Lengkap Kesiswaan</h2>
<p>Panel kesiswaan berfungsi sebagai validasi lanjutan dan pengendalian mutu data setelah tahap guru. Fokus utamanya adalah monitoring antar kelas, validasi final, pengaturan operasional, dan pelaporan progres sekolah.</p>

<h3>Ringkasan Singkat</h3>
<ol>
  <li>Login panel kesiswaan.</li>
  <li>Cek dashboard dan petakan kelas prioritas.</li>
  <li>Pantau antrean validasi per kelas.</li>
  <li>Lakukan validasi final data setelah tahap guru.</li>
  <li>Audit data master dan pengaturan formulir bila dibutuhkan.</li>
  <li>Finalisasi monitoring dan susun tindak lanjut.</li>
</ol>

<h3>1. Akses Login Kesiswaan</h3>
<ul>
  <li>Masuk menggunakan email dan password akun kesiswaan/kepala sekolah.</li>
  <li>Pastikan role akun sesuai agar menu validasi muncul lengkap.</li>
  <li>Jika akses menu terbatas, cek visibilitas role atau hubungi superadmin.</li>
</ul>

<h3>2. Area Kerja Utama Kesiswaan</h3>
<ul>
  <li><strong>Dashboard Kesiswaan</strong>: ringkasan data dan performa validasi.</li>
  <li><strong>Validasi Formulir</strong>: validasi/tolak data setelah tahap guru.</li>
  <li><strong>Validasi per Kelas</strong>: kontrol progress kelas (pending/validated/rejected).</li>
  <li><strong>Data Siswa & Data Guru</strong>: monitoring data master.</li>
  <li><strong>Pengaturan Formulir</strong>: aktivasi/non-aktivasi formulir per agama.</li>
</ul>

<h3>3. SOP Validasi Final Berjenjang</h3>
<ol>
  <li>Prioritaskan kelas dengan antrean menunggu terbanyak.</li>
  <li>Periksa data yang masuk dari sisi kelengkapan dan kewajaran.</li>
  <li>Pastikan alur verifikasi guru sudah sesuai sebelum validasi final.</li>
  <li>Tentukan status final: <strong>validasi</strong> atau <strong>tolak</strong>.</li>
  <li>Jika ditolak, tulis alasan yang operasional agar guru/siswa bisa menindaklanjuti.</li>
  <li>Pantau ulang dampak keputusan terhadap progres kelas di dashboard.</li>
</ol>

<h3>4. Kontrol Kualitas Data</h3>
<ul>
  <li>Gunakan validasi berjenjang: guru lalu kesiswaan.</li>
  <li>Hindari perubahan data pokok tanpa koordinasi pihak terkait.</li>
  <li>Perhatikan pola anomali data antar kelas untuk evaluasi.</li>
  <li>Simpan jejak keputusan validasi untuk audit internal.</li>
</ul>

<h3>5. Pengelolaan Formulir dan Kebijakan Operasional</h3>
<ul>
  <li>Aktifkan/nonaktifkan formulir sesuai kalender dan kebijakan sekolah.</li>
  <li>Pastikan perubahan status formulir dikomunikasikan ke guru.</li>
  <li>Lakukan penyesuaian bertahap agar tidak mengganggu input harian siswa.</li>
</ul>

<h3>6. Penanganan Kasus Prioritas</h3>
<ol>
  <li><strong>Kelas stuck di pending:</strong> fokuskan validasi dan cek hambatan dari sisi guru.</li>
  <li><strong>Banyak penolakan berulang:</strong> audit pola kesalahan lalu koordinasi pembinaan.</li>
  <li><strong>Menu/form tidak tampil:</strong> cek pengaturan formulir dan role akses.</li>
  <li><strong>Perbedaan data master:</strong> sinkronkan dengan admin terkait sebelum finalisasi.</li>
</ol>

<h3>7. Eskalasi dan Koordinasi</h3>
<ul>
  <li>Masalah kebijakan data: koordinasi dengan pimpinan sekolah.</li>
  <li>Masalah role/menu/hak akses: eskalasi ke superadmin.</li>
  <li>Masalah operasional kelas: koordinasi dengan guru wali kelas terkait.</li>
</ul>

<h3>8. Checklist Harian Kesiswaan</h3>
<ol>
  <li>Dashboard monitoring sudah dicek pada awal sesi kerja.</li>
  <li>Antrean validasi kelas prioritas sudah ditangani.</li>
  <li>Penolakan sudah disertai alasan yang jelas dan dapat ditindaklanjuti.</li>
  <li>Progress akhir dipantau pada rekap dan disiapkan untuk laporan.</li>
</ol>

<h3>9. Checklist Mingguan Kesiswaan</h3>
<ol>
  <li>Evaluasi progres antar kelas dan tentukan kelas yang perlu intervensi.</li>
  <li>Tinjau efektivitas validasi berjenjang guru-kesiswaan.</li>
  <li>Rangkum temuan operasional untuk bahan rapat evaluasi sekolah.</li>
</ol>

<p><strong>Catatan penting:</strong> keputusan final tetap mengikuti kebijakan resmi sekolah. Gunakan panel sebagai alat kontrol data, bukan pengganti koordinasi lapangan.</p>
HTML;
  }
}
