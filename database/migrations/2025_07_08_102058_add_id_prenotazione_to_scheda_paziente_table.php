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
        Schema::table('scheda_paziente', function (Blueprint $table) {
            $table->unsignedBigInteger('id_prenotazione')->nullable()->after('id');
            $table->foreign('id_prenotazione')
                ->references('id')
                ->on('prenotazioni')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scheda_paziente', function (Blueprint $table) {
            //
        });
    }
};
