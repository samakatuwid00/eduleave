<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('admin_role', 30)->nullable()->after('usertype')->index();
        });

        DB::table('users')->where('usertype', 'admin')->update(['admin_role' => 'super_admin']);

        Schema::create('audit_events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('actor_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('actor_label')->nullable();
            $table->string('action', 80)->index();
            $table->string('target_type', 100)->index();
            $table->string('target_id', 100)->nullable();
            $table->string('target_label')->nullable();
            $table->string('employee_number')->nullable()->index();
            $table->string('correlation_id', 64)->index();
            $table->string('source', 30)->default('web');
            $table->string('reason', 500)->nullable();
            $table->json('previous_values')->nullable();
            $table->json('new_values')->nullable();
            $table->json('metadata')->nullable();
            $table->boolean('is_held')->default(false)->index();
            $table->timestamp('created_at')->useCurrent()->index();

            $table->index(['target_type', 'target_id']);
            $table->index(['actor_user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_events');

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['admin_role']);
            $table->dropColumn('admin_role');
        });
    }
};
