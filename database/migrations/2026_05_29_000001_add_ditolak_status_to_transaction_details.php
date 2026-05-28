<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Tambahkan nilai 'ditolak' ke ENUM status di transaction_details
        DB::statement("ALTER TABLE transaction_details MODIFY COLUMN status ENUM('dipinjam','dipakai','kembali','partial','ditolak') NOT NULL DEFAULT 'dipinjam'");
    }

    public function down(): void
    {
        // Kembalikan ke tanpa 'ditolak' (pastikan tidak ada data 'ditolak' dulu)
        DB::statement("UPDATE transaction_details SET status = 'dipinjam' WHERE status = 'ditolak'");
        DB::statement("ALTER TABLE transaction_details MODIFY COLUMN status ENUM('dipinjam','dipakai','kembali','partial') NOT NULL DEFAULT 'dipinjam'");
    }
};
