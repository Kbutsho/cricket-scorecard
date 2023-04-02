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
        Schema::create('cricket_matches', function (Blueprint $table) {
            $table->id();
            $table->string('team_a');
            $table->string('team_b');
            $table->string('venue');
            $table->string('format');
            $table->dateTime('time');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cricket_matches');
    }
};
