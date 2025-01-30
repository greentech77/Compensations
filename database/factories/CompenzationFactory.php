<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CompenzationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        static $counter = 1;
        $year = date('Y');
        return [
            'name'              => $this->faker->numerify('Kompenzacija-'. str_pad($counter++, 4, '0', STR_PAD_LEFT) .'/'.$year),
            'year'              => 2024,
            'amount'            => $this->faker->randomFloat(4, 1000, 10000),
            'vat'               => 22,
            'date'              => $this->faker->date(),
            'date_finished'     => $this->faker->date(),
            'date_payed'        => $this->faker->date(),
            'storno'            => false,
            'finished'          => false,
            'with_ddv'          => $this->faker->boolean(),
        ];
    }
}
