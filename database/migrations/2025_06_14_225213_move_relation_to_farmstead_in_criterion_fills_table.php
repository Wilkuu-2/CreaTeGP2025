<?php

use App\Models\Farmstead;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('criterion_fills', function (Blueprint $table) {
           $table->dropPrimary();
           $table->dropForeignIdFor(User::class, 'user_id');
           $table->foreignIdFor(Farmstead::class, 'farmstead_id')->constrained()->onDelete('cascade');
           $table->primary(array('farmstead_id', 'criterion_id'));
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('criterion_fills', function (Blueprint $table) {
           $table->dropPrimary();
           $table->dropForeignIdFor(Farmstead::class, 'farmstead_id');
           $table->foreignIdFor(User::class, 'user_id')->constrained()->onDelete('cascade');
           $table->primary(array('user_id', 'criterion_id'));
        });
    }
};
