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
        Schema::create('marriage_letters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marriage_application_id')->constrained('marriage_applications')->cascadeOnDelete();
            $table->string('jenis_surat');
            $table->string('nomor_surat')->nullable();
            $table->string('file_generated');
            $table->string('status');
            $table->timestamp('generated_at')->nullable();
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marriage_letters');
    }
};
