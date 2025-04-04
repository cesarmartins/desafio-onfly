<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use App\Models\TravelRequest;
use PHPUnit\Framework\Attributes\Test;

class TravelRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate()
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);
        return $user;
    }

    #[Test]
    public function it_creates_a_travel_request()
    {
        $this->authenticate();

        $data = [
            'requester_name' => 'César',
            'destination' => 'Berlim',
            'departure_date' => '2025-05-01',
            'return_date' => '2025-05-10',
        ];

        $response = $this->postJson('/api/travel-requests', $data);

        $response->assertStatus(201)
            ->assertJsonFragment(['destination' => 'Berlim']);
    }

    #[Test]
    public function it_lists_travel_requests()
    {
        $user = $this->authenticate();

        TravelRequest::factory()->create([
            'user_id' => $user->id,
            'requester_name' => 'César',
            'destination' => 'Lisboa',
            'departure_date' => '2025-06-01',
            'return_date' => '2025-06-10',
        ]);

        $response = $this->getJson('/api/travel-requests');

        $response->assertStatus(200)
            ->assertJsonFragment(['destination' => 'Lisboa']);
    }

    #[Test]
    public function it_rejects_unauthenticated_access()
    {
        $response = $this->getJson('/api/travel-requests');
        $response->assertStatus(401);
    }
}
