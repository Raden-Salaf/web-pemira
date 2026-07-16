<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paslon extends Model
{
    use HasFactory;

    protected $fillable = [
        'pemilihan_id',
        'nomor_urut',
        'nama_ketua',
        'nama_wakil',
        'foto',
        'visi',
        'misi',
        'program_kerja',
        'fakultas_asal',
    ];

    // program_kerja disimpan sebagai JSON di database,
    // cast 'array' bikin Laravel OTOMATIS encode/decode JSON <-> PHP array
    // Jadi kita bisa langsung pakai $paslon->program_kerja sebagai array PHP biasa,
    // gak perlu manual json_encode()/json_decode()
    protected $casts = [
        'program_kerja' => 'array',
    ];

    public function pemilihan(): BelongsTo
    {
        return $this->belongsTo(Pemilihan::class);
    }

    public function suaras(): HasMany
    {
        return $this->hasMany(Suara::class);
    }

    /**
     * Hitung berapa suara yang didapat paslon ini
     */
    public function jumlahSuara(): int
    {
        return $this->suaras()->count();
    }

    /**
     * INI LOGIC PERSENTASE (requirement poin 5).
     * Persentase = (suara paslon ini / total suara SEMUA paslon di pemilihan yang sama) * 100
     *
     * Kenapa totalnya diambil dari $this->pemilihan->totalSuaraMasuk(),
     * bukan dari Suara::count() global?
     * Karena kalau ada banyak pemilihan tersimpan (arsip pemilihan lama + yang aktif),
     * kita HARUS hitung persentase relatif terhadap pemilihan yang sama aja,
     * bukan gabung sama suara dari pemilihan lain yang gak nyambung.
     */
    public function persentaseSuara(): float
    {
        $totalSuaraPemilihan = $this->pemilihan->totalSuaraMasuk();

        // Mencegah division by zero — kalau belum ada suara masuk sama sekali,
        // langsung return 0 daripada error
        if ($totalSuaraPemilihan === 0) {
            return 0.0;
        }

        return round(($this->jumlahSuara() / $totalSuaraPemilihan) * 100, 2);
    }
}