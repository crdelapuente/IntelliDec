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
        Schema::create('prefs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rel_users_id')->constrained();
            $table->string('ci');
            $table->string('cj');
            $table->integer('pref');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prefs');
    }
};
