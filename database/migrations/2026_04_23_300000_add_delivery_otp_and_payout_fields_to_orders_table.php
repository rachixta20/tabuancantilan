<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('delivery_otp', 6)->nullable()->after('notes');
            $table->timestamp('delivery_otp_expires_at')->nullable()->after('delivery_otp');
            $table->enum('payout_status', ['held', 'released', 'disputed'])->default('held')->after('delivery_otp_expires_at');
            $table->timestamp('payout_due_at')->nullable()->after('payout_status');
            $table->timestamp('payout_released_at')->nullable()->after('payout_due_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['delivery_otp', 'delivery_otp_expires_at', 'payout_status', 'payout_due_at', 'payout_released_at']);
        });
    }
};
