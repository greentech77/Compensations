<?php

namespace App\Services\Bills;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\Bill;
use App\Models\Entity;
use App\Models\Compenzation;
use App\Models\CompenzationEntity;
use App\Models\BillCompenzation;
use App\Models\RealizationAgreement;

class BillService
{
    /**
     * Get all bills with optional filters
     *
     * @param int|null $entityId
     * @param string|null $dateFrom
     * @param string|null $dateTo
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function bills($entityId = null, $dateFrom = null, $dateTo = null)
    {
        $query = Bill::with(['entity', 'compenzations']);

        if ($entityId) {
            $query->where('id_entity', $entityId);
        }

        if ($dateFrom) {
            $dateFromFormatted = Carbon::parse($dateFrom)->format('Y-m-d');
            $query->whereDate('date', '>=', $dateFromFormatted);
        }

        if ($dateTo) {
            $dateToFormatted = Carbon::parse($dateTo)->format('Y-m-d');
            $query->whereDate('date', '<=', $dateToFormatted);
        }

        return $query->orderBy('date', 'desc')->paginate(15);
    }

    /**
     * Get a single bill by ID
     *
     * @param int $id
     * @return Bill|null
     */
    public function bill($id)
    {
        return Bill::with(['entity', 'compenzations'])->find($id);
    }

    /**
     * Get all entities for dropdown
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getEntities()
    {
        return Entity::orderBy('company_name', 'ASC')->get(['id', 'company_name']);
    }

    /**
     * Create specification (bill) for a client within date range
     * Based on legacy createspecifications.php logic
     *
     * @param int $entityId
     * @param string $dateFrom
     * @param string $dateTo
     * @return Bill
     * @throws \Exception
     */
    public function createSpecification($entityId, $dateFrom, $dateTo)
    {
        return DB::transaction(function() use ($entityId, $dateFrom, $dateTo) {
            // Parse dates
            $dateFromFormatted = Carbon::parse($dateFrom)->format('Y-m-d');
            $dateToFormatted = Carbon::parse($dateTo)->format('Y-m-d');
            $currentDate = Carbon::now()->format('Y-m-d');
            $currentYear = Carbon::now()->format('Y');

            // Get next bill ID
            $lastBill = Bill::latest('id')->first();
            $maxId = $lastBill ? ($lastBill->id + 1) : 1;

            // Find compenzations that match criteria:
            // - date_payed between dateFrom and dateTo
            // - linked to entity (num != 2)
            // - finished = true (boolean) or 1 (integer)
            $compenzations = Compenzation::whereHas('compenzationEntity', function($query) use ($entityId) {
                $query->where('id_entity', $entityId)
                      ->where(function($q) {
                          $q->where('num', '!=', 2)
                            ->orWhereNull('num');
                      });
            })
            ->where(function($query) {
                $query->where('finished', true)
                      ->orWhere('finished', 1);
            })
            ->whereBetween('date_payed', [$dateFromFormatted, $dateToFormatted])
            ->with(['realizationAgreement', 'compenzationEntity'])
            ->get();

            // Debug logging
            \Log::info('BillService::createSpecification - Search criteria', [
                'entity_id' => $entityId,
                'date_from' => $dateFromFormatted,
                'date_to' => $dateToFormatted,
                'found_count' => $compenzations->count()
            ]);

            if ($compenzations->isEmpty()) {
                // Try to find why no compenzations were found
                $allCompenzationsForEntity = Compenzation::whereHas('compenzationEntity', function($query) use ($entityId) {
                    $query->where('id_entity', $entityId);
                })->get();
                
                $finishedCompenzations = Compenzation::whereHas('compenzationEntity', function($query) use ($entityId) {
                    $query->where('id_entity', $entityId);
                })
                ->where(function($query) {
                    $query->where('finished', true)->orWhere('finished', 1);
                })
                ->get();
                
                $inDateRange = Compenzation::whereHas('compenzationEntity', function($query) use ($entityId) {
                    $query->where('id_entity', $entityId);
                })
                ->whereBetween('date_payed', [$dateFromFormatted, $dateToFormatted])
                ->get();

                \Log::warning('BillService::createSpecification - No compenzations found', [
                    'total_for_entity' => $allCompenzationsForEntity->count(),
                    'finished_count' => $finishedCompenzations->count(),
                    'in_date_range_count' => $inDateRange->count(),
                ]);

                throw new \Exception('Ni kompenzacij za izbrano obdobje in stranko. Preverite, da so kompenzacije zaključene in da je datum plačila v izbranem obdobju.');
            }

            // Calculate totals from realization agreements
            // Based on legacy: transfer_amount = commission_amount + commission_ddv_amount
            $compenzationIds = $compenzations->pluck('id')->toArray();
            
            $totals = RealizationAgreement::whereIn('id_compenzation', $compenzationIds)
                ->selectRaw('SUM(commission_amount) as total_commission_amount, SUM(commission_ddv_amount) as total_commission_ddv_amount')
                ->first();

            $commissionAmount = (float)($totals->total_commission_amount ?? 0);
            $commissionDdvAmount = (float)($totals->total_commission_ddv_amount ?? 0);
            $transferAmount = $commissionAmount + $commissionDdvAmount;

            // Create bill
            $bill = Bill::create([
                'id_entity' => $entityId,
                'amount' => $transferAmount,
                'year' => $currentYear,
                'date' => $currentDate,
            ]);

            // Link compenzations to bill in bills_compenzations table
            foreach ($compenzations as $compenzation) {
                BillCompenzation::create([
                    'id_bill' => $bill->id,
                    'id_compenzation' => $compenzation->id,
                    'id_entity' => $entityId,
                ]);
            }

            // Load relationships
            $bill->load(['entity', 'compenzations.realizationAgreement']);

            return $bill;
        });
    }
}

