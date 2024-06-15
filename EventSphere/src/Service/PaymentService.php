<?php

namespace App\Service;

use Stripe\Stripe;
use Stripe\PaymentIntent;

class PaymentService
{
    private $stripeSecretKey;

    public function __construct(string $stripeSecretKey)
    {
        $this->stripeSecretKey = $stripeSecretKey;
    }

    public function processPayment($user, $event, $paymentData)
    {
        Stripe::setApiKey($this->stripeSecretKey);

        $paymentIntent = PaymentIntent::create([
            'amount' => $event->getCost() * 100, // amount in cents
            'currency' => 'usd',
            'payment_method_types' => ['card'],
            'metadata' => [
                'user_id' => $user->getId(),
                'event_id' => $event->getId(),
            ],
        ]);

        $paymentResult = $this->confirmPayment($paymentIntent, $paymentData);

        return $paymentResult;
    }

    private function confirmPayment($paymentIntent, $paymentData)
    {
        // We need to confirm the payment with the payment details here
        // (need to use Stripe.js for frontend payment processing)

        return ['status' => 'succeeded'];
    }
}
