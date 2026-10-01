<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        // Kolom read_at dan timestamps sudah tersedia
        // pada tabel notifications.
    }

    public function down(): void
    {
        // Tidak ada perubahan yang perlu di-rollback.
    }
};