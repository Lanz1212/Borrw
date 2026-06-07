<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE inventory MODIFY COLUMN type ENUM('pinjam', 'consumable', 'bon') NOT NULL DEFAULT 'pinjam'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE inventory MODIFY COLUMN type ENUM('pinjam', 'consumable') NOT NULL DEFAULT 'pinjam'");
    }
};
