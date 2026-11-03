@extends('layouts.app')

@section('title', $quinta->nombre . ' | Quintas de San Sebastián')

@section('content')

<nav class="migas" aria-label="Ruta de navegación">
    <a href="{{ route('quintas.index') }}">Explorar quintas</a>
    <span> / {{ $quinta->nombre }}</span>
</nav>

<div class="cab-detalle">
    <div>
        <p class="etiqueta">CONOCE ESTE LUGAR</p>
        <h1 class="t1">{{ $quinta->nombre }}</h1>
        <p class="meta">{{ $quinta->especialidad }} · {{ $quinta->zona }} · San Sebastián, Cusco</p>
    </div>
    <a href="{{ route('quintas.index') }}" class="btn btn-borde">Volver a explorar</a>
</div>

<div class="detalle">
    <div class="contenido-detalle">

        <section class="seccion-detalle">
            <p class="etiqueta">INFORMACIÓN GENERAL</p>
            <h2>Una experiencia para compartir</h2>
            <p>{{ $quinta->especialidad }} en {{ $quinta->zona }}. Conoce este establecimiento y consulta sus servicios y horarios disponibles.</p>

            <div class="datos">
                <div>
                    <span>Precio por persona</span>
                    <b>S/ {{ number_format($quinta->precio_min, 2) }} - S/ {{ number_format($quinta->precio_max, 2) }}</b>
                </div>
                <div>
                    <span>Capacidad</span>
                    <b>{{ $quinta->capacidad }} personas</b>
                </div>
                <div>
                    <span>Ubicación</span>
                    <b>{{ $quinta->zona }}</b>
                </div>
            </div>
        </section>

        <section class="seccion-detalle">
            <p class="etiqueta">SERVICIOS</p>
            <h2>Espacios y servicios</h2>
            <div class="lista-tags">
                @forelse($quinta->servicios ?? [] as $servicio)
                    <span class="tag">{{ $servicio }}</span>
                @empty
                    <p>Información de servicios pendiente.</p>
                @endforelse
            </div>
        </section>

        <section class="seccion-detalle">
            <p class="etiqueta">UBICACIÓN</p>
            <h2>Ubicación referencial</h2>
            <p>{{ $quinta->zona }}, San Sebastián, Cusco.</p>

            <div
                id="mapa-q"
                class="mapa-q"
                role="application"
                aria-label="Mapa referencial de {{ $quinta->nombre }}">
            </div>

            <p class="nota">
                Coordenadas referenciales:
                {{ $quinta->latitud }}, {{ $quinta->longitud }}.
                Confirma la dirección antes de realizar tu visita.
            </p>

            <a
                class="btn"
                href="https://www.google.com/maps/search/?api=1&query={{ urlencode($quinta->latitud . ',' . $quinta->longitud) }}"
                target="_blank"
                rel="noopener noreferrer">
                Abrir ubicación en Google Maps
            </a>
        </section>

    </div>

    <aside class="panel fijo">
        <p class="etiqueta">PLANIFICA TU VISITA</p>
        <h2>Reserva tu mesa</h2>
        <p class="nota">Horarios disponibles:</p>

        <div class="lista-tags">
            @forelse($quinta->horarios ?? [] as $horario)
                <span class="tag">{{ $horario }}</span>
            @empty
                <span class="nota">Horarios pendientes de registrar.</span>
            @endforelse
        </div>

        <hr>

        <div class="precio-panel">
            <span>Precio referencial por persona</span>
            <strong>S/ {{ number_format($quinta->precio_min, 2) }}</strong>
        </div>

        <a class="btn btn-reservar" href="{{ route('reservas.create', $quinta) }}">
            Continuar con la reserva
        </a>

        <p class="nota">Sin pago anticipado en este prototipo académico.</p>
    </aside>
</div>

@endsection

@push('styles')
<style>
.mapa-q {
    width: 100%;
    height: 350px;
    border-radius: 14px;
    overflow: hidden;
    margin: 18px 0;
    background: #eee;
    z-index: 1;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const elemento = document.getElementById('mapa-q');

    if (!elemento || typeof L === 'undefined') {
        if (elemento) {
            elemento.innerHTML = 'No se pudo cargar el mapa. Comprueba tu conexión a Internet.';
        }
        return;
    }

    const latitud = @json((float) $quinta->latitud);
    const longitud = @json((float) $quinta->longitud);
    const punto = [latitud, longitud];

    if (
        !Number.isFinite(latitud) ||
        !Number.isFinite(longitud) ||
        latitud < -90 || latitud > 90 ||
        longitud < -180 || longitud > 180
    ) {
        elemento.innerHTML = 'Las coordenadas de esta quinta no son válidas.';
        return;
    }

    const mapa = L.map(elemento, {
        scrollWheelZoom: false
    }).setView(punto, 16);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap'
    }).addTo(mapa);

    L.marker(punto)
        .addTo(mapa)
        .bindPopup('<strong>{{ addslashes($quinta->nombre) }}</strong><br>Ubicación referencial')
        .openPopup();

    setTimeout(function () {
        mapa.invalidateSize();
    }, 300);
});
</script>
@endpush
