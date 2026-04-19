<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PostNumber;

class PostNumberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Basic Slovenian postal codes - you can expand this with actual data from legacy system
        $postNumbers = [
            ['code' => 1000, 'postname' => 'Ljubljana'],
            ['code' => 2000, 'postname' => 'Maribor'],
            ['code' => 3000, 'postname' => 'Celje'],
            ['code' => 4000, 'postname' => 'Kranj'],
            ['code' => 5000, 'postname' => 'Nova Gorica'],
            ['code' => 6000, 'postname' => 'Koper'],
            ['code' => 8000, 'postname' => 'Novo mesto'],
        ];

        foreach ($postNumbers as $postNumber) {
            PostNumber::updateOrCreate(
                ['code' => $postNumber['code']],
                ['postname' => $postNumber['postname']]
            );
        }

        $this->command->info('Post numbers seeded successfully!');
    }
}
