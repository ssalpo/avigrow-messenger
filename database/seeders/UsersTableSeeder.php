<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Санжар',
                'email' => 'ssalpo@ya.ru',
                'password' => bcrypt('s1994'),
            ],
            [
                'name' => 'Шахбоз',
                'email' => 'sh@ya.ru',
                'password' => bcrypt('sh1994'),
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
