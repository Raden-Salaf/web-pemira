<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Mahasiswa extends Model
{
    use HasFactory;

    // $fillable = kolom yang BOLEH diisi lewat mass-assignment (create()/update())
    // Ini penting untuk KEAMANAN: mencegah orang iseng nge-inject kolom lain
    // yang gak seharusnya bisa diisi user (misal id, timestamps)
    protected $fillable = [
        'nim',
        'nama',
        'jurusan',
        'fakultas',
        'angkatan',
    ];

    /**
     * Relasi: 1 mahasiswa bisa terdaftar di BANYAK pemilihan (lewat tabel pemilihs)
     * Contoh pemakaian nanti: $mahasiswa->pemilihs untuk lihat semua pendaftaran dia
     */
    public function pemilihs(): HasMany
    {
        return $this->hasMany(Pemilih::class);
    }
}