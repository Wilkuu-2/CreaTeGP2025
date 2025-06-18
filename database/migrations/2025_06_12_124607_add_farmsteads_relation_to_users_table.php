<?php

use App\Models\Farmstead;
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
        Schema::rename('farmstead', 'farmsteads');
        Schema::table('users', function (Blueprint $table) {
             $table->foreignIdFor(Farmstead::class)->nullable()->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::rename('farmsteads', 'farmstead');
         Schema::dropColumns('users', 'farmstead_id');
    }
};
