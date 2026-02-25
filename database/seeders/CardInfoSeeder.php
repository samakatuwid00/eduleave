<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CardInfoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();

        // Get existing user IDs
        $userIds = DB::table('users')->pluck('id')->toArray();

        // Generate sample records for CardInfo
        foreach (range(1, 3) as $index) {
            DB::table('card_info')->insert([
                'user_id' => $faker->randomElement($userIds), // Select a valid user ID
                'inclusive_period' => $faker->word(),
                'nature_of_activity' => $faker->sentence(),
                'no_of_days_credited' => $faker->randomFloat(2, 0, 30),
                'dso_no_vsr' => $faker->word(),
                'inclusive_dates' => $faker->word(),
                'no_days_leave' => $faker->randomFloat(2, 0, 30),
                'leave_without_pay' => $faker->randomFloat(2, 0, 15),
                'service_cred_bal' => $faker->randomFloat(2, 0, 100),
                'nature_of_leave' => $faker->word(),
                'dso_no_rol' => $faker->word(),
                'remarks' => $faker->sentence(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
