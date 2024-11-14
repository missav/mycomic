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
        Schema::table('comics', function (Blueprint $table) {
            $table->unsignedBigInteger('recent_chapter_id')->nullable()->after('initial');
            $table->string('recent_chapter_title')->nullable()->after('recent_chapter_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comics', function (Blueprint $table) {
            $table->dropColumn('recent_chapter_id');
            $table->dropColumn('recent_chapter_title');
        });
    }
};
