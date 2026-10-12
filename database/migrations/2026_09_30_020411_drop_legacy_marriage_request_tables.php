<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // PENTING: Dibatalkan untuk menjaga data historis
        // Schema::dropIfExists('marriage_request_documents');
        // Schema::dropIfExists('marriage_requests');
    }

    public function down(): void
    {
        // Dibatalkan karena up() tidak melakukan drop
    }
};
