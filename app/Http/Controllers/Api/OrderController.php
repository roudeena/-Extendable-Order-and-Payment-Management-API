<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\OrderItem;

class OrderController extends Controller
{
    // List all orders (paginated)
    public function index(Request $request)
    {
        $query = Order::where('user_id', Auth::id());

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // IMPORTANT: eager load items
        $orders = $query->with('items')->paginate(10);

        return response()->json($orders);
    }


    // Create a new order
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_name' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ]);

        $total = collect($request->items)->sum(fn($i) => $i['quantity'] * $i['price']);

        $order = Order::create([
            'user_id' => Auth::id(),
            'total_amount' => $total,
            'status' => 'pending',
        ]);

        foreach ($request->items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_name' => $item['product_name'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        return response()->json(['message' => 'Order created', 'order' => $order], 201);
    }

    // Show single order
    public function show(Order $order)
    {
        $this->authorizeOrder($order);

        return response()->json($order->load('items'));
    }

    // Update order (only if pending)
    public function update(Request $request, Order $order)
    {
        $this->authorizeOrder($order);

        if ($order->status !== 'pending') {
            return response()->json(['error' => 'Cannot update non-pending order'], 403);
        }

        $request->validate([
            'items' => 'sometimes|array|min:1',
            'items.*.product_name' => 'required_with:items|string',
            'items.*.quantity' => 'required_with:items|integer|min:1',
            'items.*.price' => 'required_with:items|numeric|min:0',
            'status' => 'sometimes|in:pending,confirmed,cancelled',
        ]);

        if ($request->has('items')) {
            $order->items()->delete();
             // Create new items
            foreach ($request->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_name' => $item['product_name'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                ]);
            }
            // Recalculate total
            $order->total_amount = collect($request->items)->sum(fn($i) => $i['quantity'] * $i['price']);
        }

        if ($request->has('status')) {
            $order->status = $request->status;
        }

        $order->save();

        return response()->json(['message' => 'Order updated', 'order' => $order->load('items')]);
    }

    // Delete order (only if no payments)
    public function destroy(Order $order)
    {
        $this->authorizeOrder($order);

        if ($order->payments()->count() > 0) {
            return response()->json(['error' => 'Cannot delete order with payments'], 403);
        }

        $order->delete();

        return response()->json(['message' => 'Order deleted']);
    }

    private function authorizeOrder(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }
    }
}
