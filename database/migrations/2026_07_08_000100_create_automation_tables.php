<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('automation_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('version')->default(1);
            $table->boolean('automation_enabled')->default(true);
            $table->boolean('daily_digest_enabled')->default(true);
            $table->boolean('weekly_summary_enabled')->default(true);
            $table->boolean('employee_notifications_enabled')->default(false);
            $table->json('recipient_emails')->nullable();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('automation_runs', function (Blueprint $table) {
            $table->id();
            $table->string('rule_code', 60);
            $table->string('window_key', 40);
            $table->char('idempotency_key', 64)->unique();
            $table->string('status', 20)->default('running');
            $table->unsignedInteger('attempt')->default(1);
            $table->unsignedInteger('audience_count')->default(0);
            $table->unsignedInteger('item_count')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->string('error_summary', 500)->nullable();
            $table->timestamps();

            $table->index(['rule_code', 'created_at']);
            $table->index(['status', 'created_at']);
        });

        DB::table('automation_settings')->insert([
            'id' => 1,
            'version' => 1,
            'automation_enabled' => true,
            'daily_digest_enabled' => true,
            'weekly_summary_enabled' => true,
            'employee_notifications_enabled' => false,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('automation_runs');
        Schema::dropIfExists('automation_settings');
    }
};
