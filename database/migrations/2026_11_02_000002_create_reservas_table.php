<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();

            $table->string('codigo', 20)->unique();

            $table->foreignId('quinta_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('fecha');
            $table->string('hora', 5);
            $table->unsignedTinyInteger('personas');

            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->string('correo', 150);
            $table->string('celular', 20);

            $table->string('notas', 300)->nullable();

            $table->string('estado', 20)->default('confirmada');

            // Identifica las reservas realizadas desde la sesión del navegador.
            $table->string('session_key', 64)->index();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};