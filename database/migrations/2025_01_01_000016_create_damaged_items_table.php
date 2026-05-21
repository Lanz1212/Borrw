<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('damaged_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_id')->nullable()->constrained('inventory')->nullOnDelete();
            $table->string('item_name');
            $table->unsignedInteger('qty')->default(1);
            $table->text('description')->nullable();
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reported_by_name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('damaged_items');
    }
};
