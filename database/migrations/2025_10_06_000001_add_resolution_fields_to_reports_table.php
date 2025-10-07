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
            // After images uploaded by staff/admin as resolution evidence
            $table->json('resolution_photos')->nullable()->after('photos');

            // Who resolved and when
            $table->foreignId('resolved_by')->nullable()->after('progress')
                ->constrained('users')->nullOnDelete();
            $table->timestamp('resolved_at')->nullable()->after('resolved_by');

            // Optional notes/caption for the resolution
            $table->text('resolution_notes')->nullable()->after('resolved_at');

            $table->index('resolved_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reports', function (Blueprint $table) {
            $table->dropIndex(['resolved_by']);
            $table->dropConstrainedForeignId('resolved_by');
            $table->dropColumn(['resolution_photos', 'resolved_at', 'resolution_notes']);
        });
    }
};


