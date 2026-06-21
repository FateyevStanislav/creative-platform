<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->text('content');
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();

            $table->foreign('post_id', 'comments_post_id_fk')->references('id')->on('posts');
            $table->foreign('user_id', 'comments_user_id_fk')->references('id')->on('users');
            $table->foreign('parent_id', 'comments_parent_id_fk')->references('id')->on('comments')->nullOnDelete();
            $table->index('post_id');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};