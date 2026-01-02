<?php

namespace App\Http\Controllers;

use Stripe\Stripe;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;

class PaymentController extends Controller
{
   public function createCheckoutSession(Request $request)
{
    Stripe::setApiKey(env('STRIPE_SECRET'));

    $session = Session::create([
        'payment_method_types' => ['card'],
        'line_items' => [[
            'price_data' => [
                'currency' => 'usd',
                'product_data' => [
                    'name' => $request->name,
                    'description' => $request->description,
                ],
                'unit_amount' => $request->amount,
            ],
            'quantity' => 1,
        ]],
        'mode' => 'payment',
        'success_url' => 'http://localhost:8080/success',
        'cancel_url' => 'http://localhost:8080/cancel',
    ]);

    return response()->json(['url' => $session->url]);
}

    
}
