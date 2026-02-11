<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ets', function (Blueprint $table) {
            $table->string('sede_legale')->nullable();
            $table->string('indirizzo_pec_mail')->nullable();
            $table->string('indirizzo_dpo')->nullable();
            $table->string('telefono')->nullable();  // tel/cell
        });
    }

    public function down(): void
    {
        Schema::table('ets', function (Blueprint $table) {
            $table->dropColumn([
                'sede_legale',
                'indirizzo_pec_mail',
                'indirizzo_dpo',
                'telefono'
            ]);
        });
    }
};