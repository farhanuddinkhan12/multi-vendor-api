<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PayPal\Api\{Payer, Item, ItemList, Amount, Transaction, RedirectUrls, Payment, PaymentExecution};
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;

class PayPalController extends Controller
{
    private $apiContext;
    public function __construct()
    {
        $this->apiContext = new ApiContext(
            new OAuthTokenCredential(
                env('PAYPAL_CLIENT_ID'),
                env('PAYPAL_SECRET')
            )
        );
        $this->apiContext->setConfig([
            'mode' => env('PAYPAL_MODE', 'sandbox'),
        ]);
    }
    public function createPayment(Request $request) {
        $payer = new Payer();
        $payer->setPaymentMethod('paypal');
        $item = new Item();
        $item->setName($request->name)
        ->setCurrency('USD')
        ->setQuantity(1)
        ->setPrice($request->amount);

        $itemList = new ItemList();
        $itemList->setItems([$item]);

        $amount = new Amount();
        $amount->setCurrency('USD')
        ->setTotal($request->amount);

        $transaction = new Transaction();
        $transaction->setAmount($amount)
        ->setItemList($itemList)
        ->setDescription($request->description);

        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl(url('/api/paypal/success'))
        ->setCancelUrl(url('/api/paypal/cancel'));

        $payment = new Payment();
        $payment->setIntent('sale')
        ->setPayer($payer)
        ->setRedirectUrls($redirectUrls)
        ->setTransactions([$transaction]);

        try {
            $payment->create($this->apiContext);
            return response()->json(['approval_url' => $payment->getApprovalLink()]);
        } catch(\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()]);
        }
    }

    public function success(Request $request) {
        return redirect('http://localhost:8080/paypal-success');
    }
    public function cancel() {
        return redirect('http://localhost:8080/paypal-cancel');
    }
}
