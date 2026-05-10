<?php

namespace Database\Factories;
use App\Models\Compenzation;

use Illuminate\Database\Eloquent\Factories\Factory;

class RealizationAgreementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $commission = $this->faker->randomFloat(2, 1, 10);
        $amount     = $this->faker->randomFloat(2, 1000, 10000);

        return [
            'id_compenzation'     => \App\Models\Compenzation::factory(),
            'commission'          => $commission,
            'commission_amount'   => round($amount * $commission / 100, 2),
            'commission_ddv_amount' => round($amount * $commission / 100 * 1.22, 2),
            'transfer_amount'     => round($amount - ($amount * $commission / 100), 2),
        ];
    }
}
