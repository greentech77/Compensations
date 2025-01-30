<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        //\App\Models\Entity::factory(40)->create();
        \App\Models\Compenzation::factory(20)->create();
        \App\Models\CompenzationEntity::factory(20)->create();
        \App\Models\ImplementationAgreement::factory(20)->create();
        \App\Models\RealizationAgreement::factory(20)->create();
        $this->call([
            UserSeeder::class,
            EntitySeeder::class,
        ]);
    }
}
