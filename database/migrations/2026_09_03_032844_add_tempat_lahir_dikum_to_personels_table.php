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
            $table->string('tempat_lahir')->nullable();
            $table->string('dikum_ti')->nullable();
            $table->string('thn_lulus_dikum')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personels', function (Blueprint $table) {
            $table->dropColumn([
                'tempat_lahir',
                'dikum_ti',
                'thn_lulus_dikum',
            ]);
        });
    }
};
