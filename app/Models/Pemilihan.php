<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Pemilihan extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'jenis',
        'deskripsi',
        'status',
        'sumber_pemilih',
        'waktu_mulai',
        'waktu_selesai',
        'is_active',
    ];

    // $casts: otomatis konversi tipe data pas diambil dari database
    // 'datetime' bikin waktu_mulai & waktu_selesai jadi objek Carbon (bukan string biasa),
    // supaya kita bisa langsung pakai method perbandingan waktu kayak isPast(), isFuture(), dll
    protected $casts = [
        'waktu_mulai' => 'datetime',
        'waktu_selesai' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function paslons(): HasMany
    {
        return $this->hasMany(Paslon::class);
    }

    public function pemilihs(): HasMany
    {
        return $this->hasMany(Pemilih::class);
    }

    public function suaras(): HasMany
    {
        return $this->hasMany(Suara::class);
    }

    /**
     * INI METHOD KRUSIAL — dipakai di banyak tempat nanti:
     * - Buat nampilin/nyembunyiin tombol "Coblos Sekarang" di halaman publik
     * - Buat validasi server-side pas user submit vote (JANGAN PERNAH cuma
     *   andalkan tombol disembunyikan di frontend, karena user bisa akses
     *   endpoint langsung tanpa lewat tombol!)
     *
     * Voting dianggap "sedang berlangsung" HANYA kalau 3 syarat ini semua benar:
     * 1. Status di database memang 'berlangsung' (admin udah set manual)
     * 2. Waktu sekarang SUDAH lewat waktu_mulai
     * 3. Waktu sekarang BELUM lewat waktu_selesai
     */
    public function isVotingBuka(): bool
    {
        if ($this->status !== 'berlangsung') {
            return false;
        }

        $sekarang = Carbon::now();

        // waktu_mulai/waktu_selesai nullable, jadi kita cek dulu sebelum bandingin
        // (mencegah error kalau admin lupa isi jadwal tapi status udah 'berlangsung')
        if ($this->waktu_mulai && $sekarang->lt($this->waktu_mulai)) {
            return false; // belum waktunya mulai
        }

        if ($this->waktu_selesai && $sekarang->gt($this->waktu_selesai)) {
            return false; // sudah lewat waktu selesai
        }

        return true;
    }

    /**
     * Cek apakah waktu pemungutan suara SUDAH LEWAT (waktu_selesai sudah terlampaui).
     * Beda dengan isVotingBuka() yang cek "apakah BOLEH vote SEKARANG",
     * method ini spesifik buat kasih tau alasan KENAPA gak bisa vote --
     * apakah karena "belum waktunya" atau "udah lewat waktunya".
     */
    public function sudahBerakhir(): bool
    {
        return $this->waktu_selesai !== null && \Illuminate\Support\Carbon::now()->gt($this->waktu_selesai);
    }

    /**
     * Cek apakah waktu pemungutan suara BELUM DIMULAI (masih menunggu waktu_mulai).
     */
    public function belumMulai(): bool
    {
        return $this->waktu_mulai !== null && \Illuminate\Support\Carbon::now()->lt($this->waktu_mulai);
    }

    /**
     * Hitung total suara masuk untuk pemilihan ini (dari SEMUA paslon)
     * Dipakai buat nampilin "total suara masuk" di dashboard admin/publik
     */
    public function totalSuaraMasuk(): int
    {
        return $this->suaras()->count();
    }

    /**
     * boot() dipanggil sekali saat Model pertama kali dimuat Laravel.
     * Kita "pasang" event listener di sini: SETIAP kali ada Pemilihan baru
     * mau disimpan (creating), otomatis generate slug dari nama-nya --
     * supaya admin gak perlu mikirin/isi slug manual sendiri.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (Pemilihan $pemilihan) {
            if (empty($pemilihan->slug)) {
                $slug = Str::slug($pemilihan->nama);

                // Kalau ternyata slug ini SUDAH dipakai pemilihan lain (misal nama
                // sama persis "Pemilihan Hima" dibuat 2x di tahun berbeda), kita
                // tambahkan angka di belakang biar tetap unik: "pemilihan-hima-2"
                $slugAsli = $slug;
                $counter = 1;
                while (static::where('slug', $slug)->exists()) {
                    $slug = "{$slugAsli}-" . (++$counter);
                }

                $pemilihan->slug = $slug;
            }
        });
    }

    /**
     * Ini yang bikin Laravel otomatis pakai kolom "slug" (bukan "id") setiap
     * kali ada Route Model Binding seperti {pemilihan} di routes/web.php.
     * Jadi SEMUA route yang sudah kita buat (daftar, voting, hasil, profil)
     * otomatis ikut pakai slug tanpa perlu kita ubah satu-satu lagi.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Relasi ke mahasiswa yang MASUK WHITELIST pemilihan ini (mode manual).
     * belongsToMany karena hubungannya lewat tabel pivot pemilihan_daftar_manual.
     */
    public function whitelistManual(): BelongsToMany
    {
        return $this->belongsToMany(Mahasiswa::class, 'pemilihan_daftar_manual')->withTimestamps();
    }

    /**
     * INI LOGIC KRUSIAL FITUR WHITELIST.
     * Dipanggil dari DaftarPemilih.php untuk mengecek: mahasiswa ini
     * BOLEH gak sih daftar di pemilihan ini?
     *
     * Kalau sumber_pemilih = 'excel' -> semua mahasiswa hasil import boleh coba.
     * Kalau 'manual' -> HARUS terdaftar di whitelist khusus pemilihan ini.
     */
    public function bolehDaftar(Mahasiswa $mahasiswa): bool
    {
        if ($this->sumber_pemilih === 'excel') {
            return true;
        }

        return $this->whitelistManual()->where('mahasiswa_id', $mahasiswa->id)->exists();
    }
}
