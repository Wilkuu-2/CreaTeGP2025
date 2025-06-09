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
        Schema::table('milestones', function (Blueprint $table) {
            $table->integer('order');
            $table->string('name', 255);
            $table->boolean('do_map');
            $table->boolean('is_any');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('milestones', function (Blueprint $table) {
            $table->dropColumn('order');
            $table->dropColumn('name');
            $table->dropColumn('do_map');
            $table->dropColumn('is_any');
        });
    }
};
