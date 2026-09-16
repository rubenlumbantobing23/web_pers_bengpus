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
        Schema::create('organization_official_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('organization_unit_id');
            $table->unsignedBigInteger('personel_id');
            $table->string('role');
            $table->boolean('is_active')->default(true);
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->timestamps();

            $table->foreign('organization_unit_id')->references('id')->on('organization_units')->onDelete('cascade');
            $table->foreign('personel_id')->references('id')->on('personels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('organization_official_assignments');
    }
};
