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
        Schema::create('marriage_partners', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marriage_application_id')->constrained('marriage_applications')->cascadeOnDelete();
            $table->string('peran'); // Calon Suami / Calon Istri
            $table->string('nama');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->string('pekerjaan');
            $table->string('status_pekerjaan'); // ASN / Non-ASN
            $table->string('instansi')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('agama');
            $table->string('suku');
            $table->string('alamat');
            $table->string('kelurahan');
            $table->string('kecamatan');
            $table->string('kabupaten');
            $table->string('provinsi');
            
            // Data Orang Tua/Wali
            $table->string('bapak_nama');
            $table->string('bapak_agama');
            $table->string('bapak_pekerjaan');
            $table->string('bapak_alamat');
            $table->string('ibu_nama');
            $table->string('ibu_agama');
            $table->string('ibu_pekerjaan');
            $table->string('ibu_alamat');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marriage_partners');
    }
};
