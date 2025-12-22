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
        // Drop table if it exists (cleanup from previous failed attempt)
        Schema::dropIfExists('support_tickets');
        
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('subject');
            $table->longText('description');
            $table->enum('category', [
                'business_data',
                'email_change',
                'account_access',
                'technical_issue',
                'other'
            ])->default('other');
            $table->enum('status', [
                'open',
                'in_progress',
                'pending_user_action',
                'resolved',
                'closed'
            ])->default('open')->index();
            $table->enum('priority', [
                'low',
                'medium',
                'high',
                'urgent'
            ])->default('medium');
            $table->foreignId('assigned_to')->nullable()->constrained('admins')->onDelete('set null');
            $table->timestamp('resolved_at')->nullable();
            $table->longText('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
