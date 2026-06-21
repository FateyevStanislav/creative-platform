<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $alice = DB::table('users')->where('email', 'alice@creative.local')->value('id');
        $bob = DB::table('users')->where('email', 'bob@creative.local')->value('id');
        $music = DB::table('categories')->where('slug', 'music')->value('id');
        $text = DB::table('categories')->where('slug', 'text')->value('id');
        $photo = DB::table('categories')->where('slug', 'photography')->value('id');

        DB::table('posts')->insert([
            [
                'user_id' => $alice,
                'category_id' => $music,
                'title' => 'Мой первый трек',
                'content' => 'Наконец-то записал свой первый трек.',
                'content_type' => 'text',
                'status' => 'published',
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $alice,
                'category_id' => $text,
                'title' => 'О творчестве',
                'content' => 'Творчество - это способ выразить то, что словами не передать.',
                'content_type' => 'text',
                'status' => 'published',
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'user_id' => $bob,
                'category_id' => $photo,
                'title' => 'Закат над городом',
                'content' => 'Снял сегодня утром. Свет был просто невероятный.',
                'content_type' => 'text',
                'status' => 'published',
                'published_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}