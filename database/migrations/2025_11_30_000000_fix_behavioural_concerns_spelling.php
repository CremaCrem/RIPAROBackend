<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Fixes the spelling of 'behavoural_concerns' to 'behavioural_concerns'
     */
    public function up(): void
    {
        // Step 1: Modify enum to include both old and new values
        DB::statement("ALTER TABLE reports MODIFY COLUMN type ENUM('infrastructure', 'sanitation', 'community_welfare', 'behavoural_concerns', 'behavioural_concerns')");

        // Step 2: Update existing rows with the old misspelled value
        DB::table('reports')
            ->where('type', 'behavoural_concerns')
            ->update(['type' => 'behavioural_concerns']);

        // Step 3: Remove the old misspelled value from enum
        DB::statement("ALTER TABLE reports MODIFY COLUMN type ENUM('infrastructure', 'sanitation', 'community_welfare', 'behavioural_concerns')");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add the old misspelled value back
        DB::statement("ALTER TABLE reports MODIFY COLUMN type ENUM('infrastructure', 'sanitation', 'community_welfare', 'behavoural_concerns', 'behavioural_concerns')");

        // Update rows back to old spelling
        DB::table('reports')
            ->where('type', 'behavioural_concerns')
            ->update(['type' => 'behavoural_concerns']);

        // Remove the correct spelling
        DB::statement("ALTER TABLE reports MODIFY COLUMN type ENUM('infrastructure', 'sanitation', 'community_welfare', 'behavoural_concerns')");
    }
};

