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
        Schema::create('prenotazioni', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_paziente');
            $table->date('data_prenotazione');
            $table->time('ora_prenotazione');
            $table->integer('centro_vaccinale_id');
            $table->integer('stato_prenotazione');
            $table->integer('id_prenotazione');
            $table->integer('creato_da');
            $table->timestamps();

            $table->foreign('id_paziente')->references('id')->on('users')->onDelete('cascade');
        });

          
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prenotazioni');
    }
};
