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
        Schema::create('spotify_users', function (Blueprint $table) {
            $table->id();
            $table->string('user_id', 255)->unique();
            $table->string('name', 255);
            $table->string('access_token', 255);
            $table->string('refresh_token', 255);
            $table->string('scope', 255);
            $table->integer('followers');
            $table->string('avatar_url', 512);
            $table->string('type', 255);
            $table->timestamp('token_refreshed_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spotify_users');
    }
};
