<?php
// tests/Feature/OrderTest.php
namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_order()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        $response = $this->postJson('/api/orders', [
            'items' => [
                ['product_name' => 'Product A', 'quantity' => 2, 'price' => 50],
                ['product_name' => 'Product B', 'quantity' => 1, 'price' => 100],
            ]
        ]);

       $response->assertStatus(201)
         ->assertJsonStructure([
             'message',
             'order' => [ 
                'id',
                'user_id',
                'status',
                'items'
            ]
         ]);
    }

    public function test_list_orders()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'api');

        Order::factory()->count(3)->create(['user_id' => $user->id]);

        $response = $this->getJson('/api/orders');

        $response->assertStatus(200)
                 ->assertJsonCount(13);
    }
}