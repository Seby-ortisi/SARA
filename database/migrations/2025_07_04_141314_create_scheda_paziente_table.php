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
        Schema::create('scheda_paziente', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('paziente_id');
            $table->string('diagnosi')->nullable();
            $table->string('prestazione_erogata')->nullable(); 
            $table->string("luogo_prestazione")->nullable();
            $table->string('erogazione_farmaci')->nullable();
            $table->string('tipologia_farmaco')->nullable();
            $table->string('erogazione_dispositivo_medico')->nullable();
            $table->string('tipologia_dispositivo_medico')->nullable();

            // Foreign key constraint
            $table->foreign('paziente_id')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheda_paziente');
    }
};
