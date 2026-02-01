<?php

namespace App\Services\Payments;

class PaymentGatewayFactory
{
    public static function make(string $method): PaymentGatewayInterface
    {
        return match(strtolower($method)) {
            'credit_card' => new CreditCardGateway(),
            'paypal' => new PayPalGateway(),
            default => throw new \Exception("Payment gateway [$method] not supported"),
        };
    }
}
