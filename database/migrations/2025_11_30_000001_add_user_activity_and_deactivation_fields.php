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
            $table->timestamp('last_active_at')->nullable()->after('remember_token');
            $table->boolean('is_active')->default(true)->after('last_active_at');
            $table->timestamp('deactivated_at')->nullable()->after('is_active');
            $table->unsignedBigInteger('deactivated_by')->nullable()->after('deactivated_at');
            
            $table->foreign('deactivated_by')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['deactivated_by']);
            $table->dropColumn(['last_active_at', 'is_active', 'deactivated_at', 'deactivated_by']);
        });
    }
};

