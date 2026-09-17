<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('marriage_applications', function (Blueprint $table) {
            $table->string('alamat_domisili')->nullable();
            $table->string('kelurahan_domisili')->nullable();
            $table->string('kecamatan_domisili')->nullable();
            $table->string('kabupaten_domisili')->nullable();
            $table->string('provinsi_domisili')->nullable();
            $table->string('kua_tujuan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marriage_applications', function (Blueprint $table) {
            $table->dropColumn([
                'alamat_domisili',
                'kelurahan_domisili',
                'kecamatan_domisili',
                'kabupaten_domisili',
                'provinsi_domisili',
                'kua_tujuan'
            ]);
        });
    }
};
