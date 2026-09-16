<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->integer('approved_days')->nullable()->after('working_days_count');
            $table->text('approved_notes')->nullable()->after('approved_days'); // Catatan Kabengpus
        });
    }

    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn(['approved_days', 'approved_notes']);
        });
    }
};
