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
        Schema::table('marriage_applications', function (Blueprint $table) {
            $table->string('danramil_tujuan')->nullable()->after('kua_tujuan');
            $table->string('dandim_tujuan')->nullable()->after('danramil_tujuan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marriage_applications', function (Blueprint $table) {
            $table->dropColumn(['danramil_tujuan', 'dandim_tujuan']);
        });
    }
};
