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
    public function definition()
    {
        return [
                'id_compenzation'  => function () {
                    // Fetch the first 20 compenzation IDs from the database
                    $compenzationIds = \App\Models\Compenzation::limit(20)->pluck('id');
                    
                    // Randomly select one of these IDs
                    return $compenzationIds->random();
                },
                'commission'            =>  $this->faker->randomFloat(2, 1, 10),
                'commission_amount'     =>  $this->faker->randomFloat(2, 10, 1000),
                'commission_ddv_amount' =>  $this->faker->randomFloat(2, 10, 1000),
                'transfer_amount'       =>  $this->faker->randomFloat(2, 10, 1000)
            ];     
    }
}
