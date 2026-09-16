<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internal_letters', function (Blueprint $table) {
            $table->id();
            $table->string('letter_number');
            $table->date('letter_date');
            $table->string('category');
            $table->string('subject');
            $table->text('description')->nullable();
            $table->string('file_path');
            $table->integer('file_size')->nullable();
            $table->foreignId('uploader_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internal_letters');
    }
};
