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
            $table->string('tmt_pangkat')->nullable();
            $table->string('corps')->nullable();
            $table->string('tmt_jabatan')->nullable();
            $table->string('tmt_tni_pa')->nullable();
            $table->string('agama_suku')->nullable();
            $table->date('tgl_lahir')->nullable();
            $table->string('mkg')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->string('dik_pertama_tni')->nullable();
            $table->string('thn_lulus_dik_pertama')->nullable();
            $table->string('dikmit_tni')->nullable();
            $table->string('thn_lulus_dikmit')->nullable();
            $table->text('ket')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personels', function (Blueprint $table) {
            $table->dropColumn([
                'tmt_pangkat',
                'corps',
                'tmt_jabatan',
                'tmt_tni_pa',
                'agama_suku',
                'tgl_lahir',
                'mkg',
                'jenis_kelamin',
                'dik_pertama_tni',
                'thn_lulus_dik_pertama',
                'dikmit_tni',
                'thn_lulus_dikmit',
                'ket',
            ]);
        });
    }
};
