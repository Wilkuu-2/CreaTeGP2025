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
        Schema::create('kadaster', function (Blueprint $table) {
            $table->integer("ogc_fid")->primary()->autoIncrement();
            $table->string("gml_id");
            $table->float("areavalue")->nullable();
            $table->string("areavalue_uom",2)->nullable();
            $table->string("beginlifespanversion", 20)->nullable();
            $table->string("endlifespanversion")->nullable();
            $table->string("localid",16)->nullable();
            $table->string("namespace",8)->nullable();
            $table->integer("label")->nullable();
            $table->string("nationalcadastralreference", 35)->nullable();
            $table->string("validfrom",20)->nullable();
            $table->string("validto")->nullable();
            $table->magellanGeometry("geometry", 4258)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kadaster');
    }
};
