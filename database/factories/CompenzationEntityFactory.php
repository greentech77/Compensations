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
    public function definition(): array
    {
        return [
            'id_compenzation' => Compenzation::factory(),
            'id_entity'       => Entity::factory(),
            'num'             => $this->faker->randomElement([1, 2]),
        ];
    }
}
