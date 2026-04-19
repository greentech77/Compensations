<?php

namespace App\Services\Exports;

use App\Models\Compenzation;
use Carbon\Carbon;

class ContractsExportService
{
    public function rows(?string $dateFrom = null, ?string $dateTo = null): array
    {
        $query = Compenzation::with([
            'implementationAgreement',
            'compenzationEntity.entity',
        ])->where('finished', true);

        if ($dateFrom) {
            $query->whereDate('date_payed', '>=', Carbon::parse($dateFrom)->format('Y-m-d'));
        }

        if ($dateTo) {
            $query->whereDate('date_payed', '<=', Carbon::parse($dateTo)->format('Y-m-d'));
        }

        return $query->orderBy('date_payed')->get()->map(function ($compenzation) {
            $entities = $compenzation->compenzationEntity->sortBy(function ($item) {
                return $item->num ?? PHP_INT_MAX;
            })->values();

            $partner = $entities->firstWhere('num', 2) ?? $entities->last();
            $vatNumber = preg_replace('/^SI/i', '', (string) ($partner?->entity?->vat_num ?? ''));
            $amount = rtrim(rtrim(number_format((float) ($compenzation->implementationAgreement->net_amount ?? 0), 2, '.', ''), '0'), '.');

            return [
                'naziv_partnerja' => $partner?->entity?->company_name ?? 'N/A',
                'naslov_partnerja' => $partner?->entity?->address ?? '',
                'davcna_stevilka_partnerja' => $vatNumber,
                'stevilka_pogodbe' => (string) $compenzation->id,
                'datum_pogodbe' => optional($compenzation->date)->format('d.m.Y') ?? '',
                'znesek_provizije' => $amount,
            ];
        })->toArray();
    }
}
