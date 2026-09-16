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
        Schema::table('personels', function (Blueprint $table) {
            $table->enum('kategori_personel', ['Perwira Menengah', 'Perwira Pertama', 'Bintara', 'Tamtama', 'PNS'])->nullable()->after('jenis_personel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personels', function (Blueprint $table) {
            $table->dropColumn('kategori_personel');
        });
    }
};
