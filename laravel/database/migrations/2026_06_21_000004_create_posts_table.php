<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('category_id');
            $table->string('title')->nullable();
            $table->text('content')->nullable();
            $table->enum('content_type', ['text', 'image', 'audio', 'mixed'])->default('text');
            $table->string('media_path')->nullable();
            $table->text('excerpt')->nullable();
            $table->enum('status', ['draft', 'published', 'deleted'])->default('published');
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id', 'posts_user_id_fk')->references('id')->on('users');
            $table->foreign('category_id', 'posts_category_id_fk')->references('id')->on('categories');
            $table->index('user_id');
            $table->index('category_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};