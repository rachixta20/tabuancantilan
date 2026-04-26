<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->date('harvest_date')->nullable()->after('is_organic');
            $table->unsignedSmallInteger('shelf_life_days')->nullable()->after('harvest_date');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['harvest_date', 'shelf_life_days']);
        });
    }
};
