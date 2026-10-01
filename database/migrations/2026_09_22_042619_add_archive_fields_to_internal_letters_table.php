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
        Schema::table('internal_letters', function (Blueprint $table) {
            $table->foreignId('letter_type_id')->nullable()->constrained('letter_types')->nullOnDelete();
            $table->string('direction', 20)->nullable();
            $table->date('received_date')->nullable();
            $table->string('sender')->nullable();
            $table->string('recipient')->nullable();
            $table->string('classification', 20)->default('BIASA');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('internal_letters', function (Blueprint $table) {
            $table->dropForeign(['letter_type_id']);
            $table->dropColumn([
                'letter_type_id',
                'direction',
                'received_date',
                'sender',
                'recipient',
                'classification'
            ]);
        });
    }
};
