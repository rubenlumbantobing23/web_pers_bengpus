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
        Schema::table('marriage_documents', function (Blueprint $table) {
            // Nullable so it doesn't break existing records
            $table->foreignId('marriage_document_type_id')->nullable()->after('marriage_application_id')->constrained('marriage_document_types')->nullOnDelete();
            // We keep jenis_dokumen as requested by user to not delete before verifying.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('marriage_documents', function (Blueprint $table) {
            $table->dropForeign(['marriage_document_type_id']);
            $table->dropColumn('marriage_document_type_id');
        });
    }
};
