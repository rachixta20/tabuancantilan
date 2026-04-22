<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('account_status', ['pending', 'approved', 'rejected', 'suspended'])
                  ->default('approved')->after('is_active');
            $table->text('rejection_reason')->nullable()->after('account_status');
            $table->timestamp('verified_at')->nullable()->after('rejection_reason');
        });

        // Farmers without verification start as pending
        \Illuminate\Support\Facades\DB::statement(
            "UPDATE users SET account_status = 'approved' WHERE role != 'farmer'"
        );
        \Illuminate\Support\Facades\DB::statement(
            "UPDATE users SET account_status = 'approved', verified_at = updated_at WHERE role = 'farmer' AND is_verified = 1"
        );
        \Illuminate\Support\Facades\DB::statement(
            "UPDATE users SET account_status = 'pending' WHERE role = 'farmer' AND is_verified = 0"
        );
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['account_status', 'rejection_reason', 'verified_at']);
        });
    }
};
