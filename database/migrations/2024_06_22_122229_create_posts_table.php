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
            $table->string('title', 512)->nullable();
            $table->string('teaser', 1024)->nullable();
            $table->char('main_image_id', 36)->nullable();
            $table->text('content')->nullable();
            $table->char('editor_account_id', 36)->nullable();
            $table->dateTime('created_at')->nullable();
            $table->dateTime('modified_at')->nullable();
            $table->char('publisher_account_id', 36)->nullable();
            $table->dateTime('published_at')->nullable();
            $table->char('category_id', 36)->nullable();
            $table->string('tags', 256)->nullable();
            $table->boolean('is_commentable')->default(1);
            $table->float('rating_average')->default(0);
            $table->integer('rating_count')->default(0);
            $table->integer('comments_count')->default(0);
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
