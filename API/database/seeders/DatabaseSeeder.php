<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        DB::table('shifts')->insert(
            array(
                [
                    'name' => 'Ca sáng',
                    'amount' => 0.5,
                    'time_valid_check_in' => '08:30:00',
                    'time_valid_check_out' => '11:45:00',
                ],
                [
                    'name' => 'Ca chiều',
                    'amount' => 0.5,
                    'time_valid_check_in' => '13:00:00',
                    'time_valid_check_out' => '17:45:00',
                ],
                [
                    'name' => 'Full',
                    'amount' => 1,
                    'time_valid_check_in' => '08:30:00',
                    'time_valid_check_out' => '17:45:00',
                ]
            )
        );
    }
}
