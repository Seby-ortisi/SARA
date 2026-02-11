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
        Schema::create('padre_figlio', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('padre_id');
            $table->unsignedBigInteger('figlio_id'); 
            $table->integer('stato')->length(1); 
            $table->integer('ruolo_referente');
            $table->timestamps();

            $table->foreign("padre_id" , "fk_padre_id_20240620")->on ("users")->references("id");
            $table->foreign("figlio_id" , "fk_figlio_id_20240620")->on ("users")->references("id");


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('padre_figlio');
    }
};
