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
        Schema::create('scores', function (Blueprint $table) {
            $table->id();
            $table->integer('match_id');
            $table->string('score_line');
            $table->integer('run');
            $table->integer('ball');
            $table->integer('batsman_id');
            $table->integer('bowler_id');
            $table->integer('battingTeam_id');
            $table->integer('bowlingTeam_id');
            $table->integer('wicket');
            $table->string('extra');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scores');
    }
};
