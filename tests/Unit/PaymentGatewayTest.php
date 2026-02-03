<?php

// tests/Unit/PaymentGatewayTest.php
namespace Tests\Unit;

use App\Models\Order;
use App\Services\Payments\CreditCardGateway;
use App\Services\Payments\PayPalGateway;
use PHPUnit\Framework\TestCase;

class PaymentGatewayTest extends TestCase
{
    public function test_credit_card_gateway_returns_success()
    {
        $order = $this->createMock(Order::class);
        $gateway = new CreditCardGateway();

        $result = $gateway->pay($order, []);

        $this->assertArrayHasKey('payment_id', $result);
        $this->assertEquals('successful', $result['status']);
        $this->assertEquals('Credit card processed successfully', $result['response']['message']);
    }

    public function test_paypal_gateway_returns_success()
    {
        $order = $this->createMock(Order::class);
        $gateway = new PayPalGateway();

        $result = $gateway->pay($order, []);

        $this->assertArrayHasKey('payment_id', $result);
        $this->assertEquals('successful', $result['status']);
        $this->assertEquals('PayPal processed successfully', $result['response']['message']);
    }
}
