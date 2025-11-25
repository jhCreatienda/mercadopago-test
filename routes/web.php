<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MercadoPagoTestController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/mercadopago/test', [MercadoPagoTestController::class, 'index']);
Route::post('/mercadopago/create-preference', [MercadoPagoTestController::class, 'createPreference']);
Route::get('/mercadopago/success', [MercadoPagoTestController::class, 'success']);
Route::get('/mercadopago/failure', [MercadoPagoTestController::class, 'failure']);
Route::get('/mercadopago/pending', [MercadoPagoTestController::class, 'pending']);
Route::post('/mercadopago/webhook', [MercadoPagoTestController::class, 'webhook']);
Route::get('/mercadopago/payment/{id}', [MercadoPagoTestController::class, 'getPayment']);
// Rutas de Customer y Cards
Route::post('/mercadopago/create-customer', [MercadoPagoTestController::class, 'createCustomer']);
Route::post('/mercadopago/save-card', [MercadoPagoTestController::class, 'saveCard']);
Route::post('/mercadopago/process-payment', [MercadoPagoTestController::class, 'processPayment']);
Route::get('/mercadopago/customer/{id}', [MercadoPagoTestController::class, 'getCustomer']);
Route::get('/mercadopago/customer/{id}/cards', [MercadoPagoTestController::class, 'getCustomerCards']);
