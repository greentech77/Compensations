<?php

namespace Database\Factories;
use App\Models\Compenzation;

use Illuminate\Database\Eloquent\Factories\Factory;

class ImplementationAgreementFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        $discount = $this->faker->randomFloat(2, 1, 10);
        $amount   = $this->faker->randomFloat(2, 1000, 10000);

        return [
            'id_compenzation'     => \App\Models\Compenzation::factory(),
            'discount'            => $discount,
            'with_ddv'            => $this->faker->boolean(),
            'discount_amount'     => round($amount * $discount / 100, 2),
            'discount_ddv_amount' => round($amount * $discount / 100 * 1.22, 2),
            'net_amount'          => round($amount - ($amount * $discount / 100), 2),
            'transfer_amount'     => round($amount - ($amount * $discount / 100), 2),
        ];
    }
}
