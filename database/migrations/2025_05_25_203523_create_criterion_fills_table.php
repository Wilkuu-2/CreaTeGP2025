<?php

use App\Models\Criterion;
use App\Models\User;
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
        Schema::create('criterion_fills', function (Blueprint $table) {
            $table->foreignIdFor(User::class, 'user_id')->constrained()->onDelete('cascade');
            $table->foreignIdFor(Criterion::class, 'criterion_id' )->constrained()->onDelete('cascade');
            $table->primary(array('user_id', 'criterion_id'));
            $table->boolean('bool1')->nullable();
            $table->boolean('bool2')->nullable();
            $table->bigInteger('int1')->nullable();
            $table->bigInteger('int2')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('criterion_fills');
    }
};
