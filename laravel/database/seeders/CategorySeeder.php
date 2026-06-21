<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Музыка', 'slug' => 'music', 'description' => 'Треки, альбомы, плейлисты'],
            ['name' => 'Иллюстрации', 'slug' => 'illustrations', 'description' => 'Рисунки и цифровое искусство'],
            ['name' => 'Фотография', 'slug' => 'photography', 'description' => 'Фотоработы авторов'],
            ['name' => 'Текст', 'slug' => 'text', 'description' => 'Статьи, рассказы, стихи'],
            ['name' => 'Видео', 'slug' => 'video', 'description' => 'Видеоработы и анимация'],
        ];

        foreach ($categories as $cat) {
            DB::table('categories')->insert([
                'name' => $cat['name'],
                'slug' => $cat['slug'],
                'description' => $cat['description'],
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}