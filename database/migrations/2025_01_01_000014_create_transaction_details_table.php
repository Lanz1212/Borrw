<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->cascadeOnDelete();
            $table->foreignId('inventory_id')->nullable()->constrained('inventory')->nullOnDelete();
            $table->string('item_name');
            $table->string('item_code');
            $table->enum('item_type', ['pinjam', 'consumable'])->default('pinjam');
            $table->unsignedInteger('qty')->default(1);
            $table->enum('status', ['dipinjam', 'dipakai', 'kembali', 'partial'])->default('dipinjam');
            $table->unsignedInteger('qty_returned')->default(0);
            $table->timestamp('return_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_details');
    }
};
