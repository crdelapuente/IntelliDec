<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('rel_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained();
            $table->bigInteger('fo_id')->constrained();
            $table->double('consenso');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rel_projects');
    }
};
