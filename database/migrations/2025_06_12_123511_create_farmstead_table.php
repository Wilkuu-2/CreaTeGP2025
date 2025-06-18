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
        Schema::create('farmstead', function (Blueprint $table) {
            $table->id();
            $table->string('name',255);
            $table->string('email',255);
            $table->string('phone_number',255);
            $table->boolean('show_email');
            $table->boolean('show_number');
            $table->magellanPoint('location');
            $table->timestamps();
        });

        // Schema::table('users', function (Blueprint $table) {
        //     $table->foreignIdFor(Farmstead::class)->nullable()->constrained();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('farmstead');
        // Schema::dropColumns('users', 'farmstead_id');
    }
};
