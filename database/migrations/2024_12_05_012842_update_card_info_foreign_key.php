<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCardInfoForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('card_info', function (Blueprint $table) {
            // Drop the existing foreign key and column
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');

            // Add the new column 'emp_num' and set it as a foreign key
            $table->string('emp_num')->after('id'); // Ensure 'emp_num' aligns with your data type in the users table
            $table->foreign('emp_num')->references('employee_number')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('card_info', function (Blueprint $table) {
            // Drop the new foreign key and column
            $table->dropForeign(['emp_num']);
            $table->dropColumn('emp_num');

            // Recreate the old 'user_id' column and foreign key
            $table->unsignedBigInteger('user_id')->after('id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
}
