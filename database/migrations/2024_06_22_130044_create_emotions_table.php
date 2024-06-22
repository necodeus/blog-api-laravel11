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
        Schema::create('emotions', function (Blueprint $table) {
            $table->id();
            $table->string('content_type'); // ie. POST, COMMENT
            $table->string('content_id'); // id of the content (post_id, comment_id)
            $table->string('emotion'); // ie. LIKE, LOVE, HAHA, WOW, SAD, ANGRY
            $table->string('ipv4_address', 15);
            $table->string('ua_hash', 32);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emotions');
    }
};
