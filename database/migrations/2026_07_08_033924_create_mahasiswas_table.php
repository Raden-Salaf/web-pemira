<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel ini adalah "data master" mahasiswa, hasil import dari file Excel oleh admin.
     * Data di sini TIDAK terikat pada pemilihan tertentu — ini murni data identitas kampus.
     */
    public function up(): void
    {
        Schema::create('mahasiswas', function (Blueprint $table) {
            $table->id(); // primary key auto-increment (bigint)

            // NIM dibuat UNIQUE karena ini identitas utama mahasiswa,
            // gak boleh ada 2 mahasiswa dengan NIM yang sama (mencegah duplikat data)
            $table->string('nim')->unique();

            $table->string('nama');
            $table->string('jurusan')->nullable();  // nullable: siapa tau file excel admin gak selalu punya kolom ini
            $table->string('fakultas')->nullable(); // dipakai nanti buat filter paslon per fakultas (requirement poin 11)
            $table->string('angkatan')->nullable(); // opsional, berguna kalau pemilihan dibatasi angkatan tertentu

            // Timestamps otomatis: created_at & updated_at
            // Berguna buat tracking kapan data ini terakhir di-import/update dari Excel
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // down() dipanggil kalau kita rollback migration ini
        // dropIfExists lebih aman daripada drop biasa, gak error kalau tabel udah gak ada
        Schema::dropIfExists('mahasiswas');
    }
};