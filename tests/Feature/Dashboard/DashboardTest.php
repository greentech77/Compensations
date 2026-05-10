<?php

namespace Tests\Feature\Dashboard;

use App\Models\Bill;
use App\Models\Compenzation;
use App\Models\CompenzationEntity;
use App\Models\Entity;
use App\Models\ImplementationAgreement;
use App\Models\RealizationAgreement;
use App\Models\User;
use App\Services\Dashboard\DashboardService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

/**
 * Smoke + structural tests for the dashboard summary payload and route.
 *
 * The dashboard is read-only and aggregates from many tables, so we focus on:
 *   - the route renders the correct Inertia component for an authenticated user,
 *   - all the keys the Vue page consumes are present,
 *   - aggregates (count, sum, top entities) reflect the seeded data.
 */
class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private function seedSmallDataset(): array
    {
        $now = Carbon::now();

        $entityA = Entity::factory()->create([
            'company_name' => 'STRANKA A D.O.O.',
        ]);
        $entityB = Entity::factory()->create([
            'company_name' => 'STRANKA B D.O.O.',
        ]);

        // Finished compenzation, current year, large amount → drives KPIs.
        $finished = Compenzation::factory()->create([
            'date' => $now->copy()->subDays(5)->toDateString(),
            'date_finished' => $now->copy()->subDays(2)->toDateString(),
            'date_payed' => $now->copy()->subDays(2)->toDateString(),
            'amount' => 5000.00,
            'year' => (int) $now->format('Y'),
            'finished' => true,
            'with_ddv' => false,
        ]);
        ImplementationAgreement::create([
            'id_compenzation' => $finished->id,
            'discount' => 5,
            'with_ddv' => false,
            'discount_amount' => 250,
            'discount_ddv_amount' => 0,
            'net_amount' => 5000,
            'transfer_amount' => 4750,
        ]);
        RealizationAgreement::create([
            'id_compenzation' => $finished->id,
            'commission' => 7,
            'commission_amount' => 350,
            'commission_ddv_amount' => 77,
            'transfer_amount' => 427,
        ]);
        CompenzationEntity::create([
            'id_compenzation' => $finished->id,
            'id_entity' => $entityA->id,
            'num' => 1,
        ]);
        CompenzationEntity::create([
            'id_compenzation' => $finished->id,
            'id_entity' => $entityB->id,
            'num' => 2,
        ]);

        // Unfinished compenzation, smaller amount.
        $unfinished = Compenzation::factory()->create([
            'date' => $now->copy()->subDays(10)->toDateString(),
            'date_finished' => $now->copy()->addDays(20)->toDateString(),
            'date_payed' => $now->copy()->subDays(10)->toDateString(),
            'amount' => 1500.00,
            'year' => (int) $now->format('Y'),
            'finished' => false,
            'with_ddv' => false,
        ]);
        ImplementationAgreement::create([
            'id_compenzation' => $unfinished->id,
            'discount' => 0,
            'with_ddv' => false,
            'discount_amount' => 0,
            'discount_ddv_amount' => 0,
            'net_amount' => 1500,
            'transfer_amount' => 1500,
        ]);
        RealizationAgreement::create([
            'id_compenzation' => $unfinished->id,
            'commission' => 0,
            'commission_amount' => 0,
            'commission_ddv_amount' => 0,
            'transfer_amount' => 0,
        ]);
        CompenzationEntity::create([
            'id_compenzation' => $unfinished->id,
            'id_entity' => $entityA->id,
            'num' => 1,
        ]);

        Bill::create([
            'id_entity' => $entityA->id,
            'amount' => 950.00,
            'year' => (int) $now->format('Y'),
            'date' => $now->copy()->subDays(1)->toDateString(),
        ]);

        return [
            'entityA' => $entityA,
            'entityB' => $entityB,
            'finished' => $finished,
            'unfinished' => $unfinished,
        ];
    }

    public function test_dashboard_route_requires_authentication(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect();
        $response->assertStatus(302);
    }

    public function test_authenticated_user_sees_dashboard_inertia_component(): void
    {
        $this->seedSmallDataset();

        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertOk();
        $response->assertInertia(
            fn (Assert $page) => $page
                ->component('Dashboard', false)
                ->has('summary', fn (Assert $summary) => $summary
                    ->has('kpis')
                    ->has('monthly', 12)
                    ->has('recent_compenzations', 2)
                    ->has('recent_bills', 1)
                    ->has('top_entities', 2)
                    ->has('unfinished', 1)
                    ->etc()
                )
        );
    }

    public function test_summary_aggregates_match_seeded_data(): void
    {
        $this->seedSmallDataset();

        /** @var DashboardService $service */
        $service = $this->app->make(DashboardService::class);
        $summary = $service->summary();

        $this->assertSame(2, $summary['kpis']['entities_count']);
        $this->assertSame(1, $summary['kpis']['compenzations_active']);
        $this->assertSame(1, $summary['kpis']['compenzations_finished_year']);
        $this->assertEqualsWithDelta(6500.00, $summary['kpis']['total_amount_year'], 0.01);
        $this->assertSame(1, $summary['kpis']['bills_year_count']);
        $this->assertEqualsWithDelta(950.00, $summary['kpis']['bills_year_amount'], 0.01);

        // Monthly window length is 12 by default and entries are well-shaped.
        $this->assertCount(12, $summary['monthly']);
        $first = $summary['monthly'][0];
        $this->assertArrayHasKey('label', $first);
        $this->assertArrayHasKey('ym', $first);
        $this->assertArrayHasKey('amount', $first);
        $this->assertArrayHasKey('count', $first);
        $this->assertArrayHasKey('bills_count', $first);

        // Recent lists / top / unfinished have the right cardinality.
        $this->assertCount(2, $summary['recent_compenzations']);
        $this->assertCount(1, $summary['recent_bills']);
        $this->assertCount(2, $summary['top_entities']);
        $this->assertCount(1, $summary['unfinished']);

        // Top entity ranking: A is in 2 compenzations (5000 + 1500), B in 1 (5000).
        $this->assertSame('STRANKA A D.O.O.', $summary['top_entities'][0]['company_name']);
        $this->assertSame(2, $summary['top_entities'][0]['compenzations_count']);
        $this->assertEqualsWithDelta(6500.00, $summary['top_entities'][0]['total_amount'], 0.01);

        $this->assertSame('STRANKA B D.O.O.', $summary['top_entities'][1]['company_name']);
        $this->assertSame(1, $summary['top_entities'][1]['compenzations_count']);
        $this->assertEqualsWithDelta(5000.00, $summary['top_entities'][1]['total_amount'], 0.01);
    }

    public function test_monthly_window_is_zero_padded_when_db_empty(): void
    {
        /** @var DashboardService $service */
        $service = $this->app->make(DashboardService::class);
        $summary = $service->summary(6);

        $this->assertCount(6, $summary['monthly']);
        foreach ($summary['monthly'] as $row) {
            $this->assertSame(0.0, $row['amount']);
            $this->assertSame(0, $row['count']);
            $this->assertSame(0.0, $row['bills_amount']);
            $this->assertSame(0, $row['bills_count']);
        }

        $this->assertSame(0, $summary['kpis']['entities_count']);
        $this->assertSame(0, $summary['kpis']['compenzations_active']);
        $this->assertNull($summary['kpis']['avg_percent_diff_year']);
    }
}
