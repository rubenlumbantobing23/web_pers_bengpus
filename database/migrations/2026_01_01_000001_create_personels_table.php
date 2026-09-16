<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nrp_nip')->unique();
            $table->string('nama');
            $table->string('pangkat_golongan');
            $table->string('jabatan');
            $table->string('satuan_bagian')->default('Bengpuskomlekad');
            $table->string('no_hp')->nullable();
            $table->string('email')->nullable();
            $table->boolean('status_aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('personels');
    }
};
