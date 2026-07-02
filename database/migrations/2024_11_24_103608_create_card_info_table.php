<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('personnel_types', function (Blueprint $table) {
            $table->unsignedTinyInteger('id')->primary();
            $table->string('code', 30)->unique();
            $table->string('name', 50)->unique();
        });

        Schema::create('employee_profiles', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained()->cascadeOnDelete();
            $table->string('employee_number')->unique();
            $table->unsignedTinyInteger('personnel_type_id');
            $table->string('position')->nullable();
            $table->date('date_employed')->nullable();
            $table->string('sex')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->string('station')->nullable();
            $table->string('civil_status')->nullable();

            $table->foreign('personnel_type_id')->references('id')->on('personnel_types');
        });

        Schema::create('teaching_leave_cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_profile_id');
            $table->string('inclusive_period')->nullable();
            $table->string('nature_of_activity')->nullable();
            $table->decimal('days_credited', 8, 2)->nullable();
            $table->string('vacation_service_dso_number')->nullable();
            $table->string('inclusive_leave_dates')->nullable();
            $table->decimal('days_with_pay', 8, 2)->nullable();
            $table->decimal('service_credit_balance', 8, 2)->nullable();
            $table->decimal('days_without_pay', 8, 2)->nullable();
            $table->string('nature_of_leave')->nullable();
            $table->string('record_of_leave_dso_number')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps();

            $table->foreign('employee_profile_id')
                ->references('user_id')
                ->on('employee_profiles')
                ->cascadeOnDelete();
        });

        Schema::create('non_teaching_leave_cards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_profile_id');
            $table->string('period')->nullable();
            $table->string('particulars')->nullable();
            $table->decimal('vacation_leave_earned', 8, 2)->nullable();
            $table->string('vacation_leave_with_pay')->nullable();
            $table->string('vacation_leave_balance')->nullable();
            $table->decimal('vacation_leave_without_pay', 8, 2)->nullable();
            $table->decimal('sick_leave_earned', 8, 2)->nullable();
            $table->decimal('sick_leave_with_pay', 8, 2)->nullable();
            $table->string('sick_leave_balance')->nullable();
            $table->string('sick_leave_without_pay')->nullable();
            $table->string('leave_application_action')->nullable();
            $table->timestamps();

            $table->foreign('employee_profile_id')
                ->references('user_id')
                ->on('employee_profiles')
                ->cascadeOnDelete();
        });

        DB::table('personnel_types')->insert([
            ['id' => 1, 'code' => 'teaching', 'name' => 'Teaching'],
            ['id' => 2, 'code' => 'non_teaching', 'name' => 'Non-Teaching'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('non_teaching_leave_cards');
        Schema::dropIfExists('teaching_leave_cards');
        Schema::dropIfExists('employee_profiles');
        Schema::dropIfExists('personnel_types');
    }
};
