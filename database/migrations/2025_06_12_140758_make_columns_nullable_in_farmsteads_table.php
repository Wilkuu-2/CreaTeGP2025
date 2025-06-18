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
        Schema::table('farmsteads', function (Blueprint $table) {
            //
            $table->string('phone_number',255)->nullable()->change();
            $table->magellanPoint('location')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('farmsteads', function (Blueprint $table) {
            $table->string('phone_number',255)->change();
            $table->magellanPoint('location')->change();
        });
    }
};
