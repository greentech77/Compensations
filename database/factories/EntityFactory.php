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
        // Common Slovenian company suffixes
        $companySuffixes = ['D.O.O.', 'D.D.', 'S.P.', 'S.R.O.', 'D.N.O.'];
        $companyTypes = ['TRGOVINA', 'PRODAJA', 'STORITVE', 'PROIZVODNJA', 'INŽENIRING', 'KONSULTING'];
        $companyNames = ['TEHNIKA', 'SISTEMI', 'SOLUTIONS', 'SERVIS', 'TRADE', 'GROUP', 'HOLDING'];
        
        $companyName = $this->faker->randomElement($companyNames) . ' ' . 
                      $this->faker->randomElement($companyTypes) . ' ' . 
                      $this->faker->randomElement($companySuffixes);
        
        // Slovenian street names
        $streetTypes = ['Ulica', 'Cesta', 'Trg', 'Pot'];
        $slovenianLastNames = ['Novak', 'Horvat', 'Kovačič', 'Krajnc', 'Zupančič', 'Potočnik', 'Kos', 'Vidmar', 'Golob', 'Turk'];
        $streetName = $this->faker->randomElement($streetTypes) . ' ' . 
                     $this->faker->randomElement($slovenianLastNames) . ' ' . 
                     $this->faker->numberBetween(1, 200);
        
        // Slovenian cities
        $slovenianCities = [
            'Ljubljana', 'Maribor', 'Celje', 'Kranj', 'Velenje', 'Koper', 'Novo Mesto',
            'Ptuj', 'Trbovlje', 'Kamnik', 'Nova Gorica', 'Jesenice', 'Murska Sobota',
            'Domžale', 'Škofja Loka', 'Izola', 'Kočevje', 'Postojna', 'Logatec', 'Vrhnika'
        ];
        
        // Slovenian postal codes (4 digits, typically 1000-9999)
        $postNum = $this->faker->numberBetween(1000, 9999);
        
        // Slovenian first names
        $slovenianFirstNames = ['Janez', 'Marija', 'Franc', 'Ana', 'Anton', 'Maja', 'Marko', 'Irena', 'Peter', 'Nina', 'Luka', 'Sara', 'Tomaž', 'Katja', 'Andrej', 'Mojca'];
        $slovenianLastNamesForNames = ['Novak', 'Horvat', 'Kovačič', 'Krajnc', 'Zupančič', 'Potočnik', 'Kos', 'Vidmar', 'Golob', 'Turk', 'Petek', 'Koren', 'Zupan', 'Hribar', 'Kovač'];
        
        return [
            'company_name'      => $companyName,
            'name'              => $this->faker->randomElement($slovenianFirstNames),
            'lastname'          => $this->faker->randomElement($slovenianLastNamesForNames),
            'address'           => $streetName,
            'post_num'          => $postNum,
            'post_town'         => $this->faker->randomElement($slovenianCities),
            'vat_num'           => 'SI' . $this->faker->numerify('########'),
            'registration_num'  => $this->faker->numerify('##########'),
            'bank_account'      => 'SI56' . $this->faker->numerify('####') . $this->faker->numerify('####') . $this->faker->numerify('####'),
            'bank_bic'          => $this->faker->randomElement(['LJUB', 'MARI', 'KOPE']) . 'SI2X',
            'bank_name'         => $this->faker->randomElement(['Nova Ljubljanska banka', 'Banka Slovenije', 'Unicredit Banka', 'SKB Banka', 'Abanka']) . ' D.D.',
            'email'             => $this->faker->unique()->safeEmail(),
            'fax'               => $this->faker->numerify('01/#######'),
            'mobile'            => $this->faker->numerify('0#########'),
            'phone'             => $this->faker->numerify('01/#######'),
            'show_email'        => true,
            'show_fax'          => true
        ];
    }
}
