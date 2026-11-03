
@extends('layouts.app')

@section('title', 'Mis reservas | Quintas de San Sebastián')

@section('content')

<section class="listado">

    <div class="cab-res">
        <div>
            <p class="etiqueta">TUS VISITAS</p>

            <h1 class="t1">Mis reservas</h1>

            <p class="nota">
                Aquí puedes consultar las reservas realizadas
                desde este navegador.
            </p>
        </div>

        <a href="{{ route('quintas.index') }}" class="btn">
            Explorar quintas
        </a>
    </div>

    <ul class="cards">

        @forelse($reservas as $reserva)

            <li class="card">

                <div class="cuerpo">

                    <p class="etiqueta">
                        CÓDIGO DE RESERVA: {{ $reserva->codigo }}
                    </p>

                    <h3>{{ $reserva->quinta->nombre }}</h3>

                    <p>
                        <strong>Fecha:</strong>
                        {{ $reserva->fecha->format('d/m/Y') }}
                    </p>

                    <p>
                        <strong>Hora:</strong>
                        {{ $reserva->hora }}
                    </p>

                    <p>
                        <strong>Personas:</strong>
                        {{ $reserva->personas }}
                    </p>

                    <p>
                        <strong>Reservado a nombre de:</strong>
                        {{ $reserva->nombres }} {{ $reserva->apellidos }}
                    </p>

                    <p>
                        <strong>Estado:</strong>
                        {{ ucfirst($reserva->estado) }}
                    </p>

                    @if($reserva->estado === 'confirmada')

                        <form
                            method="POST"
                            action="{{ route('reservas.cancelar', $reserva) }}"
                            onsubmit="return confirm('¿Deseas cancelar esta reserva?')">

                            @csrf
                            @method('PATCH')

                            <button class="btn btn-chico btn-borde" type="submit">
                                Cancelar reserva
                            </button>

                        </form>

                    @endif

                </div>

            </li>

        @empty

            <li class="vacio">

                <h3>Todavía no tienes reservas</h3>

                <p>
                    Elige una quinta y solicita tu mesa
                    para comenzar.
                </p>

                <a href="{{ route('quintas.index') }}" class="btn">
                    Explorar quintas
                </a>

            </li>

        @endforelse

    </ul>

</section>

@endsection
