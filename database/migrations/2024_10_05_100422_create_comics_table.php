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
        Schema::create('comics', function (Blueprint $table) {
            $table->bigInteger('id')->unique();
            $table->string('name');
            $table->string('original_name')->nullable();
            $table->string('aliases')->nullable();
            $table->text('description')->nullable();
            $table->string('country')->nullable()->index();
            $table->string('audience')->nullable()->index();
            $table->unsignedSmallInteger('year')->nullable()->index();
            $table->string('initial', 3)->index();
            $table->boolean('has_downloaded_cover')->default(false)->index();
            $table->boolean('is_finished')->default(false)->index();
            $table->boolean('is_outdated')->default(false)->index();
            $table->date('last_updated_on')->nullable()->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comics');
    }
};
