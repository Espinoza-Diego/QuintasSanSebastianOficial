@extends('layouts.app')

@section('title', 'Reserva confirmada | Quintas de San Sebastián')

@section('content')

<section class="listado">

    <div class="cab-res">
        <div>
            <p class="etiqueta">¡RESERVA REGISTRADA!</p>
            <h1 class="t1">Tu reserva está confirmada</h1>
            <p class="meta">
                Gracias por elegir Quintas de San Sebastián.
                Aquí tienes el resumen de tu reserva.
            </p>
        </div>
    </div>

    <div class="card">
        <div class="cuerpo">

            <p class="etiqueta">COMPROBANTE DE RESERVA</p>

            <h2>{{ $reserva->quinta->nombre }}</h2>

            <p>
                <strong>Código de reserva:</strong>
                {{ $reserva->codigo }}
            </p>

            <p>
                <strong>Fecha:</strong>
                {{ $reserva->fecha->format('d/m/Y') }}
            </p>

            <p>
                <strong>Hora:</strong>
                {{ $reserva->hora }}
            </p>

            <p>
                <strong>Número de personas:</strong>
                {{ $reserva->personas }}
            </p>

            <p>
                <strong>Nombre:</strong>
                {{ $reserva->nombres }} {{ $reserva->apellidos }}
            </p>

            <p>
                <strong>Correo electrónico:</strong>
                {{ $reserva->correo }}
            </p>

            <p>
                <strong>Celular:</strong>
                {{ $reserva->celular }}
            </p>

            @if($reserva->notas)
                <p>
                    <strong>Indicaciones especiales:</strong>
                    {{ $reserva->notas }}
                </p>
            @endif

            <p>
                <strong>Estado:</strong>
                {{ ucfirst($reserva->estado) }}
            </p>

            <p class="nota">
                Guarda tu código de reserva para consultar tus visitas
                desde este navegador.
            </p>

            <div class="acciones">
                <a href="{{ route('quintas.reservas') }}" class="btn">
                    Ver mis reservas
                </a>

                <a href="{{ route('quintas.index') }}" class="btn btn-borde">
                    Explorar quintas
                </a>
            </div>

        </div>
    </div>

</section>

@endsection