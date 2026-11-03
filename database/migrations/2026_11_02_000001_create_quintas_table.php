<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Si la tabla ya existe, se conserva y no se modifica.
        if (Schema::hasTable('quintas')) {
            return;
        }

        Schema::create('quintas', function (Blueprint $table) {
            $table->id();

            $table->string('nombre');
            $table->string('zona');
            $table->string('categoria');
            $table->string('especialidad');

            $table->decimal('precio_min', 8, 2);
            $table->decimal('precio_max', 8, 2);

            $table->json('servicios');
            $table->json('horarios');

            $table->unsignedSmallInteger('capacidad');

            $table->decimal('latitud', 10, 7)->nullable();
            $table->decimal('longitud', 10, 7)->nullable();

            $table->boolean('activa')->default(true);

            $table->timestamps();

            $table->index(['activa', 'categoria']);
        });
    }

    public function down(): void
    {
        // Se conserva la tabla para evitar borrar datos preexistentes.
    }
};