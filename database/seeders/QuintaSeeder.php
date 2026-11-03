<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class QuintaSeeder extends Seeder
{
    public function run(): void
    {
        $quintas = [
            [
                'id' => 1,
                'nombre' => 'Quinta Los Jardines',
                'zona' => 'Zona central',
                'categoria' => 'Cusqueña y regional',
                'especialidad' => 'Comida cusqueña',
                'precio_min' => 35.00,
                'precio_max' => 55.00,
                'servicios' => json_encode(['Jardín', 'Salón interior', 'Estacionamiento', 'Acceso sin escalones'], JSON_UNESCAPED_UNICODE),
                'horarios' => json_encode(['13:00', '13:30', '14:00']),
                'capacidad' => 38,
                'latitud' => -13.5333000,
                'longitud' => -71.9167000,
                'activa' => 1,
            ],
            [
                'id' => 2,
                'nombre' => 'Quinta El Fogón Sebastiano',
                'zona' => 'Zona sur',
                'categoria' => 'Parrillas',
                'especialidad' => 'Parrillas y comida regional',
                'precio_min' => 40.00,
                'precio_max' => 65.00,
                'servicios' => json_encode(['Terraza', 'Juegos infantiles'], JSON_UNESCAPED_UNICODE),
                'horarios' => json_encode(['12:30', '13:00', '14:00']),
                'capacidad' => 30,
                'latitud' => -13.5385000,
                'longitud' => -71.9230000,
                'activa' => 1,
            ],
            [
                'id' => 3,
                'nombre' => 'Quinta La Campiña',
                'zona' => 'Zona este',
                'categoria' => 'Cusqueña y regional',
                'especialidad' => 'Comida tradicional',
                'precio_min' => 30.00,
                'precio_max' => 50.00,
                'servicios' => json_encode(['Jardín', 'Juegos infantiles'], JSON_UNESCAPED_UNICODE),
                'horarios' => json_encode(['13:00', '14:00', '14:30']),
                'capacidad' => 24,
                'latitud' => -13.5420000,
                'longitud' => -71.9100000,
                'activa' => 1,
            ],
            [
                'id' => 4,
                'nombre' => 'Quinta Sabor de Casa',
                'zona' => 'Zona central',
                'categoria' => 'Comida casera',
                'especialidad' => 'Comida casera y regional',
                'precio_min' => 25.00,
                'precio_max' => 45.00,
                'servicios' => json_encode(['Salón interior', 'Estacionamiento'], JSON_UNESCAPED_UNICODE),
                'horarios' => json_encode(['12:00', '13:00', '13:30']),
                'capacidad' => 40,
                'latitud' => -13.5300000,
                'longitud' => -71.9200000,
                'activa' => 1,
            ],
            [
                'id' => 5,
                'nombre' => 'Quinta El Molle',
                'zona' => 'Plaza de San Sebastián',
                'categoria' => 'Cusqueña y regional',
                'especialidad' => 'Chicharrón y lechón',
                'precio_min' => 30.00,
                'precio_max' => 48.00,
                'servicios' => json_encode(['Jardín', 'Estacionamiento'], JSON_UNESCAPED_UNICODE),
                'horarios' => json_encode(['12:00', '13:00', '14:00']),
                'capacidad' => 36,
                'latitud' => -13.5355000,
                'longitud' => -71.9150000,
                'activa' => 1,
            ],
            [
                'id' => 6,
                'nombre' => 'Quinta Tumbo',
                'zona' => 'Larapa',
                'categoria' => 'Parrillas',
                'especialidad' => 'Cuy y parrillas',
                'precio_min' => 45.00,
                'precio_max' => 70.00,
                'servicios' => json_encode(['Terraza', 'Acceso sin escalones'], JSON_UNESCAPED_UNICODE),
                'horarios' => json_encode(['13:00', '13:30', '14:30']),
                'capacidad' => 28,
                'latitud' => -13.5400000,
                'longitud' => -71.9260000,
                'activa' => 1,
            ],
            [
                'id' => 7,
                'nombre' => 'Quinta Los Retamas',
                'zona' => 'Larapa',
                'categoria' => 'Comida casera',
                'especialidad' => 'Trucha y comida casera',
                'precio_min' => 28.00,
                'precio_max' => 42.00,
                'servicios' => json_encode(['Jardín', 'Juegos infantiles'], JSON_UNESCAPED_UNICODE),
                'horarios' => json_encode(['12:30', '13:30', '14:00']),
                'capacidad' => 24,
                'latitud' => -13.5440000,
                'longitud' => -71.9120000,
                'activa' => 1,
            ],
            [
                'id' => 8,
                'nombre' => 'Quinta Sol de Maíz',
                'zona' => 'Plaza de San Sebastián',
                'categoria' => 'Cusqueña y regional',
                'especialidad' => 'Chicharrón cusqueño',
                'precio_min' => 32.00,
                'precio_max' => 52.00,
                'servicios' => json_encode(['Jardín', 'Salón interior', 'Estacionamiento'], JSON_UNESCAPED_UNICODE),
                'horarios' => json_encode(['12:00', '12:30', '13:00']),
                'capacidad' => 50,
                'latitud' => -13.5290000,
                'longitud' => -71.9210000,
                'activa' => 1,
            ],
        ];

        foreach ($quintas as $quinta) {
            DB::table('quintas')->updateOrInsert(
                ['id' => $quinta['id']],
                $quinta
            );
        }
    }
}