<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Client\Customer\CustomerClient;
use MercadoPago\Client\CustomerCard\CustomerCardClient;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;

class MercadoPagoTestController extends Controller
{
    public function __construct()
    {
        // Configurar el SDK con el access token
        MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
    }

    /**
     * Mostrar formulario de prueba
     */
    public function index()
    {
        return view('mercadopago.test');
    }

    /**
     * Crear un customer
     */
    public function createCustomer(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'first_name' => 'required|string',
                'last_name' => 'required|string',
                'phone' => 'nullable|string',
                'identification_type' => 'nullable|string',
                'identification_number' => 'nullable|string'
            ]);

            $client = new CustomerClient();

            $customerData = [
                "email" => $request->email,
                "first_name" => $request->first_name,
                "last_name" => $request->last_name,
            ];

            if ($request->phone) {
                $customerData['phone'] = [
                    "area_code" => "",
                    "number" => $request->phone
                ];
            }

            if ($request->identification_type && $request->identification_number) {
                $customerData['identification'] = [
                    "type" => $request->identification_type,
                    "number" => $request->identification_number
                ];
            }

            $customer = $client->create($customerData);

            \Log::info('Customer created:', ['customer_id' => $customer->id]);

            return response()->json([
                'success' => true,
                'customer_id' => $customer->id,
                'email' => $customer->email
            ]);

        } catch (\Exception $e) {
            \Log::error('Create Customer Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Guardar tarjeta del customer
     */
    public function saveCard(Request $request)
    {
        try {
            $request->validate([
                'customer_id' => 'required|string',
                'token' => 'required|string'
            ]);

            $client = new CustomerCardClient();

            $card = $client->create($request->customer_id, [
                "token" => $request->token
            ]);

            \Log::info('Card saved:', [
                'customer_id' => $request->customer_id,
                'card_id' => $card->id,
                'last_four_digits' => $card->last_four_digits
            ]);

            return response()->json([
                'success' => true,
                'card_id' => $card->id,
                'last_four_digits' => $card->last_four_digits,
                'payment_method' => $card->payment_method->name,
                'expiration_month' => $card->expiration_month,
                'expiration_year' => $card->expiration_year
            ]);

        } catch (\Exception $e) {
            \Log::error('Save Card Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Procesar pago con tarjeta guardada
     */
    public function processPayment(Request $request)
    {
        try {
            $request->validate([
                'customer_id' => 'required|string',
                'card_id' => 'required|string',
                'amount' => 'required|numeric',
                'description' => 'nullable|string'
            ]);

            $client = new PaymentClient();

            $payment = $client->create([
                "transaction_amount" => (float) $request->amount,
                "description" => $request->description ?? "Pago de suscripción",
                "payment_method_id" => "master", // Ajustar según la tarjeta
                "payer" => [
                    "id" => $request->customer_id,
                    "type" => "customer"
                ],
                "token" => $request->card_id
            ]);

            \Log::info('Payment processed:', [
                'payment_id' => $payment->id,
                'status' => $payment->status,
                'amount' => $payment->transaction_amount
            ]);

            return response()->json([
                'success' => true,
                'payment_id' => $payment->id,
                'status' => $payment->status,
                'status_detail' => $payment->status_detail,
                'amount' => $payment->transaction_amount
            ]);

        } catch (\Exception $e) {
            \Log::error('Process Payment Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }

    /**
     * Listar tarjetas de un customer
     */
    public function getCustomerCards($customerId)
    {
        try {
            $client = new CustomerCardClient();
            $cards = $client->list($customerId);

            return response()->json([
                'success' => true,
                'cards' => array_map(function($card) {
                    return [
                        'id' => $card->id,
                        'last_four_digits' => $card->last_four_digits,
                        'payment_method' => $card->payment_method->name ?? 'N/A',
                        'expiration_month' => $card->expiration_month,
                        'expiration_year' => $card->expiration_year
                    ];
                }, iterator_to_array($cards))
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Webhook para notificaciones de Mercado Pago
     */
    public function webhook(Request $request)
    {
        \Log::info('Mercado Pago Webhook:', $request->all());

        try {
            $type = $request->input('type');
            $dataId = $request->input('data.id');

            if ($type === 'payment') {
                $client = new PaymentClient();
                $payment = $client->get($dataId);

                \Log::info('Payment Info:', [
                    'id' => $payment->id,
                    'status' => $payment->status,
                    'status_detail' => $payment->status_detail,
                    'external_reference' => $payment->external_reference,
                    'transaction_amount' => $payment->transaction_amount
                ]);

                switch ($payment->status) {
                    case 'approved':
                        // Pago aprobado
                        break;
                    case 'pending':
                        // Pago pendiente
                        break;
                    case 'rejected':
                        // Pago rechazado
                        break;
                }
            }

            return response()->json(['status' => 'ok'], 200);

        } catch (\Exception $e) {
            \Log::error('Webhook Error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Consultar un customer
     */
    public function getCustomer($customerId)
    {
        try {
            $client = new CustomerClient();
            $customer = $client->get($customerId);

            return response()->json([
                'success' => true,
                'customer' => [
                    'id' => $customer->id,
                    'email' => $customer->email,
                    'first_name' => $customer->first_name,
                    'last_name' => $customer->last_name
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
