<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('marriage_request_documents');
        Schema::dropIfExists('marriage_requests');
    }

    public function down(): void
    {
        Schema::create('marriage_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('request_number')->unique();
            $table->string('spouse_name');
            $table->string('spouse_nrp_nip')->nullable();
            $table->string('spouse_occupation')->nullable();
            $table->date('marriage_date');
            $table->string('marriage_location');
            $table->string('status')->default('pending'); // pending, approved, rejected, cancelled
            $table->text('rejection_reason')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });

        Schema::create('marriage_request_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marriage_request_id')->constrained('marriage_requests')->cascadeOnDelete();
            $table->string('document_name');
            $table->string('file_path');
            $table->integer('file_size')->nullable();
            $table->timestamps();
        });
    }
};
