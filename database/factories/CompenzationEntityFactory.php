<?php

namespace Database\Factories;
use App\Models\CompenzationEntity;
use App\Models\Compenzation;
use App\Models\Entity;

use Illuminate\Database\Eloquent\Factories\Factory;

class CompenzationEntityFactory extends Factory
{
    protected $model = CompenzationEntity::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'id_compenzation' => Compenzation::factory(), // Generate or reference a related Compenzation
            'id_entity' => Entity::factory(),            // Generate or reference a related Entity
            'num' => $this->faker->numberBetween(1, 100), // Random number or null
        ];
    }
}
