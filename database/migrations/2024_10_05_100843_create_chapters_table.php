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
        Schema::create('chapters', function (Blueprint $table) {
            $table->bigInteger('id')->unique();
            $table->foreignId('comic_id')->index();
            $table->string('type');
            $table->unsignedInteger('number');
            $table->string('title');
            $table->unsignedSmallInteger('pages');
            $table->boolean('has_downloaded_pages')->default(false)->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chapters');
    }
};
