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
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image')->nullable();
            $table->integer('team_id');
            $table->enum('role', ['Batting AllRounder', 'Bowling AllRounder', 'WK Batsman', 'Batsman', 'Bowler']);
            $table->enum('batting_style', ['Right handed', 'Left handed']);
            $table->enum('bowling_style', ['Right arm pace', 'Left arm pace', 'Left arm spin','Right arm spin', 'N/A']);
            $table->dateTime('born');
            $table->text('biography');
            $table->integer('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};
