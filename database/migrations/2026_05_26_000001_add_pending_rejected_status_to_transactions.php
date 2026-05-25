<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE transactions MODIFY COLUMN status ENUM('aktif','selesai','partial','menunggu_persetujuan','ditolak') DEFAULT 'aktif'");
        } elseif ($driver === 'sqlite') {
            DB::statement('PRAGMA foreign_keys=OFF');
            DB::statement('ALTER TABLE transactions RENAME TO _trx_bak');
            DB::statement("CREATE TABLE transactions (
                id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
                transaction_code VARCHAR(255) NOT NULL,
                borrower_id INTEGER,
                borrower_name VARCHAR(255) NOT NULL,
                loan_date DATETIME NOT NULL,
                return_date DATETIME,
                status TEXT CHECK(status IN ('aktif','selesai','partial','menunggu_persetujuan','ditolak')) NOT NULL DEFAULT 'aktif',
                notes TEXT,
                created_by INTEGER,
                created_by_name VARCHAR(255),
                signature TEXT,
                created_at DATETIME,
                updated_at DATETIME
            )");
            DB::statement('INSERT INTO transactions SELECT * FROM _trx_bak');
            DB::statement('DROP TABLE _trx_bak');
            DB::statement('PRAGMA foreign_keys=ON');
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();
        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE transactions MODIFY COLUMN status ENUM('aktif','selesai','partial') DEFAULT 'aktif'");
        }
    }
};
