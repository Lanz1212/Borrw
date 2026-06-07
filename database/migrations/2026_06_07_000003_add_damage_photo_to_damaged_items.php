<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('damaged_items', function (Blueprint $table) {
            $table->string('damage_photo')->nullable()->after('condition_notes');
        });
    }

    public function down(): void
    {
        Schema::table('damaged_items', function (Blueprint $table) {
            $table->dropColumn('damage_photo');
        });
    }
};
