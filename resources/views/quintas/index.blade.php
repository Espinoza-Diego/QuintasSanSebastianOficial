@extends("layouts.app")

@section("title", "Explorar quintas | Quintas de San Sebastián")

@section("content")
<section class="hero">
    <div class="hero-inner">
        <p class="meta">DESCUBRE SAN SEBASTIÁN</p>
        <h1>Encuentra tu próxima experiencia</h1>
        <p class="hero-description">Explora las mejores quintas de San Sebastián y encuentra el lugar ideal para compartir momentos especiales.</p>
        <form method="GET" action="{{ route("quintas.index") }}" class="barra">
            <div class="campo">
                <label for="q">¿Qué lugar buscas?</label>
                <input id="q" type="search" name="q" placeholder="Nombre o zona de la quinta" value="{{ request("q") }}">
            </div>
            <div class="campo">
                <label for="categoria">Categoría</label>
                <select id="categoria" name="categoria">
                    <option value="">Todas las categorías</option>
                    @foreach($categorias as $categoria)
                        <option value="{{ $categoria }}" @selected(request("categoria") == $categoria)>{{ $categoria }}</option>
                    @endforeach
                </select>
            </div>
            <div class="campo">
                <label for="orden">Ordenar por</label>
                <select id="orden" name="orden">
                    <option value="">Nombre</option>
                    <option value="pa" @selected(request("orden") == "pa")>Menor precio</option>
                    <option value="pd" @selected(request("orden") == "pd")>Mayor precio</option>
                </select>
            </div>
            <button type="submit" class="btn">Buscar quintas</button>
        </form>
    </div>
</section>

<section class="listado">
    <div class="cab-res">
        <div>
            <p class="meta">EXPLORA NUESTRA SELECCIÓN</p>
            <h2>Quintas disponibles</h2>
            <p class="nota">Encuentra un espacio para disfrutar con familiares y amigos.</p>
        </div>
        <span class="tag">{{ $quintas->total() }} establecimientos</span>
    </div>

    @if($quintas->count())
        <div class="cards">
            @foreach($quintas as $quinta)
                <article class="card">
                    <div class="foto">
                        <div class="foto-card" style="background: linear-gradient(135deg, #78917C, #304F3D); display:flex; align-items:center; justify-content:center; color:white; font-weight:bold;">{{ $quinta->nombre }}</div>
                    </div>
                    <div class="cuerpo">
                        <span class="etiqueta">{{ $quinta->categoria }}</span>
                        <h3>{{ $quinta->nombre }}</h3>
                        <p class="descripcion-card">{{ $quinta->zona }}</p>
                        <p>{{ $quinta->especialidad }}</p>
                        <div class="linea-card">
                            <span class="precio">Desde S/ {{ number_format($quinta->precio_min, 2) }}</span>
                            <a href="{{ route("quintas.show", $quinta) }}" class="btn-chico">Ver detalles</a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
        <div class="pag">{{ $quintas->links() }}</div>
    @else
        <div class="vacio">
            <h3>No encontramos quintas</h3>
            <p>Prueba con otro nombre, zona o categoría.</p>
            <a href="{{ route("quintas.index") }}" class="btn">Ver todas las quintas</a>
        </div>
    @endif
</section>
@endsection
