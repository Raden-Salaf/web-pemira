<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel "daftar tamu VIP" -- whitelist khusus per pemilihan.
     * Kalau sebuah Pemilihan pakai sumber_pemilih = 'manual', mahasiswa
     * cuma boleh daftar DPS kalau namanya ADA di tabel ini untuk
     * pemilihan yang bersangkutan. Beda dengan tabel 'pemilihs' (yang
     * mencatat SIAPA SAJA yang SUDAH daftar), tabel ini mencatat SIAPA
     * SAJA yang BOLEH daftar -- disiapkan admin SEBELUM pendaftaran dibuka.
     */
    public function up(): void
    {
        Schema::create('pemilihan_daftar_manual', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemilihan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('mahasiswa_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            // Mencegah 1 mahasiswa dimasukkan ke whitelist yang SAMA 2 kali
            $table->unique(['pemilihan_id', 'mahasiswa_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemilihan_daftar_manual');
    }
};