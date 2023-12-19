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
                    'phone_number' => '0982851486',
                ],
            )
        );
        \App\Models\User::create([
            "name" => "Hoàng Nguyên",
            "email" => "kiaisoft.nguyen.vo@gmail.com",
            "password" => '$2a$04$iof3u74fU5BgLoMT74xwW.bxSUrYspyl3Ot8QSepee39.NVCYRsSu',
            "department_id" => 1
        ]);
        DB::table('google_tokens')->insert(
            array(
                [
                    'id' => '1',
                    'access_token' => '{"access_token":"ya29.a0AfB_byDVbhK7nRgS7b4c67ODi2zGNghgOoDNmNnk4gJpkl2tidIm-N5F19u6B27aMIBlr99GHRAgBAAiZsQPTnYKiVIhLiIZ40T6U-3vYHh-2qjiy96BoLe1jIg0oSVOmybbtFRqf11vLGD-Jr1kvs7DdOCNJWSX97slaCgYKAScSARASFQHGX2MivWOUAjz9m-FndND4TRnusw0171","expires_in":3599,"scope":"https:\/\/www.googleapis.com\/auth\/drive.file https:\/\/www.googleapis.com\/auth\/drive","token_type":"Bearer","created":1700711930,"refresh_token":"1\/\/0ef-wA-0Z6iQMCgYIARAAGA4SNwF-L9IrqZXm3JNyTG85I92s4bCi7YcDxLldt51SnTZPIrwGtVLXvLRYmck4TCIchSfrdFMjnYM"}',
                    'refresh_token' => '1//0ef-wA-0Z6iQMCgYIARAAGA4SNwF-L9IrqZXm3JNyTG85I92s4bCi7YcDxLldt51SnTZPIrwGtVLXvLRYmck4TCIchSfrdFMjnYM',
                    'created_at' => '2023-11-23 03:57:46',
                    'updated_at' => '2023-11-23 03:58:50'
                ],
            )
        );
    }
}
