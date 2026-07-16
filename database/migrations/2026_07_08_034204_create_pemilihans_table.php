<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel ini adalah INTI dari fleksibilitas sistem kita.
     * Setiap baris di sini = 1 "acara pemilihan" yang berdiri sendiri
     * (Pemira BEM, Pemilihan Ketua Himatif, dst), lengkap dengan jadwalnya sendiri.
     */
    public function up(): void
    {
        Schema::create('pemilihans', function (Blueprint $table) {
            $table->id();

            $table->string('nama'); // contoh: "Pemira BEM 2026", "Pemilihan Ketua Himatif 2026"

            // jenis dipakai buat bedain tampilan/logic khusus kalau perlu
            // (misal: pemilihan "bem" nanti nampilin halaman perkenalan khusus per requirement poin 11)
            $table->string('jenis')->default('umum'); // contoh isi: 'bem', 'hima', 'umum'

            $table->text('deskripsi')->nullable(); // deskripsi acara, ditampilkan di halaman publik

            // status ini yang dipakai buat kontrol "buka/tutup" pemungutan suara (requirement poin 4)
            // enum dipakai supaya nilai yang bisa dimasukkan TERBATAS, gak bisa asal ketik string sembarangan
            $table->enum('status', ['draft', 'berlangsung', 'selesai'])->default('draft');

            // waktu_mulai & waktu_selesai: jadwal otomatis buka-tutup voting
            // nullable karena admin mungkin belum nentuin jadwal pas awal bikin (masih draft)
            $table->dateTime('waktu_mulai')->nullable();
            $table->dateTime('waktu_selesai')->nullable();

            // is_active: flag tambahan untuk nentuin pemilihan MANA yang lagi "tampil" di halaman utama publik
            // (bisa aja ada banyak record pemilihan tersimpan sebagai arsip, tapi cuma 1 yang aktif ditampilkan)
            $table->boolean('is_active')->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemilihans');
    }
};