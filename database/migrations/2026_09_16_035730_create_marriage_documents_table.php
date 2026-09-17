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
        Schema::create('marriage_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marriage_application_id')->constrained('marriage_applications')->cascadeOnDelete();
            $table->string('pihak'); // Anggota / Pasangan
            $table->string('jenis_dokumen');
            $table->string('nama_dokumen');
            $table->string('file_path');
            $table->string('file_name');
            $table->string('mime_type');
            $table->integer('file_size');
            $table->string('status_verifikasi')->default('BELUM_DIPERIKSA'); // BELUM_DIPERIKSA, DITERIMA, DITOLAK
            $table->text('catatan_verifikasi')->nullable();
            $table->timestamp('uploaded_at')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marriage_documents');
    }
};
