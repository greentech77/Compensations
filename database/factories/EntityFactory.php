<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class EntityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'company_name'      => $this->faker->company(),
            'name'              => $this->faker->name(),
            'lastname'          => $this->faker->name(),
            'address'           => $this->faker->streetAddress(),
            'post_num'          => $this->faker->numerify('####'),
            'post_town'         => $this->faker->city(),
            'vat_num'           => $this->faker->numerify('########'),
            'registration_num'  => $this->faker->numerify('##########'),
            'bank_account'      => $this->faker->bankAccountNumber(),
            'bank_bic'          => $this->faker->numerify('BIC########'),
            'bank_name'         => $this->faker->name(),
            'email'             => $this->faker->unique()->safeEmail(),
            'fax'               => $this->faker->numerify('########'),
            'mobile'            => $this->faker->numerify('########'),
            'phone'             => $this->faker->numerify('########'),
            'show_email'        => true,
            'show_fax'          => true
        ];
    }
}
