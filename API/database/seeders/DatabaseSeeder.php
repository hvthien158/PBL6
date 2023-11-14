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
                    'name' => 'AM',
                    'amount' => 0.5,
                    'time_valid_check_in' => '08:30:00',
                    'time_valid_check_out' => '13:00:00',
                ],
                [
                    'name' => 'PM',
                    'amount' => 0.5,
                    'time_valid_check_in' => '13:00:00',
                    'time_valid_check_out' => '17:45:00',
                ],
                [
                    'name' => 'Full',
                    'amount' => 1,
                    'time_valid_check_in' => '00:00:01',
                    'time_valid_check_out' => '23:59:59',
                ]
            )
        );
        DB::table('departments')->insert(
            array(
                [
                    'department_name' => 'Đà Nẵng',
                    'address' => '363 Nguyễn Hữu Thọ, Khuê Trung, Cẩm Lệ, Đà Nẵng',
                    'email' => 'contact.dn@kiaisoft.com',
                    'phone_number' => ' (+84)982-851-486',
                ],
            )
        );
    }
}
