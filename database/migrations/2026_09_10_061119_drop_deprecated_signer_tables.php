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
        Schema::dropIfExists('leave_signatory_configs');
        Schema::dropIfExists('signer_officials');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No down migration needed as these are deprecated tables
    }
};
