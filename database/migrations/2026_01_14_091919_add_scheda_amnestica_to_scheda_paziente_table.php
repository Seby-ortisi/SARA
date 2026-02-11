<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('scheda_paziente', function (Blueprint $table) {
            $table->text('scheda_amnestica')->nullable()->after('tipologia_dispositivo_medico');
        });
    }

    public function down(): void
    {
        Schema::table('scheda_paziente', function (Blueprint $table) {
            $table->dropColumn('scheda_amnestica');
        });
    }
};
