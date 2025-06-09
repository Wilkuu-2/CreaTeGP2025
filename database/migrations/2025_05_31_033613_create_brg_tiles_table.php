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
        Schema::create('brg_tiles', function (Blueprint $table) {
            $table->integer('id')->primary()->autoIncrement();
            $table->string('category',100);
            $table->string('gewas',120);
            $table->integer('gewascode');
            $table->magellanGeometry('geom',4326);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brg_tiles');
    }
};
