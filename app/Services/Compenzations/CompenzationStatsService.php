<?php

namespace App\Services\Compenzations;

use App\Models\Compenzation;
use Carbon\Carbon;

class CompenzationStatsService
{
    public function stats(?string $dateFrom = null, ?string $dateTo = null): array
    {
        $query = Compenzation::with([
            'implementationAgreement',
            'realizationAgreement',
            'compenzationEntity.entity',
        ])->where('finished', true);

        if ($dateFrom) {
            $query->whereDate('date_payed', '>=', Carbon::parse($dateFrom)->format('Y-m-d'));
        }

        if ($dateTo) {
            $query->whereDate('date_payed', '<=', Carbon::parse($dateTo)->format('Y-m-d'));
        }

        $compenzations = $query->orderBy('date_payed', 'desc')->get();

        $rows = $compenzations->map(function ($compenzation) {
            $entities = $compenzation->compenzationEntity->sortBy(function ($item) {
                return $item->num ?? PHP_INT_MAX;
            })->values();

            $firstEntity = $entities->first();
            $secondEntity = $entities->firstWhere('num', 2) ?? $entities->last();

            $amount = (float) $compenzation->amount;
            $discount = (float) ($compenzation->implementationAgreement->discount ?? 0);
            $commission = (float) ($compenzation->realizationAgreement->commission ?? 0);
            $withDdv = (bool) ($compenzation->implementationAgreement->with_ddv ?? false);

            if ($withDdv && $discount > 0) {
                $discount = round($discount / 1.22, 2);
            }

            $percentDiff = round($commission - $discount, 2);
            $amountDiff = round($amount * ($percentDiff / 100), 2);

            return [
                'id' => $compenzation->id,
                'name' => $compenzation->name,
                'amount' => $amount,
                'first_entity' => $firstEntity?->entity?->company_name ?? '',
                'second_entity' => $secondEntity?->entity?->company_name ?? '',
                'discount' => $discount,
                'commission' => $commission,
                'percent_diff' => $percentDiff,
                'amount_diff' => $amountDiff,
            ];
        })->values();

        $count = max($rows->count(), 1);

        return [
            'rows' => $rows->toArray(),
            'summary' => [
                'avg_percent_diff' => round($rows->sum('percent_diff') / $count, 2),
                'sum_amount_diff' => round($rows->sum('amount_diff'), 2),
                'count' => $rows->count(),
            ],
        ];
    }
}
