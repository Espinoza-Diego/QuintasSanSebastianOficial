<?php
namespace App\Http\Controllers;

use App\Models\Quinta;
use App\Models\Reserva;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;


class ReservaController extends Controller
{
    /**
     * Muestra el formulario para reservar una quinta.
     */
    public function create(Quinta $quinta)
    {
        abort_unless($quinta->activa, 404);

        return view('quintas.reservar', compact('quinta'));
    }

    /**
     * Valida y registra una nueva reserva.
     */
    public function store(Request $request, Quinta $quinta)
    {
        abort_unless($quinta->activa, 404);

        $data = $request->validate([
            'fecha' => ['required', 'date', 'after_or_equal:today'],
            'hora' => ['required', 'in:' . implode(',', $quinta->horarios ?? [])],
            'personas' => ['required', 'integer', 'between:2,12'],
            'nombres' => ['required', 'string', 'min:2', 'max:100'],
            'apellidos' => ['required', 'string', 'min:2', 'max:100'],
            'correo' => ['required', 'email', 'max:150'],
            'celular' => ['required', 'regex:/^9\d{8}$/'],
            'notas' => ['nullable', 'string', 'max:300'],
            'acepta' => ['accepted'],
        ], [
            'celular.regex' => 'Ingresa un celular peruano de 9 dígitos que empiece con 9.',
            'acepta.accepted' => 'Debes aceptar las condiciones y el aviso de privacidad.',
        ]);

        $reserva = DB::transaction(function () use ($data, $quinta, $request) {

            // Bloquea la quinta para evitar superar su capacidad.
            $bloqueada = Quinta::whereKey($quinta->id)
                ->lockForUpdate()
                ->firstOrFail();

            // Calcula las personas que ya tienen reserva confirmada.
            $ocupadas = Reserva::where('quinta_id', $quinta->id)
                ->whereDate('fecha', $data['fecha'])
                ->where('hora', $data['hora'])
                ->where('estado', 'confirmada')
                ->sum('personas');

            // Verifica que exista capacidad disponible.
            if ($ocupadas + (int) $data['personas'] > $bloqueada->capacidad) {
                throw ValidationException::withMessages([
                    'hora' => 'Ya no hay aforo suficiente para ese horario. Elige otro horario o reduce el grupo.',
                ]);
            }

            // Guarda la reserva en la base de datos.
            return Reserva::create([
                'codigo' => 'RES-' . Str::upper(Str::random(7)),
                'quinta_id' => $quinta->id,
                'fecha' => $data['fecha'],
                'hora' => $data['hora'],
                'personas' => $data['personas'],
                'nombres' => $data['nombres'],
                'apellidos' => $data['apellidos'],
                'correo' => $data['correo'],
                'celular' => $data['celular'],
                'notas' => $data['notas'] ?? null,
                'estado' => 'confirmada',
                'session_key' => hash('sha256', $request->session()->getId()),
            ]);
        });

        return redirect()
            ->route('reservas.confirmacion', $reserva)
            ->with('success', 'Tu reserva fue registrada correctamente.');
    }

    /**
     * Muestra únicamente las reservas de la sesión actual.
     */
    public function index(Request $request)
    {
        $key = hash('sha256', $request->session()->getId());

        $reservas = Reserva::with('quinta')
            ->where('session_key', $key)
            ->latest()
            ->get();

        return view('quintas.reservas', compact('reservas'));
    }

    /**
     * Muestra la confirmación de una reserva propia.
     */
    public function confirmacion(Request $request, Reserva $reserva)
    {
        $key = hash('sha256', $request->session()->getId());

        abort_unless(
            hash_equals((string) $reserva->session_key, $key),
            403
        );

        return view('quintas.confirmacion', compact('reserva'));
    }

    /**
     * Cancela una reserva sin eliminarla de la base de datos.
     */
    public function cancelar(Request $request, Reserva $reserva)
    {
        $key = hash('sha256', $request->session()->getId());

        abort_unless(
            hash_equals((string) $reserva->session_key, $key),
            403
        );

        if ($reserva->fecha->isFuture() || $reserva->fecha->isToday()) {
            $reserva->update([
                'estado' => 'cancelada',
            ]);
        }

        return back()->with(
            'success',
            'Se actualizó el estado de la reserva.'
        );
    }
}
