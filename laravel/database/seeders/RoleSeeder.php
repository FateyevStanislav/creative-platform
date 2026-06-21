<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'name' => 'reader',
                'display_name' => 'Читатель',
                'description' => 'Может просматривать посты, комментировать, ставить реакции и подписываться.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'publisher',
                'display_name' => 'Публикатор',
                'description' => 'Может публиковать посты и всё что может читатель.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'admin',
                'display_name' => 'Администратор',
                'description' => 'Полный доступ: модерация, блокировка пользователей, удаление постов.',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}