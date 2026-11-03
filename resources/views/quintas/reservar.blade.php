@extends('layouts.app')

@section('title', 'Reservar en '.$quinta->nombre)

@section('content')

<nav class="migas" aria-label="Ruta de navegación">
    <a href="{{ route('quintas.index') }}">Explorar quintas</a>
    /
    <a href="{{ route('quintas.show', $quinta) }}">{{ $quinta->nombre }}</a>
    / Reservar
</nav>

<h1 class="t1">Completa tu reserva</h1>

<p class="meta">
    Revisa los detalles de tu visita y completa tus datos de contacto.
    Los campos marcados con * son obligatorios.
</p>

<div class="res">

    <form method="POST" action="{{ route('reservas.store', $quinta) }}">
        @csrf

        <h2>1. Tu reserva</h2>

        <div class="fila">

            <div class="campo">
                <label for="fecha">Fecha de visita *</label>

                <input
                    id="fecha"
                    name="fecha"
                    type="date"
                    min="{{ now()->toDateString() }}"
                    value="{{ old('fecha', now()->toDateString()) }}"
                    required
                >

                @error('fecha')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="campo">
                <label for="hora">Horario *</label>

                <select id="hora" name="hora" required>
                    <option value="">Selecciona un horario</option>

                    @foreach($quinta->horarios ?? [] as $h)
                        <option
                            value="{{ $h }}"
                            @selected(old('hora') == $h)
                        >
                            {{ $h }}
                        </option>
                    @endforeach
                </select>

                @error('hora')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="campo">
                <label for="personas">Número de personas *</label>

                <select id="personas" name="personas" required>
                    @for($i = 2; $i <= 12; $i++)
                        <option
                            value="{{ $i }}"
                            @selected(old('personas', 4) == $i)
                        >
                            {{ $i }} personas
                        </option>
                    @endfor
                </select>

                @error('personas')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

        </div>

        <h2>2. Datos de contacto</h2>

        <div class="fila">

            <div class="campo">
                <label for="nombres">Nombres *</label>

                <input
                    id="nombres"
                    name="nombres"
                    type="text"
                    value="{{ old('nombres') }}"
                    autocomplete="given-name"
                    maxlength="100"
                    required
                >

                @error('nombres')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="campo">
                <label for="apellidos">Apellidos *</label>

                <input
                    id="apellidos"
                    name="apellidos"
                    type="text"
                    value="{{ old('apellidos') }}"
                    autocomplete="family-name"
                    maxlength="100"
                    required
                >

                @error('apellidos')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

        </div>

        <div class="fila">

            <div class="campo">
                <label for="correo">Correo electrónico *</label>

                <input
                    id="correo"
                    name="correo"
                    type="email"
                    value="{{ old('correo') }}"
                    autocomplete="email"
                    maxlength="150"
                    required
                >

                @error('correo')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="campo">
                <label for="celular">Número de celular *</label>

                <input
                    id="celular"
                    name="celular"
                    type="tel"
                    inputmode="numeric"
                    placeholder="900000000"
                    pattern="9[0-9]{8}"
                    maxlength="9"
                    value="{{ old('celular') }}"
                    autocomplete="tel"
                    required
                >

                @error('celular')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

        </div>

        <div class="campo">
            <label for="notas">
                ¿Alguna indicación especial? (Opcional)
            </label>

            <textarea
                id="notas"
                name="notas"
                maxlength="300"
                rows="4"
                placeholder="Escribe aquí alguna observación..."
            >{{ old('notas') }}</textarea>

            @error('notas')
                <span class="error">{{ $message }}</span>
            @enderror
        </div>

        <h2>3. Confirmación</h2>

        <div class="campo">

            <label class="chk">
                <input
                    type="checkbox"
                    name="acepta"
                    value="1"
                    required
                    @checked(old('acepta'))
                >

                Acepto las condiciones de reserva y el aviso de privacidad. *
            </label>

            @error('acepta')
                <span class="error">{{ $message }}</span>
            @enderror

        </div>

        <button class="btn" type="submit">
            Confirmar reserva →
        </button>

        <a
            class="btn btn-borde"
            href="{{ route('quintas.show', $quinta) }}"
        >
            Volver
        </a>

    </form>

</div>

@endsection