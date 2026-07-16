# 🗳️ Pemira Online

Sistem pemilihan umum (Pemira) digital untuk kampus — dibangun agar fleksibel dipakai oleh BEM, Himpunan Mahasiswa (Hima), maupun organisasi eksternal. Terinspirasi dari alur kerja KPU: pendaftaran calon, verifikasi pemilih (DPS → DPT), pemungutan suara, hingga rekapitulasi hasil — semuanya digital.

![PHP](https://img.shields.io/badge/PHP-8.2%20--%208.4-777BB4?style=flat&logo=php&logoColor=white)
![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=flat&logo=laravel&logoColor=white)
![Filament](https://img.shields.io/badge/Filament-5-F59E0B?style=flat&logo=laravel&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-3-4E56A6?style=flat&logo=livewire&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind-4-06B6D4?style=flat&logo=tailwindcss&logoColor=white)
![License](https://img.shields.io/badge/license-MIT-green?style=flat)

---

## ✨ Fitur

- 📋 **Manajemen Paslon** — pendaftaran kandidat lengkap dengan foto, visi, misi, dan program kerja
- ✅ **Verifikasi Pemilih (DPS → DPT)** — mahasiswa memverifikasi diri dengan NIM + Nama, admin menyetujui satu per satu atau massal
- 📥 **Import Data Mahasiswa dari Excel** — mapping kolom fleksibel, mendukung banyak berkas sekaligus, deteksi otomatis baris header
- 🔒 **Whitelist Pemilih Manual** — mode kurasi khusus untuk pemilihan skala kecil (Hima/organisasi) agar tidak disusupi pemilih dari luar lingkup
- 🗳️ **Voting Aman** — satu pemilih satu suara, dilindungi dari race condition lewat row-level locking database
- 📊 **Hasil Real-Time** — persentase & jumlah suara ter-update otomatis, dilengkapi grafik lingkaran dan laporan PDF resmi
- 🔁 **Reset & Arsip** — pemilihan lama diarsipkan (bukan dihapus), siap dipakai ulang untuk acara pemira berikutnya lewat fitur duplikat
- 🎨 **Desain Interaktif** — tema biru-putih modern, dibangun penuh dengan Tailwind CSS

## 🛠️ Tech Stack

| Layer | Teknologi |
|---|---|
| Backend | Laravel 13 |
| Admin Panel | Filament 5 |
| Interaktivitas | Livewire 3 + Alpine.js |
| Styling | Tailwind CSS 4 |
| Import Excel | Laravel Excel (maatwebsite/excel) |
| Export PDF | barryvdh/laravel-dompdf |
| Grafik | Chart.js |
| Database | MySQL / MariaDB |

## 🚀 Quick Start

```bash
git clone <url-repository-ini>
cd pemira-app
composer install
npm install
cp .env.example .env
php artisan key:generate
# sesuaikan koneksi database di .env, lalu:
php artisan migrate
php artisan storage:link
php artisan make:filament-user
npm run dev    # terminal terpisah
php artisan serve
```

> ⚠️ **Butuh PHP 8.2 – 8.4.** Package import Excel belum kompatibel dengan PHP 8.5 pada saat ini.

📖 **Panduan instalasi lengkap** (Windows, macOS, Linux — termasuk cara menjalankan versi PHP lawas berdampingan) ada di **[PANDUAN-INSTALASI.md](./PANDUAN-INSTALASI.md)**.

## 📂 Struktur Konsep

Aplikasi ini dibangun di sekitar satu entitas generik bernama **Pemilihan** — sehingga satu sistem dapat menampung banyak acara pemilihan sekaligus (Pemira BEM, Pemilihan Ketua Hima, dll), masing-masing dengan paslon, jadwal, dan daftar pemilihnya sendiri, tanpa perlu membangun ulang struktur untuk tiap organisasi.

```
Pemilihan (1 acara pemira)
 ├── Paslon (kandidat yang bertarung)
 ├── Pemilih (status DPS/DPT per mahasiswa, khusus pemilihan ini)
 └── Suara (catatan suara masuk, TIDAK terhubung ke identitas pemilih)
```

Prinsip kerahasiaan suara dijaga dengan memisahkan **"siapa yang sudah memilih"** (tabel `pemilihs`) dari **"suara apa yang masuk"** (tabel `suaras`) — keduanya tidak saling berelasi langsung.

## 👤 Akses Admin

Buat akun admin lewat:

```bash
php artisan make:filament-user
```

Lalu masuk ke `/admin`.

## 📄 Lisensi

Project ini bebas digunakan dan dimodifikasi untuk keperluan pendidikan maupun operasional kampus/organisasi.

---

*Dibangun sebagai proyek pembelajaran bertahap dari nol — seluruh proses pengembangan, termasuk histori debugging dan pengambilan keputusan teknis, didokumentasikan secara lengkap di luar repository ini.*
