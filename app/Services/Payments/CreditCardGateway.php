<?php

namespace App\Services\Payments;

use App\Models\Order;

class CreditCardGateway implements PaymentGatewayInterface
{
    public function pay(Order $order, array $data): array
    {
        return [
            'payment_id' => 'CC' . time(),
            'status' => 'successful',
            'response' => [
                'message' => 'Credit card processed successfully',
            ],
        ];
    }
}
