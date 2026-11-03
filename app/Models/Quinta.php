<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quinta extends Model
{
    // Campos que pueden registrarse o actualizarse.
    protected $fillable = [
        'nombre',
        'zona',
        'categoria',
        'especialidad',
        'precio_min',
        'precio_max',
        'servicios',
        'horarios',
        'capacidad',
        'latitud',
        'longitud',
        'activa',
    ];

    // Conversión automática de datos.
    protected function casts(): array
    {
        return [
            'servicios' => 'array',
            'horarios' => 'array',
            'activa' => 'boolean',
            'precio_min' => 'decimal:2',
            'precio_max' => 'decimal:2',
            'latitud' => 'float',
            'longitud' => 'float',
        ];
    }

    // Una quinta puede tener muchas reservas.
    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class);
    }
}