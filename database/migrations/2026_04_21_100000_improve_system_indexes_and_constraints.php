<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fix: add unique constraint to carts to prevent duplicate entries
        Schema::table('carts', function (Blueprint $table) {
            $table->unique(['user_id', 'product_id']);
        });

        // Performance: add indexes on frequently queried columns
        Schema::table('products', function (Blueprint $table) {
            $table->index('status');
            $table->index('location');
            $table->index('is_featured');
            $table->index('is_organic');
            $table->index(['status', 'is_featured']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index('status');
            $table->index('payment_status');
            $table->index(['buyer_id', 'status']);
            $table->index(['seller_id', 'status']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index('role');
            $table->index('account_status');
            $table->index(['role', 'account_status']);
            $table->index(['role', 'is_active']);
        });

        // Soft deletes for products and users
        Schema::table('products', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->softDeletes();
        });

        // Admin audit log
        Schema::create('admin_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('users')->cascadeOnDelete();
            $table->string('action');
            $table->string('subject_type');
            $table->unsignedBigInteger('subject_id');
            $table->json('before')->nullable();
            $table->json('after')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
            $table->index(['subject_type', 'subject_id']);
        });

        // Order status history for timeline
        Schema::create('order_status_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('status');
            $table->string('notes')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index('order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_histories');
        Schema::dropIfExists('admin_audit_logs');

        Schema::table('users', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropIndex(['role', 'is_active']);
            $table->dropIndex(['role', 'account_status']);
            $table->dropIndex(['account_status']);
            $table->dropIndex(['role']);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['seller_id', 'status']);
            $table->dropIndex(['buyer_id', 'status']);
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['status']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropIndex(['status', 'is_featured']);
            $table->dropIndex(['is_organic']);
            $table->dropIndex(['is_featured']);
            $table->dropIndex(['location']);
            $table->dropIndex(['status']);
        });

        Schema::table('carts', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'product_id']);
        });
    }
};
