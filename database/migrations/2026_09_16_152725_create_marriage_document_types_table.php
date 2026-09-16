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
        Schema::create('marriage_document_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('category'); // SURAT_SATUAN, TEMPLATE_CALON, HASIL_INSTANSI, DOKUMEN_ANGGOTA, DOKUMEN_PASANGAN, SURAT_FINAL
            $table->string('owner_type'); // ANGGOTA, PASANGAN, ORANG_TUA_PASANGAN, SATUAN
            $table->string('source_type'); // SYSTEM, SATUAN, INSTANSI_LUAR, ANGGOTA, PASANGAN, ORANG_TUA
            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marriage_document_types');
    }
};
