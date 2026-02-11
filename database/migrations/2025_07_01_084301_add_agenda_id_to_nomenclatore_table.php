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
        Schema::table('nomenclatore', function (Blueprint $table) {
            $table->unsignedBigInteger('agenda_id')->nullable()->after('id');
            $table->foreign('agenda_id')->references('id')->on('agenda')->onDelete('set null');
        
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('nomenclatore', function (Blueprint $table) {
            //
        });
    }
};
