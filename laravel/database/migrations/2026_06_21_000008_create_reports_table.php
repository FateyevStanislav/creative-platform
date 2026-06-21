<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('target_type', ['post', 'comment', 'user']);
            $table->unsignedBigInteger('target_id');
            $table->enum('reason', ['spam', 'abuse', 'plagiarism', 'other']);
            $table->text('message')->nullable();
            $table->enum('status', ['pending', 'reviewed', 'rejected', 'accepted'])->default('pending');
            $table->unsignedBigInteger('reviewed_by')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id', 'reports_user_id_fk')->references('id')->on('users');
            $table->foreign('reviewed_by', 'reports_reviewed_by_fk')->references('id')->on('users')->nullOnDelete();
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reports');
    }
};