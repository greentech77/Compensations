<?php

namespace Database\Seeders;

use App\Models\Compenzation;
use App\Models\CompenzationEntity;
use App\Models\Entity;
use App\Models\ImplementationAgreement;
use App\Models\RealizationAgreement;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Vsaka kompenzacija dobi:
     *  - 2x CompenzationEntity  (num=1 iniciator, num=2 partner)
     *  - 1x ImplementationAgreement
     *  - 1x RealizationAgreement
     */
    public function run(): void
    {
        $this->call([UserSeeder::class]);

        // Ustvari bazen strank, ki se delijo med kompenzacijami
        $entities = Entity::factory(40)->create();

        // Ustvari kompenzacije in za vsako vse potrebne zapise
        Compenzation::factory(20)->create()->each(function (Compenzation $compenzation) use ($entities) {

            // Iniciator (num=1) in partner (num=2) — obe stranki različni
            [$initiator, $partner] = $entities->random(2)->values();

            CompenzationEntity::create([
                'id_compenzation' => $compenzation->id,
                'id_entity'       => $initiator->id,
                'num'             => 1,
            ]);

            CompenzationEntity::create([
                'id_compenzation' => $compenzation->id,
                'id_entity'       => $partner->id,
                'num'             => 2,
            ]);

            // Pogodba o izvedbi
            ImplementationAgreement::factory()->create([
                'id_compenzation' => $compenzation->id,
                'with_ddv'        => $compenzation->with_ddv,
            ]);

            // Pogodba o unovčenju
            RealizationAgreement::factory()->create([
                'id_compenzation' => $compenzation->id,
            ]);
        });
    }
}
