# Context Project Pemira Online

## Stack

-   Laravel 13.20
-   Filament 5.6.8
-   Livewire 4.3.3
-   Railway
-   MySQL Railway
-   PHP 8.4

## 1. Deploy Railway

### Masalah

Deploy gagal karena Railway awalnya memakai PHP 8.3 sedangkan
composer.lock berisi package Symfony 8 yang membutuhkan PHP \>= 8.4.1.
Selain itu extension `intl`, `gd`, dan `zip` belum tersedia.

### Solusi

-   Menggunakan Railpack dengan PHP 8.4.
-   Menambahkan requirement extension pada `composer.json`.

Perubahan penting:

``` json
"require": {
    "php": "^8.4",
    "ext-gd": "*",
    "ext-intl": "*",
    "ext-zip": "*"
}
```

## 2. Database Railway

Awalnya memakai:

    DB_USERNAME=pemira_user

menghasilkan:

    SQLSTATE[HY000] [1045] Access denied

Solusi: Railway MySQL ternyata menggunakan:

    MYSQLUSER=root

Sehingga production memakai:

    DB_USERNAME=root

## 3. Filament Login (403)

Setelah deploy, `/admin` selalu 403 walaupun admin sudah dibuat memakai:

    php artisan make:filament-user

### Penyebab

Model `User` belum mengimplementasikan `FilamentUser`.

### Perubahan User.php

``` php
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasFactory, Notifiable;

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
```

Setelah ditambahkan `implements FilamentUser` dan `canAccessPanel()`,
login admin berhasil.

## 4. AdminPanelProvider

Tidak ada perubahan khusus.

Masih menggunakan:

``` php
->login()
->path('admin')
->authMiddleware([
    Authenticate::class,
])
```

Tidak ada middleware custom.

## 5. Vite

CSS Filament sempat hilang.

`vite.config.js` sudah benar:

``` js
laravel({
    input: [
        'resources/css/app.css',
        'resources/js/app.js',
        'resources/css/filament/admin/theme.css',
    ],
})
```

Setelah build ulang, tampilan kembali normal.

## 6. APP_URL

Production:

    APP_URL=https://web-pemira-production.up.railway.app

`php artisan about` di Railway sudah menunjukkan URL production.

## 7. Status Saat Ini

Sudah berhasil:

-   Deploy Railway
-   Database Railway
-   Login Filament
-   Dashboard
-   CSS Filament

## 8. Masalah yang Masih Terjadi

Upload file Livewire hanya gagal di Railway.

Local berjalan normal.

Baik upload gambar maupun upload Excel menghasilkan:

    POST /livewire-xxxx/upload-file
    401 Unauthorized

Yang sudah dicek:

-   APP_URL benar
-   HTTPS benar
-   Session aktif
-   Cookie terkirim
-   CSRF token terkirim
-   request()-\>isSecure() == true
-   Route upload Livewire ada
-   Middleware route normal
-   Tidak ada middleware custom
-   Session:
    -   driver = database
    -   domain = null
    -   same_site = lax
    -   secure = null

Fokus berikutnya adalah mencari penyebab endpoint
`livewire-*/upload-file` mengembalikan 401 Unauthorized di Railway
walaupun login, session, dan CSRF semuanya valid.
