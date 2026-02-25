<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCardInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('card_info', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('user_id'); // Foreign key to users table
            $table->date('inclusive_period')->nullable();
            $table->string('nature_of_activity')->nullable();
            $table->decimal('no_of_days_credited')->nullable();
            $table->string('dso_no_vsr')->nullable();
            $table->date('inclusive_dates')->nullable();
            $table->decimal('no_days_leave')->nullable();
            $table->integer('leave_without_pay')->nullable();
            $table->integer('service_cred_bal')->nullable();
            $table->integer('nature_of_leave')->nullable();
            $table->string('dso_no_rol')->nullable();
            $table->string('remarks')->nullable();
            $table->timestamps(); // Created at & Updated at

            // Add a foreign key constraint if user_id relates to the users table
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('card_info');
    }
}
