<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesTableSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            ['name' => 'Администратор', 'slug' => 'admin'],
            ['name' => 'Руководитель',   'slug' => 'director'],
            ['name' => 'Менеджер',       'slug' => 'manager'],
            ['name' => 'Гость',          'slug' => 'guest'],
        ]);
    }
}
