<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemilihans', function (Blueprint $table) {
            // slug = versi "url-friendly" dari nama (misal "Pemira BEM 2026"
            // jadi "pemira-bem-2026"), dipakai buat URL yang rapi & mudah dibagikan
            $table->string('slug')->unique()->nullable()->after('nama');
        });
    }

    public function down(): void
    {
        Schema::table('pemilihans', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};