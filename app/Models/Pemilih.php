<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pemilih extends Model
{
    use HasFactory;

    // Override nama tabel karena Laravel bakal nebak "pemiliharen" atau semacamnya
    // dari pluralisasi otomatis kata "Pemilih" — kita pastikan eksplisit aja biar gak ambigu
    protected $table = 'pemilihs';

    protected $fillable = [
        'mahasiswa_id',
        'pemilihan_id',
        'status',
        'sudah_memilih',
        'diverifikasi_at',
        'waktu_memilih',
    ];

    protected $casts = [
        'sudah_memilih' => 'boolean',
        'diverifikasi_at' => 'datetime',
        'waktu_memilih' => 'datetime',
    ];

    public function mahasiswa(): BelongsTo
    {
        return $this->belongsTo(Mahasiswa::class);
    }

    public function pemilihan(): BelongsTo
    {
        return $this->belongsTo(Pemilihan::class);
    }

    /**
     * Cek apakah mahasiswa ini BOLEH mencoblos SEKARANG.
     * Ini gabungan dari 3 syarat penting:
     * 1. Status dia harus 'dpt' (sudah diverifikasi admin)
     * 2. Belum pernah mencoblos sebelumnya
     * 3. Pemilihannya sendiri memang lagi dalam masa voting terbuka
     *
     * Method ini akan dipanggil di controller SEBELUM proses simpan vote,
     * sebagai lapisan validasi terakhir sebelum data masuk ke tabel suaras.
     */
    public function bolehMemilih(): bool
    {
        return $this->status === 'dpt'
            && ! $this->sudah_memilih
            && $this->pemilihan->isVotingBuka();
    }
}