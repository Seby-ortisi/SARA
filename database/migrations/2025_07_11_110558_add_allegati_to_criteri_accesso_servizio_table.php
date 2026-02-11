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
        Schema::table('criteri_accesso_servizio', function (Blueprint $table) {
            $table->string('copia_primo_foglio_ISEE_minorenne')->nullable()->after('allegato_tessera_sanitaria');
            $table->string('documento_genitore')->nullable()->after('copia_primo_foglio_ISEE_minorenne');
            $table->string('copia_primo_foglio_ISEE')->nullable()->after('documento_genitore');
            $table->string('permesso_soggiorno')->nullable()->after('copia_primo_foglio_ISEE');
            $table->string('allegato_documento_identita')->nullable()->after('permesso_soggiorno');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('criteri_accesso_servizio', function (Blueprint $table) {
            //
        });
    }
};
