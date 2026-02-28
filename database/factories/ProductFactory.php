<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'          => fake()->words(3, true),
            'sku'           => strtoupper(fake()->unique()->bothify('SKU-####')),
            'unit'          => fake()->randomElement(['pcs', 'kg', 'box', 'liter']),
            'current_stock' => 0,
            'min_stock'     => fake()->numberBetween(5, 20),
        ];
    }
}
