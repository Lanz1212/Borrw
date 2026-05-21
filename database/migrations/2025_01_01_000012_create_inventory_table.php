<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('category')->default('Umum');
            $table->enum('type', ['pinjam', 'consumable'])->default('pinjam');
            $table->unsignedInteger('total_qty')->default(0);
            $table->unsignedInteger('available_qty')->default(0);
            $table->unsignedInteger('min_stock')->default(0);
            $table->enum('condition', ['baik', 'rusak', 'perlu_perbaikan'])->default('baik');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory');
    }
};
