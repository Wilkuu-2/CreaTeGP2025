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
        Schema::table('criterion_fills', function (Blueprint $table) {
            $table->double('double1')->nullable();
            $table->double('double2')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('criterion_fills', function (Blueprint $table) {
            $table->dropColumn('double1');
            $table->dropColumn('double2');
        });
    }
};
