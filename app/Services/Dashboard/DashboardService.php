<?php

namespace App\Services\Dashboard;

use App\Models\Bill;
use App\Models\Compenzation;
use App\Models\Entity;
use App\Services\Compenzations\CompenzationStatsService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Aggregates read-only metrics for the operations dashboard.
 *
 * No user input is consumed; all queries use Eloquent / parameterized
 * Query Builder calls, so the surface area is safe by construction.
 */
class DashboardService
{
    /**
     * Slovenian month abbreviations used in chart x-axis labels.
     * Indexed 1..12 to match Carbon::month numbering.
     */
    private const SL_MONTH_SHORT = [
        1 => 'jan',
        2 => 'feb',
        3 => 'mar',
        4 => 'apr',
        5 => 'maj',
        6 => 'jun',
        7 => 'jul',
        8 => 'avg',
        9 => 'sep',
        10 => 'okt',
        11 => 'nov',
        12 => 'dec',
    ];

    public function __construct(
        private readonly CompenzationStatsService $stats,
    ) {}

    /**
     * Build the full dashboard payload.
     *
     * @param  int  $months  Number of trailing months to include in the trend chart.
     * @return array{
     *     kpis: array<string, int|float|null>,
     *     monthly: array<int, array{label: string, ym: string, amount: float, count: int, bills_count: int, bills_amount: float}>,
     *     recent_compenzations: array<int, array<string, mixed>>,
     *     recent_bills: array<int, array<string, mixed>>,
     *     top_entities: array<int, array<string, mixed>>,
     *     unfinished: array<int, array<string, mixed>>,
     *     generated_at: string,
     * }
     */
    public function summary(int $months = 12): array
    {
        $now = Carbon::now();
        $yearStart = $now->copy()->startOfYear()->toDateString();
        $yearEnd = $now->copy()->endOfYear()->toDateString();

        return [
            'kpis' => $this->kpis($now, $yearStart, $yearEnd),
            'monthly' => $this->monthlyTrend($now, $months),
            'recent_compenzations' => $this->recentCompenzations(),
            'recent_bills' => $this->recentBills(),
            'top_entities' => $this->topEntities(),
            'unfinished' => $this->unfinishedCompenzations(),
            'generated_at' => $now->toIso8601String(),
        ];
    }

    /**
     * Single-number KPIs that go into the top strip.
     *
     * @return array<string, int|float|null>
     */
    private function kpis(Carbon $now, string $yearStart, string $yearEnd): array
    {
        $year = $now->year;

        $compenzationsActive = Compenzation::where('finished', false)->count();
        $compenzationsFinishedYear = Compenzation::where('finished', true)
            ->whereYear('date', $year)
            ->count();

        $totalAmountYear = (float) Compenzation::whereYear('date', $year)->sum('amount');
        $billsYearCount = Bill::whereYear('date', $year)->count();
        $billsYearAmount = (float) Bill::whereYear('date', $year)->sum('amount');

        // Re-use the canonical stats service so percent-diff math stays in one
        // place (CLAUDE.md section 4, point 6).
        $statsForYear = $this->stats->stats($yearStart, $yearEnd);
        $avgPercentDiffYear = $statsForYear['summary']['count'] > 0
            ? (float) $statsForYear['summary']['avg_percent_diff']
            : null;
        $sumAmountDiffYear = (float) $statsForYear['summary']['sum_amount_diff'];

        return [
            'entities_count' => Entity::count(),
            'compenzations_active' => $compenzationsActive,
            'compenzations_finished_year' => $compenzationsFinishedYear,
            'total_amount_year' => round($totalAmountYear, 2),
            'bills_year_count' => $billsYearCount,
            'bills_year_amount' => round($billsYearAmount, 2),
            'avg_percent_diff_year' => $avgPercentDiffYear,
            'sum_amount_diff_year' => round($sumAmountDiffYear, 2),
            'year' => $year,
        ];
    }

    /**
     * Last `$months` months of compenzation + bill totals, padded with zeros
     * so the chart always renders a full window.
     *
     * @return array<int, array{label: string, ym: string, amount: float, count: int, bills_count: int, bills_amount: float}>
     */
    private function monthlyTrend(Carbon $now, int $months): array
    {
        $months = max(1, min($months, 36));
        $start = $now->copy()->startOfMonth()->subMonths($months - 1)->toDateString();
        $end = $now->copy()->endOfMonth()->toDateString();

        $compenzationRows = Compenzation::query()
            ->selectRaw("DATE_FORMAT(date, '%Y-%m') as ym, SUM(amount) as amount, COUNT(*) as count")
            ->whereBetween('date', [$start, $end])
            ->groupBy('ym')
            ->get()
            ->keyBy('ym');

        $billRows = Bill::query()
            ->selectRaw("DATE_FORMAT(date, '%Y-%m') as ym, SUM(amount) as amount, COUNT(*) as count")
            ->whereBetween('date', [$start, $end])
            ->groupBy('ym')
            ->get()
            ->keyBy('ym');

        $window = collect();
        $cursor = $now->copy()->startOfMonth()->subMonths($months - 1);
        for ($i = 0; $i < $months; $i++) {
            $ym = $cursor->format('Y-m');
            $cRow = $compenzationRows->get($ym);
            $bRow = $billRows->get($ym);

            $window->push([
                'ym' => $ym,
                'label' => self::SL_MONTH_SHORT[$cursor->month].' '.$cursor->format('Y'),
                'amount' => round((float) ($cRow->amount ?? 0), 2),
                'count' => (int) ($cRow->count ?? 0),
                'bills_amount' => round((float) ($bRow->amount ?? 0), 2),
                'bills_count' => (int) ($bRow->count ?? 0),
            ]);

            $cursor->addMonth();
        }

        return $window->all();
    }

    /**
     * 5 most recently dated compenzations with the data the table needs.
     *
     * @return array<int, array<string, mixed>>
     */
    private function recentCompenzations(): array
    {
        return Compenzation::query()
            ->with([
                'compenzationEntity.entity',
                'implementationAgreement',
                'realizationAgreement',
            ])
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit(5)
            ->get()
            ->map(fn (Compenzation $c) => [
                'id' => $c->id,
                'name' => $c->name,
                'date' => optional($c->date)->toDateString(),
                'amount' => (float) $c->amount,
                'finished' => (bool) $c->finished,
                'first_entity' => $this->firstEntityName($c),
                'second_entity' => $this->secondEntityName($c),
                'discount' => (float) ($c->implementationAgreement->discount ?? 0),
                'commission' => (float) ($c->realizationAgreement->commission ?? 0),
            ])
            ->all();
    }

    /**
     * 5 most recent bills.
     *
     * @return array<int, array<string, mixed>>
     */
    private function recentBills(): array
    {
        return Bill::query()
            ->with('entity:id,company_name')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit(5)
            ->get()
            ->map(fn (Bill $bill) => [
                'id' => $bill->id,
                'date' => optional($bill->date)->toDateString(),
                'year' => (int) $bill->year,
                'amount' => (float) $bill->amount,
                'entity_id' => $bill->id_entity,
                'entity_name' => $bill->entity->company_name ?? null,
            ])
            ->all();
    }

    /**
     * Top 5 entities by sum of compenzation amounts they participated in.
     *
     * Uses a single grouped query on the pivot rather than loading every
     * entity into PHP.
     *
     * @return array<int, array<string, mixed>>
     */
    private function topEntities(): array
    {
        $rows = DB::table('compenzation_entities as ce')
            ->join('compenzations as c', 'c.id', '=', 'ce.id_compenzation')
            ->join('entities as e', 'e.id', '=', 'ce.id_entity')
            ->select(
                'e.id as id',
                'e.company_name as company_name',
                DB::raw('COUNT(DISTINCT c.id) as compenzations_count'),
                DB::raw('SUM(c.amount) as total_amount'),
            )
            ->groupBy('e.id', 'e.company_name')
            ->orderByDesc('total_amount')
            ->limit(5)
            ->get();

        return $rows->map(fn ($row) => [
            'id' => (int) $row->id,
            'company_name' => (string) $row->company_name,
            'compenzations_count' => (int) $row->compenzations_count,
            'total_amount' => round((float) $row->total_amount, 2),
        ])->all();
    }

    /**
     * Up to 5 unfinished compenzations sorted by their target finish date,
     * so the user sees the most overdue ones first.
     *
     * @return array<int, array<string, mixed>>
     */
    private function unfinishedCompenzations(): array
    {
        return Compenzation::query()
            ->with('compenzationEntity.entity')
            ->where('finished', false)
            ->orderBy('date_finished')
            ->orderBy('id')
            ->limit(5)
            ->get()
            ->map(fn (Compenzation $c) => [
                'id' => $c->id,
                'name' => $c->name,
                'date' => optional($c->date)->toDateString(),
                'date_finished' => optional($c->date_finished)->toDateString(),
                'amount' => (float) $c->amount,
                'first_entity' => $this->firstEntityName($c),
                'second_entity' => $this->secondEntityName($c),
            ])
            ->all();
    }

    /**
     * Resolve the initiator (`num = 1` or first sorted) entity name.
     */
    private function firstEntityName(Compenzation $c): string
    {
        $entities = $this->sortedEntities($c);

        return $entities->first()?->entity?->company_name ?? '';
    }

    /**
     * Resolve the partner (`num = 2`) entity name when present.
     */
    private function secondEntityName(Compenzation $c): string
    {
        $entities = $this->sortedEntities($c);

        return $entities->firstWhere('num', 2)?->entity?->company_name
            ?? $entities->last()?->entity?->company_name
            ?? '';
    }

    /**
     * Stable ordering of pivot rows by `num`, mirroring CompenzationStatsService.
     */
    private function sortedEntities(Compenzation $c): Collection
    {
        return $c->compenzationEntity
            ->sortBy(fn ($item) => $item->num ?? PHP_INT_MAX)
            ->values();
    }
}
