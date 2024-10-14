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
        Schema::create('missing_pages', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('chapter_id')->index();
            $table->unsignedInteger('page');

            $table->unique(['chapter_id', 'page']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('missing_pages');
    }
};
