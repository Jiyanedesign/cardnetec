<?php

use App\Http\Controllers\PublicController;
use App\Http\Controllers\QuoteRequestController;
use Illuminate\Support\Facades\Route;

// Rutas Públicas de CardNet.ec
Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/nosotros', [PublicController::class, 'nosotros'])->name('nosotros');
Route::get('/productos', [PublicController::class, 'productos'])->name('productos');
Route::get('/personalizacion', [PublicController::class, 'personalizacion'])->name('personalizacion');
Route::get('/empresas', [PublicController::class, 'empresas'])->name('empresas');
Route::get('/cotizacion', [PublicController::class, 'cotizacion'])->name('cotizacion');
Route::get('/contacto', [PublicController::class, 'contacto'])->name('contacto');
Route::get('/faq', [PublicController::class, 'faq'])->name('faq');

// Simuladores Canvas (Interactivos)
Route::get('/simulador', [PublicController::class, 'simulador'])->name('simulador');
Route::get('/simulador/carnets', [PublicController::class, 'simuladorCarnets'])->name('simulador.carnets');

// Post de Cotizaciones
Route::post('/cotizacion/enviar', [QuoteRequestController::class, 'store'])->name('cotizacion.store');
