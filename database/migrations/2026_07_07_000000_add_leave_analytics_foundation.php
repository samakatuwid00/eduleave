<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leave_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 100)->unique();
            $table->unsignedTinyInteger('personnel_type_id')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('personnel_type_id')
                ->references('id')
                ->on('personnel_types')
                ->nullOnDelete();
        });

        Schema::table('teaching_leave_cards', function (Blueprint $table) {
            $table->date('period_start')->nullable()->after('inclusive_period');
            $table->date('period_end')->nullable()->after('period_start');
            $table->foreignId('leave_type_id')->nullable()->after('nature_of_leave')
                ->constrained('leave_types')->nullOnDelete();
            $table->string('parse_state', 20)->default('unparseable');
            $table->string('parse_note')->nullable();

            $table->index(['employee_profile_id', 'period_start']);
            $table->index(['leave_type_id', 'period_start']);
        });

        Schema::table('non_teaching_leave_cards', function (Blueprint $table) {
            $table->date('period_start')->nullable()->after('period');
            $table->date('period_end')->nullable()->after('period_start');
            $table->foreignId('leave_type_id')->nullable()->after('particulars')
                ->constrained('leave_types')->nullOnDelete();
            $table->decimal('vacation_leave_with_pay_value', 8, 2)->nullable();
            $table->decimal('vacation_leave_balance_value', 8, 2)->nullable();
            $table->decimal('sick_leave_balance_value', 8, 2)->nullable();
            $table->decimal('sick_leave_without_pay_value', 8, 2)->nullable();
            $table->string('application_action_code', 20)->nullable();
            $table->string('parse_state', 20)->default('unparseable');
            $table->string('parse_note')->nullable();

            $table->index(['employee_profile_id', 'period_start']);
            $table->index(['leave_type_id', 'period_start']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('processed_by')->nullable()->after('status')
                ->constrained('users')->nullOnDelete();
            $table->timestamp('processed_at')->nullable()->after('processed_by');
            $table->string('decision_reason', 500)->nullable()->after('processed_at');

            $table->index(['status', 'email_verified_at']);
        });

        DB::table('leave_types')->insert([
            ['code' => 'vacation', 'name' => 'Vacation', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'sick', 'name' => 'Sick', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'other', 'name' => 'Other', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['status', 'email_verified_at']);
            $table->dropConstrainedForeignId('processed_by');
            $table->dropColumn(['processed_at', 'decision_reason']);
        });

        Schema::table('non_teaching_leave_cards', function (Blueprint $table) {
            $table->dropIndex(['employee_profile_id', 'period_start']);
            $table->dropIndex(['leave_type_id', 'period_start']);
            $table->dropConstrainedForeignId('leave_type_id');
            $table->dropColumn([
                'period_start',
                'period_end',
                'vacation_leave_with_pay_value',
                'vacation_leave_balance_value',
                'sick_leave_balance_value',
                'sick_leave_without_pay_value',
                'application_action_code',
                'parse_state',
                'parse_note',
            ]);
        });

        Schema::table('teaching_leave_cards', function (Blueprint $table) {
            $table->dropIndex(['employee_profile_id', 'period_start']);
            $table->dropIndex(['leave_type_id', 'period_start']);
            $table->dropConstrainedForeignId('leave_type_id');
            $table->dropColumn(['period_start', 'period_end', 'parse_state', 'parse_note']);
        });

        Schema::dropIfExists('leave_types');
    }
};
