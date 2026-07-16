<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pemilihans', function (Blueprint $table) {
            $table->enum('sumber_pemilih', ['excel', 'manual'])->default('excel')->after('jenis');
        });
    }

    public function down(): void
    {
        Schema::table('pemilihans', function (Blueprint $table) {
            $table->dropColumn('sumber_pemilih');
        });
    }
};