<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('import_batches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('admin_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedBigInteger('employee_profile_id')->nullable();
            $table->string('card_type', 30);
            $table->string('parser_version', 20)->default('1');
            $table->string('original_name');
            $table->char('file_hash', 64);
            $table->string('stored_path')->nullable();
            $table->string('status', 20)->default('validated');
            $table->unsignedInteger('row_count')->default(0);
            $table->unsignedInteger('error_count')->default(0);
            $table->json('preview_data')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('committed_at')->nullable();
            $table->timestamp('rolled_back_at')->nullable();
            $table->string('rollback_reason', 500)->nullable();
            $table->timestamps();

            $table->foreign('employee_profile_id')
                ->references('user_id')
                ->on('employee_profiles')
                ->nullOnDelete();
            $table->index(['status', 'created_at']);
        });

        Schema::table('teaching_leave_cards', function (Blueprint $table) {
            $table->foreignUuid('import_batch_id')->nullable()->constrained('import_batches')->nullOnDelete();
            $table->unsignedInteger('source_row_number')->nullable();
            $table->index(['import_batch_id', 'source_row_number']);
        });

        Schema::table('non_teaching_leave_cards', function (Blueprint $table) {
            $table->foreignUuid('import_batch_id')->nullable()->constrained('import_batches')->nullOnDelete();
            $table->unsignedInteger('source_row_number')->nullable();
            $table->index(['import_batch_id', 'source_row_number']);
        });
    }

    public function down(): void
    {
        Schema::table('non_teaching_leave_cards', function (Blueprint $table) {
            $table->dropIndex(['import_batch_id', 'source_row_number']);
            $table->dropConstrainedForeignId('import_batch_id');
            $table->dropColumn('source_row_number');
        });

        Schema::table('teaching_leave_cards', function (Blueprint $table) {
            $table->dropIndex(['import_batch_id', 'source_row_number']);
            $table->dropConstrainedForeignId('import_batch_id');
            $table->dropColumn('source_row_number');
        });

        Schema::dropIfExists('import_batches');
    }
};
