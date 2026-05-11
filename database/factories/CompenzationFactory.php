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
        $year = (int) date('Y');

        // Naključni datum znotraj trenutnega leta
        $startOfYear = \Carbon\Carbon::createFromDate($year, 1, 1);
        $today       = \Carbon\Carbon::today();
        $date        = $this->faker->dateTimeBetween($startOfYear, $today);

        return [
            'name'          => 'Kompenzacija-' . str_pad($counter++, 4, '0', STR_PAD_LEFT) . '/' . $year,
            'year'          => $year,
            'amount'        => $this->faker->randomFloat(4, 1000, 10000),
            'vat'           => 22,
            'date'          => $date,
            'date_finished' => $this->faker->dateTimeBetween($date, $today),
            'date_payed'    => $this->faker->dateTimeBetween($date, $today),
            'storno'        => false,
            'finished'      => false,
            'with_ddv'      => $this->faker->boolean(),
        ];
    }
}
