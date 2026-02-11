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
        Schema::create('criteri_accesso_servizio', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user');
            $table->integer('titolo_studio')->nullable();
            $table->integer('condizione_professionale')->nullable();
            $table->integer('cerca_lavoro')->nullable();
            $table->integer('categoria_vulenerabilita')->nullable();
            $table->integer('criteri_contesto')->nullable();
            $table->integer('criteri_persona')->nullable();
            $table->string('allegato_tessera_sanitaria')->nullable();
            $table->timestamps();

             $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('criteri_accesso_servizio');
    }
};
