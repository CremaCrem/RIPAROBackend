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
        Schema::table('citizen_feedback', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('subject', 150)->nullable();
            $table->boolean('anonymous')->default(false);
            $table->string('contact_email')->nullable();
            $table->text('message');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('citizen_feedback', function (Blueprint $table) {
            $table->dropColumn([
                'user_id',
                'subject',
                'anonymous',
                'contact_email',
                'message',
            ]);
        });
    }
};
