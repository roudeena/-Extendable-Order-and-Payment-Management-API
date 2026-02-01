<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Services\Payments\PaymentGatewayFactory;

class PaymentController extends Controller
{
    // Process payment
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'payment_method' => 'required|string',
        ]);

        $order = Order::findOrFail($request->order_id);

        // Check if already paid
        if ($order->status == 'paid') {
            return response()->json(['error' => 'Order has already been paid'], 403);
        }

        // Business rule: only confirmed orders
        if ($order->status !== 'confirmed') {
            return response()->json(['error' => 'Payments can only be processed for confirmed orders'], 403);
        }

        try {
            $gateway = PaymentGatewayFactory::make($request->payment_method);
            $result = $gateway->pay($order, $request->all());

            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_id' => $result['payment_id'],
                'status' => $result['status'],
                'payment_method' => $request->payment_method,
                'gateway_response' => $result['response'],
            ]);

            // Update order status if payment successful
            if ($result['status'] === 'successful') {
                $order->update(['status' => 'paid']);
            }

            return response()->json(['message' => 'Payment processed', 'payment' => $payment]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    // List all payments
    public function index()
    {
        $payments = Payment::paginate(10);
        return response()->json($payments);
    }

    // Payments for specific order
    public function show(Order $order)
    {
        return response()->json($order->payments);
    }
}
