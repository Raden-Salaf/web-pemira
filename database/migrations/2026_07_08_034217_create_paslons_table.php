<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Data kandidat/paslon. Setiap paslon WAJIB terikat ke 1 pemilihan tertentu,
     * karena paslon "Nomor 1" di Pemira BEM beda sama paslon "Nomor 1" di Pemilihan Hima.
     */
    public function up(): void
    {
        Schema::create('paslons', function (Blueprint $table) {
            $table->id();

            // foreignId() otomatis bikin kolom "pemilihan_id" tipe unsignedBigInteger
            // constrained() otomatis nyambungin ke tabel "pemilihans" (Laravel nebak dari nama kolom)
            // cascadeOnDelete(): kalau data pemilihan-nya dihapus, semua paslon terkait IKUT terhapus otomatis
            // (mencegah data paslon "nyangkut" tanpa induk pemilihan yang jelas)
            $table->foreignId('pemilihan_id')->constrained()->cascadeOnDelete();

            $table->unsignedInteger('nomor_urut'); // nomor urut kandidat, misal 1, 2, 3

            // Karena paslon biasanya berupa PASANGAN (ketua + wakil), kita pisah 2 kolom nama
            // Kalau ternyata pemilihan yang dijalankan cuma butuh 1 kandidat (bukan pasangan),
            // tinggal kosongkan/nullable-kan wakil
            $table->string('nama_ketua');
            $table->string('nama_wakil')->nullable();

            $table->string('foto')->nullable(); // menyimpan PATH file foto, bukan file-nya langsung (best practice)

            $table->text('visi');
            $table->text('misi');

            // program_kerja disimpan sebagai JSON supaya fleksibel:
            // admin bisa isi list poin-poin program kerja tanpa kita perlu bikin tabel terpisah
            $table->json('program_kerja')->nullable();

            $table->string('fakultas_asal')->nullable(); // ditampilkan di halaman perkenalan (requirement poin 11)

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paslons');
    }
};