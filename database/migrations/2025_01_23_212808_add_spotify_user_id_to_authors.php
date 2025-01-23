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
        Schema::table('authors', function (Blueprint $table) {
            $table->string('spotify_user_id', 255)->nullable()->after('links');
            $table->json('spotify_activity')->nullable()->after('spotify_user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('authors', callback: function (Blueprint $table) {
            $table->dropColumn('spotify_user_id');
            $table->dropColumn('spotify_activity');
        });
    }
};
