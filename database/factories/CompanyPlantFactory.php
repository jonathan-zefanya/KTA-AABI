<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\CompanyPlant;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyPlantFactory extends Factory
{
    protected $model = CompanyPlant::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'type' => fake()->randomElement(['AMP', 'CBP']),
            'address' => fake()->address(),
        ];
    }

    public function amp(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'AMP',
        ]);
    }

    public function cbp(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'CBP',
        ]);
    }
}
