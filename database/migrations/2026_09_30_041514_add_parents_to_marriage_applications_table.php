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
            $table->string('bapak_anggota_nama')->nullable()->after('provinsi_domisili');
            $table->string('bapak_anggota_agama')->nullable()->after('bapak_anggota_nama');
            $table->string('bapak_anggota_pekerjaan')->nullable()->after('bapak_anggota_agama');
            $table->string('bapak_anggota_alamat', 500)->nullable()->after('bapak_anggota_pekerjaan');
            $table->string('ibu_anggota_nama')->nullable()->after('bapak_anggota_alamat');
            $table->string('ibu_anggota_agama')->nullable()->after('ibu_anggota_nama');
            $table->string('ibu_anggota_pekerjaan')->nullable()->after('ibu_anggota_agama');
            $table->string('ibu_anggota_alamat', 500)->nullable()->after('ibu_anggota_pekerjaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marriage_applications', function (Blueprint $table) {
            $table->dropColumn([
                'bapak_anggota_nama',
                'bapak_anggota_agama',
                'bapak_anggota_pekerjaan',
                'bapak_anggota_alamat',
                'ibu_anggota_nama',
                'ibu_anggota_agama',
                'ibu_anggota_pekerjaan',
                'ibu_anggota_alamat',
            ]);
        });
    }
};
