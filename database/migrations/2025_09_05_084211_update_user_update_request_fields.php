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
        Schema::table('user_update_request', function (Blueprint $table) {
            // Requesting user
            $table->foreignId('user_id')->after('id')->constrained('users')->onDelete('cascade');

            // Optional new values (only provided fields will be set by the client)
            $table->string('first_name', 100)->nullable()->after('user_id');
            $table->string('middle_name', 100)->nullable()->after('first_name');
            $table->string('last_name', 100)->nullable()->after('middle_name');
            $table->string('email', 255)->nullable()->after('last_name');
            $table->string('mobile_number', 20)->nullable()->after('email');
            $table->string('barangay', 100)->nullable()->after('mobile_number');
            $table->string('zone', 50)->nullable()->after('barangay');
            $table->string('password', 255)->nullable()->after('zone');

            // New valid ID image associated with the requested change
            $table->string('id_document_path', 2048)->nullable()->after('password');

            // Review workflow
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->after('id_document_path');
            $table->foreignId('reviewed_by')->nullable()->after('status')->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
            $table->text('admin_notes')->nullable()->after('reviewed_at');

            // Staff queue index
            $table->index(['user_id', 'status'], 'user_update_request_user_id_status_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_update_request', function (Blueprint $table) {
            $table->dropIndex('user_update_request_user_id_status_index');
            $table->dropConstrainedForeignId('user_id');
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropColumn([
                'first_name',
                'middle_name',
                'last_name',
                'email',
                'mobile_number',
                'barangay',
                'zone',
                'password',
                'id_document_path',
                'status',
                'reviewed_by',
                'reviewed_at',
                'admin_notes',
            ]);
        });
    }
};
