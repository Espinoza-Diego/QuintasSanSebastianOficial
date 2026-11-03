<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\QuintaController;
use App\Http\Controllers\ReservaController;

// Pagina principal
Route::get("/", [QuintaController::class, "index"])->name("quintas.index");

// Detalles de una quinta
Route::get("/quintas/{quinta}", [QuintaController::class, "show"])->name("quintas.show");

// Formulario de reserva
Route::get("/quintas/{quinta}/reservar", [ReservaController::class, "create"])->name("reservas.create");

// Registrar reserva
Route::post("/quintas/{quinta}/reservar", [ReservaController::class, "store"])->name("reservas.store");

// Consultar reservas
Route::get("/reservas", [ReservaController::class, "index"])->name("quintas.reservas");

// Cancelar reserva
Route::patch("/reservas/{reserva}/cancelar", [ReservaController::class, "cancelar"])->name("reservas.cancelar");

// Confirmacion
Route::get("/reservas/{reserva}/confirmacion", [ReservaController::class, "confirmacion"])->name("reservas.confirmacion");
