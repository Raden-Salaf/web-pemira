<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel pencatat suara.
     *
     * PRINSIP PENTING (asas pemilu: LUBER JURDIL - salah satunya RAHASIA):
     * Tabel ini SENGAJA tidak punya kolom "pemilih_id" atau "mahasiswa_id".
     * Kenapa? Karena kalau ada, siapapun yang akses database bisa tau
     * "si A dari NIM sekian milih paslon nomor sekian" — ini melanggar
     * asas kerahasiaan suara.
     *
     * Pencegahan vote ganda TIDAK dilakukan lewat tabel ini,
     * tapi lewat flag "sudah_memilih" di tabel "pemilihs" (lihat migration sebelumnya).
     * Jadi tabel "suaras" ini isinya cuma: "paslon X dapat 1 suara di pemilihan Y,
     * pada waktu Z" — gak ada jejak siapa yang mencoblos.
     */
    public function up(): void
    {
        Schema::create('suaras', function (Blueprint $table) {
            $table->id();

            $table->foreignId('paslon_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pemilihan_id')->constrained()->cascadeOnDelete();

            // created_at cukup buat tau kapan vote masuk (misal buat grafik real-time nanti)
            // TIDAK ADA updated_at yang perlu diubah-ubah, karena vote itu sifatnya "insert sekali, gak pernah diubah"
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suaras');
    }
};