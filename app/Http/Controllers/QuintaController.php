<?php

namespace App\Http\Controllers;

use App\Models\Quinta;
use Illuminate\Http\Request;

class QuintaController extends Controller
{
    /** Lista quintas activas y aplica filtros en el servidor. */
    public function index(Request $request)
    {
        $quintas = Quinta::query()->where('activa', true)
            ->when($request->filled('q'), fn ($q) => $q->where(fn ($w) => $w->where('nombre','like','%'.$request->q.'%')->orWhere('zona','like','%'.$request->q.'%')))
            ->when($request->filled('categoria'), fn ($q) => $q->where('categoria', $request->categoria))
            ->when($request->filled('precio'), function ($q) use ($request) {
                match ($request->precio) {
                    '30' => $q->where('precio_min', '<=', 30),
                    '50' => $q->whereBetween('precio_min', [30.01, 50]),
                    'mas' => $q->where('precio_min', '>', 50),
                    default => null,
                };
            })
            ->when($request->filled('orden'), fn ($q) => $request->orden === 'pd' ? $q->orderByDesc('precio_min') : ($request->orden === 'pa' ? $q->orderBy('precio_min') : $q->orderBy('nombre')))
            ->paginate(6)->withQueryString();

        $categorias = Quinta::where('activa', true)->select('categoria')->distinct()->orderBy('categoria')->pluck('categoria');
        return view('quintas.index', compact('quintas', 'categorias'));
    }

    /** Muestra el detalle y horarios del establecimiento seleccionado. */
    public function show(Quinta $quinta)
    {
        abort_unless($quinta->activa, 404);
        return view('quintas.show', compact('quinta'));
    }
}
