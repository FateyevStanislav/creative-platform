<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = DB::table('roles')->where('name', 'admin')->value('id');
        $publisherRole = DB::table('roles')->where('name', 'publisher')->value('id');
        $readerRole = DB::table('roles')->where('name', 'reader')->value('id');

        DB::table('users')->insert([
            [
                'name' => 'Admin',
                'email' => 'admin@creative.local',
                'password' => Hash::make('password'),
                'role_id' => $adminRole,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Alice',
                'email' => 'alice@creative.local',
                'password' => Hash::make('password'),
                'role_id' => $publisherRole,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bob',
                'email' => 'bob@creative.local',
                'password' => Hash::make('password'),
                'role_id' => $publisherRole,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Reader',
                'email' => 'reader@creative.local',
                'password' => Hash::make('password'),
                'role_id' => $readerRole,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}