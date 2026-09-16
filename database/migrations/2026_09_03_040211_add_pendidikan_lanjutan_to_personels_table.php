<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('personels', function (Blueprint $table) {
            $table->string('pendidikan_lanjutan')->nullable()->after('thn_lulus_dikmit');
            $table->string('tahun_lulus_lanjutan')->nullable()->after('pendidikan_lanjutan');
        });
    }

    public function down(): void
    {
        Schema::table('personels', function (Blueprint $table) {
            $table->dropColumn(['pendidikan_lanjutan', 'tahun_lulus_lanjutan']);
        });
    }
};
