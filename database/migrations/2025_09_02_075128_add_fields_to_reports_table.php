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
        Schema::table('reports', function (Blueprint $table) {
            // Public-facing reference ID (e.g., RPR-2025-000123)
            $table->string('report_id')->unique()->after('id');

            // Which account submitted the report
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete()->after('report_id');

            // Person being reported for (not necessarily the account owner)
            $table->string('submitter_name')->after('user_id');
            $table->unsignedTinyInteger('age')->after('submitter_name');
            $table->string('gender', 20)->after('age');
            $table->text('address')->after('gender');

            // Report details
            $table->enum('type', [
                'infrastructure',
                'sanitation',
                'community_welfare',
                'behavoural_concerns',
            ])->after('address');
            $table->json('photos')->nullable()->after('type');
            $table->text('description')->after('photos');

            // Workflow state
            $table->enum('progress', [
                'pending',
                'in_review',
                'assigned',
                'resolved',
                'rejected',
            ])->default('pending')->after('description');

            // For explicit display; you can also rely on created_at
            $table->timestamp('date_generated')->useCurrent()->after('progress');

            // Helpful indexes for filtering
            $table->index('type');
            $table->index('progress');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropColumn([
                'report_id',
                'user_id',
                'submitter_name',
                'age',
                'gender',
                'address',
                'type',
                'photos',
                'description',
                'progress',
                'date_generated',
            ]);

            $table->dropIndex(['type']);
            $table->dropIndex(['progress']);
            $table->dropIndex(['user_id']);
        });
    }
};
