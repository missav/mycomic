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
            $table->unsignedInteger('views_1d')->default(0)->index()->after('views');
            $table->unsignedInteger('views_7d')->default(0)->index()->after('views_1d');
            $table->unsignedInteger('views_30d')->default(0)->index()->after('views_7d');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comics', function (Blueprint $table) {
            //
        });
    }
};
