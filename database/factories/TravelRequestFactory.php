<?php

namespace Database\Factories;

use App\Models\TravelRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TravelRequestFactory extends Factory
{
    protected $model = TravelRequest::class;

    public function definition(): array
    {
        return [
            'id' => $this->faker->uuid(),
            'requester_name' => $this->faker->name(),
            'destination' => $this->faker->city(),
            'departure_date' => now()->addDays(5)->toDateString(),
            'return_date' => now()->addDays(10)->toDateString(),
            'status' => 'solicitado',
            'user_id' => User::factory(), // relaciona corretamente
        ];
    }
}
