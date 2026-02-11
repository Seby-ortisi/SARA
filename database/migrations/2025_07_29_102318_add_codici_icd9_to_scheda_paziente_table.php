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
            $table->string('codici_icd9')->nullable()->after('luogo_prestazione');
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
