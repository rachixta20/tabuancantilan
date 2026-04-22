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
            $table->string('id_document')->nullable()->after('verified_at');
            $table->string('selfie_photo')->nullable()->after('id_document');
            $table->string('farm_document')->nullable()->after('selfie_photo');
            $table->string('id_type')->nullable()->after('farm_document');
            $table->string('farm_name')->nullable()->after('id_type');
            $table->text('admin_notes')->nullable()->after('farm_name');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['id_document', 'selfie_photo', 'farm_document', 'id_type', 'farm_name', 'admin_notes']);
        });
    }
};
