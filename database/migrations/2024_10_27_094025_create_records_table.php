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
        Schema::create('records', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id')->index();
            $table->unsignedBigInteger('comic_id')->index();
            $table->unsignedBigInteger('chapter_id')->nullable();
            $table->boolean('has_bookmarked')->default(false)->index();
            $table->timestamps();

            $table->index(['user_id', 'comic_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('records');
    }
};
