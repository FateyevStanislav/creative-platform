<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subscriber_id');
            $table->unsignedBigInteger('publisher_id');
            $table->timestamps();

            $table->foreign('subscriber_id', 'subs_subscriber_id_fk')->references('id')->on('users');
            $table->foreign('publisher_id', 'subs_publisher_id_fk')->references('id')->on('users');
            $table->index('subscriber_id');
            $table->index('publisher_id');
            $table->unique(['subscriber_id', 'publisher_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};