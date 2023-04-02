<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('captains', function (Blueprint $table) {
            $table->id();
            $table->integer('player_id');
            $table->integer('team_id');
            $table->enum('format', ['TEST', 'ODI', 'T20']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('captains');
    }
};
