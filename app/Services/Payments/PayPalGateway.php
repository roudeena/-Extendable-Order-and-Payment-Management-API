<?php

namespace App\Services\Payments;

use App\Models\Order;

class PayPalGateway implements PaymentGatewayInterface
{
    public function pay(Order $order, array $data): array
    {
        return [
            'payment_id' => 'PP' . time(),
            'status' => 'successful',
            'response' => [
                'message' => 'PayPal processed successfully',
            ],
        ];
    }
}
