<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Сначала создаём роли
        $this->call([
            RolesTableSeeder::class,
        ]);

        // Затем — администратора
        User::create([
            'login' => 'admin',
            'email' => 'admin@example.com',
            'name' => 'Системный Администратор',
            'internal_phone' => '100',
            'external_phone' => '8-800-555-35-35',
            'birth_date' => '1990-01-01',
            'horoscope' => 'Козерог',
            'role_id' => 1, // ID роли "admin", см. сидер
            'password' => Hash::make('admin'), // Пароль по умолчанию
        ]);
    }
}
