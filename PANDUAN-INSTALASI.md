# Panduan Instalasi Pemira Online (Lintas OS)

Panduan ini menjelaskan cara menyiapkan environment development project **Pemira Online** (Laravel 13 + Filament 5 + Livewire) dari nol, untuk tiga sistem operasi: **Windows**, **macOS**, dan **Linux** (Arch/Manjaro serta Debian/Ubuntu).

> **Catatan penting sebelum mulai:** Package `maatwebsite/excel` (dipakai untuk fitur import Excel) saat ini **belum kompatibel dengan PHP 8.5**, karena pustaka intinya (PhpSpreadsheet) belum menambahkan dukungan resmi untuk versi PHP tersebut. **Gunakan PHP 8.2, 8.3, atau 8.4** — jangan pakai PHP 8.5 dulu sampai isu ini resmi diperbaiki upstream.

---

## Daftar Isi

- [Kebutuhan Umum](#kebutuhan-umum)
- [Instalasi di Windows](#instalasi-di-windows)
- [Instalasi di macOS](#instalasi-di-macos)
- [Instalasi di Linux (Arch/Manjaro)](#instalasi-di-linux-archmanjaro)
- [Instalasi di Linux (Debian/Ubuntu)](#instalasi-di-linux-debianubuntu)
- [Setup Project (Sama untuk Semua OS)](#setup-project-sama-untuk-semua-os)
- [Troubleshooting Umum](#troubleshooting-umum)

---

## Kebutuhan Umum

Apa pun OS yang dipakai, pastikan komponen berikut tersedia:

| Komponen | Versi Minimal | Catatan |
|---|---|---|
| PHP | 8.2 — 8.4 | **Jangan PHP 8.5** (lihat catatan di atas) |
| Ekstensi PHP | `gd`, `intl`, `zip`, `mbstring`, `pdo_mysql`, `pdo_sqlite`, `phar`, `pcntl`, `bcmath`, `ctype`, `curl`, `openssl`, `xml`, `dom`, `fileinfo`, `tokenizer` | Wajib untuk Laravel + Filament + Import Excel |
| Composer | Terbaru | Manajer dependensi PHP |
| Node.js & npm | Node 18+ | Untuk compile Tailwind CSS |
| MySQL/MariaDB | 8.0+ / 10.6+ | Database utama |
| Git | Terbaru | Untuk clone repository |

---

## Instalasi di Windows

Ada dua pendekatan: **cara mudah** (pakai Laragon, direkomendasikan untuk pemula) atau **cara manual** (install komponen satu per satu).

### Opsi A — Cara Mudah: Laragon (Direkomendasikan)

[Laragon](https://laragon.org/download/) adalah paket all-in-one yang sudah menyertakan PHP, MySQL, Composer, dan Node.js sekaligus.

1. Unduh dan install **Laragon Full** dari https://laragon.org/download/
2. Buka Laragon, klik kanan pada tray icon → **PHP** → pilih versi **8.3** atau **8.4** (Laragon biasanya menyediakan beberapa versi PHP sekaligus, bisa diunduh tambahan lewat menu **Quick Add**)
3. Klik kanan tray icon → **Apache/Nginx** → pastikan berjalan, atau langsung pakai `php artisan serve` seperti biasa
4. Buka **Laragon Terminal** (klik kanan tray icon → Terminal) untuk menjalankan seluruh perintah di bagian [Setup Project](#setup-project-sama-untuk-semua-os) di bawah

### Opsi B — Cara Manual (Native Windows)

1. **Install PHP** lewat [Chocolatey](https://chocolatey.org/install) (package manager Windows):
   ```powershell
   # Jalankan PowerShell sebagai Administrator
   choco install php --version=8.4.0
   ```
   Atau unduh manual dari https://windows.php.net/download/ (pilih **Thread Safe**, versi 8.3/8.4), ekstrak ke `C:\php`, lalu tambahkan `C:\php` ke **Environment Variables → PATH**.

2. **Aktifkan ekstensi PHP** — edit `php.ini` (biasanya di `C:\php\php.ini`), hapus tanda `;` di depan baris berikut:
   ```ini
   extension=gd
   extension=intl
   extension=zip
   extension=mbstring
   extension=pdo_mysql
   extension=curl
   extension=fileinfo
   extension=openssl
   ```

3. **Install Composer** — unduh installer dari https://getcomposer.org/download/ (`Composer-Setup.exe`), jalankan dan arahkan ke lokasi PHP yang sudah diinstall.

4. **Install Node.js** — unduh installer LTS dari https://nodejs.org/, jalankan seperti instalasi aplikasi Windows biasa.

5. **Install MySQL** — unduh **MySQL Installer** dari https://dev.mysql.com/downloads/installer/, pilih **MySQL Server** saat instalasi, catat password root yang diset.

6. Buka **PowerShell** atau **Command Prompt**, lanjut ke bagian [Setup Project](#setup-project-sama-untuk-semua-os).

### Opsi C — WSL2 (Windows Subsystem for Linux)

Jika terbiasa dengan lingkungan Linux, install [WSL2](https://learn.microsoft.com/windows/wsl/install) dengan distro Ubuntu:

```powershell
wsl --install -d Ubuntu
```

Setelah WSL2 aktif, ikuti panduan **[Instalasi di Linux (Debian/Ubuntu)](#instalasi-di-linux-debianubuntu)** di bawah — karena WSL2 Ubuntu berperilaku persis seperti Ubuntu native.

---

## Instalasi di macOS

Cara termudah di macOS adalah menggunakan [Homebrew](https://brew.sh/).

```bash
# Install Homebrew jika belum ada
/bin/bash -c "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/HEAD/install.sh)"

# Install PHP 8.3 (atau 8.4), MySQL, Composer, dan Node.js
brew install php@8.3 mysql composer node

# Jadikan PHP 8.3 sebagai versi default di terminal
echo 'export PATH="/opt/homebrew/opt/php@8.3/bin:$PATH"' >> ~/.zshrc
source ~/.zshrc

# Nyalakan MySQL sebagai service
brew services start mysql

# Verifikasi versi
php -v
composer -V
node -v
```

Ekstensi PHP seperti `gd`, `intl`, `zip`, `mbstring`, `pdo_mysql` biasanya sudah otomatis ikut terpasang lewat Homebrew. Cek dengan:

```bash
php -m | grep -Ei "gd|intl|zip|mbstring|pdo_mysql"
```

Kalau ada yang belum aktif, cari lokasi `php.ini` dengan `php --ini`, lalu edit sesuai kebutuhan.

Lanjut ke bagian [Setup Project](#setup-project-sama-untuk-semua-os).

---

## Instalasi di Linux (Arch/Manjaro)

> Ini environment yang dipakai untuk mengembangkan project ini. **Perhatian:** jika sistem sudah memakai PHP 8.5 (karena sifat rolling release Arch), package `maatwebsite/excel` akan gagal terinstal. Lihat solusi *"PHP versi lebih lama berdampingan"* di bawah.

```bash
# Update sistem -- WAJIB diselesaikan sampai tuntas, JANGAN dibatalkan
# di tengah proses (bisa merusak kompatibilitas glibc)
sudo pacman -Syu

# Install PHP beserta ekstensi wajib
sudo pacman -S php php-fpm php-gd php-intl php-sqlite

# Install MariaDB (drop-in replacement MySQL di Arch)
sudo pacman -S mariadb
sudo mariadb-install-db --datadir=/var/lib/mysql --user=mysql
sudo systemctl enable --now mariadb
sudo mariadb-secure-installation

# Install Composer dan Node.js
sudo pacman -S composer nodejs npm
```

### Jika Sistem Sudah Memakai PHP 8.5

Install PHP versi lebih lama secara berdampingan lewat AUR, tanpa mengganti PHP sistem:

```bash
# Install AUR helper (yay) jika belum ada
sudo pacman -S --needed git base-devel
git clone https://aur.archlinux.org/yay.git
cd yay && makepkg -si

# Install PHP 8.4 beserta seluruh ekstensi yang dibutuhkan
yay -S php84 php84-cli php84-fpm php84-gd php84-intl php84-zip \
       php84-sqlite php84-phar php84-mbstring php84-mysql \
       php84-pcntl php84-bcmath php84-ctype php84-openssl \
       php84-fileinfo php84-tokenizer php84-xml php84-dom \
       php84-simplexml --sudoloop
```

Lalu di dalam folder project, gunakan [direnv](https://direnv.net/) agar `php`/`composer` otomatis memakai versi 8.4 khusus di folder ini:

```bash
sudo pacman -S direnv
echo 'eval "$(direnv hook zsh)"' >> ~/.zshrc   # ganti "zsh" jadi "bash" jika pakai Bash
source ~/.zshrc

mkdir -p .bin
ln -sf /usr/bin/php84 .bin/php
cat > .bin/composer << 'EOF'
#!/bin/bash
exec php84 /usr/bin/composer "$@"
EOF
chmod +x .bin/composer

echo 'PATH_add .bin' > .envrc
direnv allow .
```

Lanjut ke bagian [Setup Project](#setup-project-sama-untuk-semua-os).

---

## Instalasi di Linux (Debian/Ubuntu)

```bash
sudo apt update && sudo apt upgrade -y

# Tambahkan repository PHP (Ondřej Surý PPA) untuk akses versi PHP terbaru
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP 8.3 beserta ekstensi wajib
sudo apt install -y php8.3 php8.3-fpm php8.3-cli php8.3-mysql \
    php8.3-gd php8.3-intl php8.3-zip php8.3-mbstring php8.3-xml \
    php8.3-curl php8.3-bcmath

# Install MySQL Server
sudo apt install -y mysql-server
sudo mysql_secure_installation

# Install Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Install Node.js (via NodeSource)
curl -fsSL https://deb.nodesource.com/setup_lts.x | sudo -E bash -
sudo apt install -y nodejs
```

Lanjut ke bagian [Setup Project](#setup-project-sama-untuk-semua-os).

---

## Setup Project (Sama untuk Semua OS)

Setelah environment siap (PHP, Composer, Node.js, MySQL semua terinstall), langkah berikut **sama persis** di Windows/macOS/Linux — jalankan lewat terminal (PowerShell/Command Prompt untuk Windows, Terminal untuk macOS/Linux).

### 1. Clone repository

```bash
git clone <url-repository-project-ini>
cd pemira-app
```

### 2. Buat database

Masuk ke MySQL/MariaDB:

```bash
mysql -u root -p
```

```sql
CREATE DATABASE pemira_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'pemira_user'@'localhost' IDENTIFIED BY 'password_kamu';
GRANT ALL PRIVILEGES ON pemira_db.* TO 'pemira_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

> Di Windows dengan Laragon, database bisa juga dibuat lewat phpMyAdmin/HeidiSQL bawaan Laragon tanpa perlu command line.

### 3. Install dependency PHP & JavaScript

```bash
composer install
npm install
```

### 4. Konfigurasi environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit file `.env`, sesuaikan bagian berikut:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=pemira_db
DB_USERNAME=pemira_user
DB_PASSWORD=password_kamu

APP_TIMEZONE=Asia/Jakarta
```

### 5. Jalankan migration & buat akun admin

```bash
php artisan migrate
php artisan storage:link
php artisan make:filament-user
```

### 6. Compile asset & jalankan server

Buka dua terminal terpisah (atau gunakan `composer run dev` yang menjalankan semuanya sekaligus):

```bash
# Terminal 1
npm run dev

# Terminal 2
php artisan serve
```

Buka `http://localhost:8000` untuk halaman publik, dan `http://localhost:8000/admin` untuk panel admin.

---

## Troubleshooting Umum

| Gejala | Kemungkinan Penyebab | Solusi |
|---|---|---|
| `composer require maatwebsite/excel` gagal, menyebut versi PHP tidak didukung | PHP 8.5 belum didukung PhpSpreadsheet | Gunakan PHP 8.2–8.4 (lihat bagian instalasi di atas) |
| `could not find driver` saat migrate | Ekstensi `pdo_mysql` belum aktif | Aktifkan di `php.ini`, lalu restart terminal/server |
| Foto/gambar tidak muncul di halaman publik | Berkas tersimpan di disk yang salah | Pastikan `FileUpload` dan `Storage::url()` memakai `disk('public')` secara eksplisit, lalu jalankan `php artisan storage:link` |
| Composer/npm sangat lambat | Koneksi ke registry default lambat dari lokasi tertentu | Coba `composer config -g repos.packagist composer https://packagist.org` atau gunakan mirror npm lokal |
| Halaman voting menunjukkan status waktu yang salah | Timezone aplikasi belum disesuaikan | Set `APP_TIMEZONE=Asia/Jakarta` di `.env` dan `'timezone' => 'Asia/Jakarta'` di `config/app.php`, lalu `php artisan config:clear` |
| Error `No hint path defined for [layouts]` | Komponen Livewire full-page belum diberi atribut Layout | Tambahkan `#[Layout('components.layouts.app')]` di atas class komponen |

---

*Dokumen ini adalah pelengkap dari buku dokumentasi utama "Membangun Pemira Online" yang mencatat seluruh proses pengembangan project ini secara lebih mendalam, termasuk histori debugging dan alasan di balik setiap keputusan teknis.*
