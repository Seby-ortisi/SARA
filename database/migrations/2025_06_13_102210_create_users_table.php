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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nome')->nullable();
            $table->string('cognome')->nullable();
            $table->string('email')->nullable();
            $table->string('cellulare')->nullable();
            $table->date('data_nascita')->nullable();
            $table->string('codice_fiscale')->nullable();
            $table->string('ldap_username')->nullable();
            $table->unsignedBigInteger('ruolo_id')->nullable();

            $table->timestamps();


           $table->foreign('ruolo_id')->references('id')->on('roles')->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
