<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('type', ['like', 'dislike']);
            $table->timestamps();

            $table->foreign('post_id', 'reactions_post_id_fk')->references('id')->on('posts');
            $table->foreign('user_id', 'reactions_user_id_fk')->references('id')->on('users');
            $table->index('post_id');
            $table->index('user_id');
            $table->unique(['user_id', 'post_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reactions');
    }
};