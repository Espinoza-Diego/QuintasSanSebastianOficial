<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reserva extends Model
{
    // Campos que pueden guardarse mediante asignación masiva.
    protected $fillable = [
        'codigo',
        'quinta_id',
        'fecha',
        'hora',
        'personas',
        'nombres',
        'apellidos',
        'correo',
        'celular',
        'notas',
        'estado',
        'session_key',
    ];

    // Conversión automática de tipos de datos.
    protected function casts(): array
    {
        return [
            'fecha' => 'date',
            'personas' => 'integer',
        ];
    }

    // Cada reserva pertenece a una quinta.
    public function quinta(): BelongsTo
    {
        return $this->belongsTo(Quinta::class);
    }
}