<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('user_id');
            $table->string('post_files');
            $table->string('post_caption');
            $table->string('post_tags')->nullable();
            $table->string('post_location')->nullable();
            $table->string('post_audience');
            $table->string('post_likes')->default(0);
            $table->string('post_comments')->default(0);
            $table->enum('comment_status', ['open', 'closed'])->default('open');
            $table->enum('like_count', ['visible', 'notVisible'])->default('visible');

            $table->foreign('user_id')->references('id')->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
