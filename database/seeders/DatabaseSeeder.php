<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Seeding the Boss
        if (env("ADMIN_USER_EMAIL") != null && env("ADMIN_USER_PASS") != null) {
            DB::table('users')->insert([
                'first_name' => "Администратор",
                'last_name' => "Системный",
                'email' => env("ADMIN_USER_EMAIL"),
                'password' => Hash::make(env("ADMIN_USER_PASS")),
            ]);
        }

        DB::table('globals')->insert([
            [
                'key' => 'import_news',
                'value' => false,
            ],
            [
                'key' => 'import_page',
                'value' => false,
            ],
        ]);
    }
}
