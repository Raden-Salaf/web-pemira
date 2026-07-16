<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Suara extends Model
{
    use HasFactory;

    protected $fillable = [
        'paslon_id',
        'pemilihan_id',
    ];

    // Suara TIDAK PERLU updated_at karena data ini bersifat "insert sekali, gak pernah diubah"
    // (kalau dibiarkan default Laravel akan expect 2 kolom timestamp, padahal migration kita cuma bikin created_at)
    const UPDATED_AT = null;

    public function paslon(): BelongsTo
    {
        return $this->belongsTo(Paslon::class);
    }

    public function pemilihan(): BelongsTo
    {
        return $this->belongsTo(Pemilihan::class);
    }
}