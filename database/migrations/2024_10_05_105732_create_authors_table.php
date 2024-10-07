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
        Schema::create('authors', function (Blueprint $table) {
            $table->bigInteger('id')->unique();
            $table->string('name')->index();
            $table->string('original_name')->nullable();
            $table->text('description')->nullable();
            $table->string('country')->nullable()->index();
            $table->string('initial', 1)->nullable()->index();
            $table->boolean('has_downloaded_cover')->default(false)->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authors');
    }
};
