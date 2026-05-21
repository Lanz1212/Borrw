<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('returns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->cascadeOnDelete();
            $table->foreignId('transaction_detail_id')->constrained('transaction_details')->cascadeOnDelete();
            $table->foreignId('inventory_id')->nullable()->constrained('inventory')->nullOnDelete();
            $table->string('item_name');
            $table->unsignedInteger('qty_returned')->default(0);
            $table->unsignedInteger('qty_good')->default(0);
            $table->unsignedInteger('qty_damaged')->default(0);
            $table->unsignedInteger('qty_lost')->default(0);
            $table->string('condition')->default('baik');
            $table->text('notes')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('processed_by_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('returns');
    }
};
