<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_ets', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('ets_id');
            $table->tinyInteger('abilitato')->default(1);
            $table->timestamps();

            // Indici
            $table->index('user_id');
            $table->index('ets_id');

            // Chiavi esterne (opzionali)
            // $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            // $table->foreign('ets_id')->references('id')->on('ets')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_ets');
    }
};
