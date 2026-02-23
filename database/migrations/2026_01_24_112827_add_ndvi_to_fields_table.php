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
    Schema::table('fields', function (Blueprint $table) {
        $table->json('ndvi_zones')->nullable();
        $table->float('ndvi_avg')->nullable();
    });
}

public function down(): void
{
    Schema::table('fields', function (Blueprint $table) {
        $table->dropColumn(['ndvi_zones', 'ndvi_avg']);
    });
}
};
