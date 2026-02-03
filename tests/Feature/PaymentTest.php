<?php
// tests/Feature/PaymentTest.php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Services\Payments\PaymentGatewayFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_process_credit_card_payment()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'confirmed'
        ]);

        $response = $this->postJson('/api/payments', [
            'order_id' => $order->id,
            'payment_method' => 'credit_card'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                    'message' => 'Payment processed'
                 ]);
    }

    public function test_process_paypal_payment()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'confirmed'
        ]);

        $response = $this->postJson('/api/payments', [
            'order_id' => $order->id,
            'payment_method' => 'paypal'
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'message' => 'Payment processed'
                 ]);
    }

    public function test_payment_fails_if_order_is_pending()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        // Create an order with 'pending' status instead of 'confirmed'
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'pending' 
        ]);

        $response = $this->postJson('/api/payments', [
            'order_id' => $order->id,
            'payment_method' => 'credit_card'
        ]);

        // Assert 403 Forbidden and the specific error message
        $response->assertStatus(403)
                 ->assertJson([
                     'error' => 'Payments can only be processed for confirmed orders'
                 ]);
    }

    public function test_payment_fails_if_order_is_already_shipped()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        // Test with another non-confirmed status
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'status' => 'paid'
        ]);

        $response = $this->postJson('/api/payments', [
            'order_id' => $order->id,
            'payment_method' => 'paypal'
        ]);

        $response->assertStatus(403)
                 ->assertJson([
                     'error' => 'Order has already been paid'
                 ]);
    }

    public function test_payment_gateway_factory_throws_exception_for_invalid_method()
    {
        $this->expectException(\Exception::class);
        PaymentGatewayFactory::make('invalid_method');
    }
}