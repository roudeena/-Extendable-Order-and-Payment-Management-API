<?php

// tests/Feature/AuthTest.php
namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_user()
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

       $response->assertStatus(201)
         ->assertJson([
             'message' => 'User registered successfully',
         ]);
    }

    public function test_login_user()
    {
        $user = User::factory()->create([
            'password' => bcrypt($password = 'password123')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'access_token',
                     'token_type',
                     'expires_in'
                 ]);
    }

    public function test_get_me_requires_auth()
    {
        $response = $this->getJson('/api/me');
        $response->assertStatus(401);
    }
}
