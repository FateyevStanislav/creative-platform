<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("ALTER TABLE posts MODIFY COLUMN content_type ENUM('text', 'image', 'audio', 'video', 'mixed') DEFAULT 'text'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE posts MODIFY COLUMN content_type ENUM('text', 'image', 'audio', 'mixed') DEFAULT 'text'");
    }
};