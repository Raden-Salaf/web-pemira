<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tabel PENGHUBUNG antara mahasiswa & pemilihan.
     * Inilah tempat status DPS -> DPT (requirement poin 2 & 6) dikelola.
     *
     * Kenapa gak taruh status ini langsung di tabel "mahasiswas"?
     * Karena 1 mahasiswa BISA ikut lebih dari 1 pemilihan (misal ikut Pemira BEM
     * SEKALIGUS Pemilihan Hima jurusannya) — dan statusnya bisa beda-beda
     * di tiap pemilihan itu. Makanya harus tabel terpisah, bukan kolom di mahasiswas.
     */
    public function up(): void
    {
        Schema::create('pemilihs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('mahasiswa_id')->constrained()->cascadeOnDelete();
            $table->foreignId('pemilihan_id')->constrained()->cascadeOnDelete();

            // status ini yang jadi jawaban requirement poin 2:
            // 'dps' = Daftar Pemilih Sementara (baru daftar/ajukan verifikasi, belum di-approve admin)
            // 'dpt' = Daftar Pemilih Tetap (sudah di-approve admin, BOLEH mencoblos)
            // 'ditolak' = admin menolak pengajuan (misal data gak valid)
            $table->enum('status', ['dps', 'dpt', 'ditolak'])->default('dps');

            // FLAG PALING KRUSIAL DI SELURUH SISTEM INI.
            // sudah_memilih dipakai buat mencegah 1 mahasiswa mencoblos LEBIH DARI 1 KALI.
            // Begini alurnya nanti di logic voting:
            // 1. Cek dulu status == 'dpt' (harus DPT dulu baru boleh akses halaman coblos)
            // 2. Cek sudah_memilih == false (belum pernah mencoblos)
            // 3. Kalau lolos 2 syarat itu: simpan vote ke tabel "suaras", 
            //    LALU update sudah_memilih jadi true di baris ini
            // Dengan begini, walau seseorang coba akses halaman voting berkali-kali,
            // sistem akan selalu nolak di percobaan ke-2 dst karena flag ini sudah true.
            $table->boolean('sudah_memilih')->default(false);

            $table->timestamp('diverifikasi_at')->nullable(); // kapan admin approve jadi DPT
            $table->timestamp('waktu_memilih')->nullable(); // kapan dia mencoblos (buat log/audit, BUKAN buat tau pilih siapa)

            $table->timestamps();

            // UNIQUE COMPOSITE: kombinasi mahasiswa_id + pemilihan_id harus unik.
            // Ini mencegah 1 mahasiswa punya 2 baris pendaftaran di pemilihan yang SAMA
            // (kalau gak dikasih constraint ini, secara teori bisa aja ke-insert 2x
            // gara-gara double-click tombol daftar misalnya)
            $table->unique(['mahasiswa_id', 'pemilihan_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemilihs');
    }
};