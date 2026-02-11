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
        // 1. Aggiungi le colonne (nazionalita come string)
       Schema::table('users', function (Blueprint $table) {
            $table->string('nazionalita')->nullable()->after('codice_fiscale');
            $table->unsignedBigInteger('provincia')->nullable()->after('nazionalita');
            $table->unsignedBigInteger('stato')->nullable()->after('ruolo_id');
            $table->unsignedBigInteger('modalita_autenticazione')->nullable()->after('stato');
            $table->unsignedBigInteger('creato_da')->nullable()->after('modalita_autenticazione');
});



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
   
    }
};
