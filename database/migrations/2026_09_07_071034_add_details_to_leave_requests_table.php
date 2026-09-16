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
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->string('tujuan')->nullable()->after('reason');
            $table->string('pengikut')->nullable()->after('tujuan');
            $table->string('kendaraan')->nullable()->after('pengikut');
            $table->string('kodim_koramil')->nullable()->after('kendaraan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn(['tujuan', 'pengikut', 'kendaraan', 'kodim_koramil']);
        });
    }
};
