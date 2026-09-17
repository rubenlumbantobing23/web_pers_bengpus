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
        Schema::create('marriage_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('personel_id')->constrained('personels')->cascadeOnDelete();
            $table->string('jenis_kelamin_anggota'); // pria / wanita
            $table->string('peran_anggota'); // Calon Suami / Calon Istri
            $table->date('tanggal_pengajuan');
            $table->date('tanggal_rencana_nikah');
            $table->string('tempat_nikah');
            $table->string('alamat_nikah');
            $table->string('kelurahan_nikah');
            $table->string('kecamatan_nikah');
            $table->string('kabupaten_nikah');
            $table->string('provinsi_nikah');
            $table->string('status')->default('DRAFT'); // DRAFT, DIAJUKAN, PERLU_PERBAIKAN, DIVERIFIKASI, DISETUJUI, DITOLAK, SELESAI
            $table->text('catatan_admin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marriage_applications');
    }
};
