<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('latitude', 10, 7)->nullable()->after('location');
            $table->decimal('longitude', 11, 7)->nullable()->after('latitude');
            $table->boolean('is_live')->default(false)->after('longitude');
            $table->string('live_title')->nullable()->after('is_live');
        });

        // Default coordinates around Cantilan for existing farmers (MySQL only — uses RAND())
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("
                UPDATE users
                SET
                    latitude  = 9.3139  + (RAND() - 0.5) * 0.04,
                    longitude = 125.9897 + (RAND() - 0.5) * 0.04
                WHERE role = 'farmer'
            ");
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude', 'is_live', 'live_title']);
        });
    }
};
