<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_returns_token_on_successful_login()
    {
        $user = User::factory()->create([
            'email' => 'cesar@onfly.dev',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'cesar@onfly.dev',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['token']);
    }

    #[Test]
    public function it_rejects_invalid_credentials()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'fake@user.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
            ->assertJson(['message' => 'Credenciais inválidas']);
    }
}
