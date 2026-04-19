<?php

namespace App\Services\Compenzations;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Compenzation;
use App\Models\CompenzationEntity;
use App\Models\CompenzationProposal;
use App\Models\ImplementationAgreement;
use App\Models\RealizationAgreement;

use App\Services\Calculations\CalculationsService;

class CompenzationService {

    protected $calculationsService;

    public function __construct(CalculationsService $calculationsService)
    {
        $this->calculationsService = $calculationsService;
    }

    public function compenzations(?string $search = null, ?string $dateFrom = null, ?string $dateTo = null) 
    {
        $query = Compenzation::with(['realizationAgreement', 'implementationAgreement']);

        if ($search) {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%")
                    ->orWhere('date', 'like', "%{$search}%")
                    ->orWhere('amount', 'like', "%{$search}%")
                    ->orWhereHas('implementationAgreement', function ($agreementQuery) use ($search) {
                        $agreementQuery->where('discount', 'like', "%{$search}%");
                    })
                    ->orWhereHas('realizationAgreement', function ($agreementQuery) use ($search) {
                        $agreementQuery->where('commission', 'like', "%{$search}%");
                    });
            });
        }

        if ($dateFrom) {
            $query->whereDate('date', '>=', Carbon::parse($dateFrom)->format('Y-m-d'));
        }

        if ($dateTo) {
            $query->whereDate('date', '<=', Carbon::parse($dateTo)->format('Y-m-d'));
        }
        
        return $query->orderBy('date', 'desc')->paginate(7);
    }

    public function compenzation($id)
    {
        $compenzation = Compenzation::with(['realizationAgreement', 'implementationAgreement', 'compenzationEntity.entity', 'proposal'])->find($id);

        return $compenzation;
    }

    public function patchCompenzation($id, $data)
    {
        return DB::transaction(function() use ($id, $data) {
            // Find the Compenzation
            $compenzation = Compenzation::findOrFail($id);

            // Extract entities if present
            $entities = $data['entities'] ?? null;
            
            // Separate data for different models
            $compenzationData = Arr::only($data, ['date', 'amount', 'date_payed', 'finished']);
            
            // Convert date to proper format if present (date only, not datetime)
            if (isset($compenzationData['date'])) {
                $compenzationData['date'] = Carbon::parse($compenzationData['date'])->format('Y-m-d');
            }
            
            // Convert date_payed to proper format if present
            if (isset($compenzationData['date_payed'])) {
                $compenzationData['date_payed'] = Carbon::parse($compenzationData['date_payed'])->format('Y-m-d');
            }
            
            // Convert finished to boolean if present
            if (isset($compenzationData['finished'])) {
                $compenzationData['finished'] = (bool)$compenzationData['finished'];
            }
            
            // Extract discount, commission, and with_ddv
            $discount = isset($data['discount']) ? floatval(str_replace(',', '.', $data['discount'])) : null;
            $commission = isset($data['commission']) ? floatval(str_replace(',', '.', $data['commission'])) : null;
            $withDdv = $data['with_ddv'] ?? false;

            // Update Compenzation basic data
            if (!empty($compenzationData)) {
                $compenzation->update($compenzationData);
            }

            // Update entities if provided
            if ($entities !== null && is_array($entities)) {
                // Delete existing entities
                CompenzationEntity::where('id_compenzation', $id)->delete();
                
                // Insert new entities
                foreach ($entities as $entity) {
                    if (isset($entity['key'])) {
                        CompenzationEntity::create([
                            'id_compenzation' => $id,
                            'id_entity' => $entity['key'],
                            'num' => null
                        ]);
                    }
                }
            }

            // Recalculate and update Implementation Agreement
            if ($discount !== null || $withDdv !== null) {
                $amount = $compenzation->amount;
                $discountValue = $discount ?? $compenzation->implementationAgreement->discount ?? 0;
                $withDdvValue = $withDdv;
                
                $discountCalculation = $this->calculationsService->calculateDiscount(
                    $amount, 
                    $discountValue, 
                    $withDdvValue
                );

                if ($compenzation->implementationAgreement) {
                    $compenzation->implementationAgreement->update([
                        'discount' => $discountValue,
                        'with_ddv' => $withDdvValue,
                        'discount_amount' => $discountCalculation['discountAmount'],
                        'discount_ddv_amount' => $discountCalculation['netDicountAmount'],
                        'net_amount' => $discountCalculation['amountWithOutDDV'],
                        'transfer_amount' => $discountCalculation['transferAmount'],
                    ]);
                } else {
                    $compenzation->implementationAgreement()->create([
                        'discount' => $discountValue,
                        'with_ddv' => $withDdvValue,
                        'discount_amount' => $discountCalculation['discountAmount'],
                        'discount_ddv_amount' => $discountCalculation['netDicountAmount'],
                        'net_amount' => $discountCalculation['amountWithOutDDV'],
                        'transfer_amount' => $discountCalculation['transferAmount'],
                    ]);
                }
            }

            // Recalculate and update Realization Agreement
            if ($commission !== null) {
                $amount = $compenzation->amount;
                
                $commissionCalculation = $this->calculationsService->calculateCompenzation(
                    $amount, 
                    $commission
                );

                if ($compenzation->realizationAgreement) {
                    $compenzation->realizationAgreement->update([
                        'commission' => $commission,
                        'commission_amount' => $commissionCalculation['comissionAmount'],
                        'commission_ddv_amount' => $commissionCalculation['commisionAmountDDV'],
                        'transfer_amount' => $commissionCalculation['transferAmount'],
                    ]);
                } else {
                    $compenzation->realizationAgreement()->create([
                        'commission' => $commission,
                        'commission_amount' => $commissionCalculation['comissionAmount'],
                        'commission_ddv_amount' => $commissionCalculation['commisionAmountDDV'],
                        'transfer_amount' => $commissionCalculation['transferAmount'],
                    ]);
                }
            }

            // Refresh the model to ensure all updates are reflected
            $compenzation->refresh();
            $compenzation->load([
                'compenzationEntity.entity',
                'implementationAgreement',
                'realizationAgreement',
                'proposal'
            ]);

            // Trigger PDF regeneration event
            event(new \App\Services\Compenzations\Events\AddCompenzationEvent($compenzation));

            return $compenzation;
        });
    }

    public function deleteCompenzation($id) {
        return Compenzation::find($id)->delete();
    }

    public function addCompenzation($data) {

        [
            'compenzationData' => $compenzationData,

        ] = $data;


        //dd($compenzationData);
        //var_dump($compenzationData);
        //$compenzationDiscount = $data['compenzationData']['compenzationDiscount'] ?? null;
        //$compenzationCommission = $data['compenzationData']['compenzationCommission'] ?? null;

        $compenzation = DB::transaction(function() use ($compenzationData) 
        {
            $compenzationDiscount = $compenzationData['compenzationDiscount'] ?? null;
            $discountWithVat = $compenzationData['discountWithVat'] ?? null;
            $compenzationCommission = $compenzationData['compenzationCommission'] ?? null;

            $lastCompenzation = Compenzation::latest('id')->first();

            if ($lastCompenzation) {
                $newId = $lastCompenzation->id + 1;
            } else {
                $newId = 1; // Handle case where no record exists
            }

            $compenzationName = 'Kompenzacija-' . 
                (($newId < 100) 
                    ? str_pad($newId, 4, '0', STR_PAD_LEFT) 
                    : (($newId < 1000) 
                        ? str_pad($newId, 3, '0', STR_PAD_LEFT) 
                        : $newId)) 
                . '/' . date('Y');

            $compenzation = Compenzation::make([
                'name'          => $compenzationName,
                'date'          => Carbon::parse($compenzationData['compenzationDate'])->format('Y-m-d'),
                'year'          => date('Y'),
                'amount'        => $compenzationData['compenzationAmount'],
                'date_finished' => Carbon::parse($compenzationData['compenzationDate'])->format('Y-m-d'),
                'date_payed'    => Carbon::parse($compenzationData['compenzationDate'])->format('Y-m-d'),
            ]);

            $compenzationId = $compenzation->save() ? $compenzation->id : null;

            $this->insertCompenzationEntities($compenzationId, $compenzationData['compenzationEntities']);

            $this->insertCompenzationProposal($compenzationId);

            $this->insertImplementationAgreement($compenzationId, $compenzationData['compenzationAmount'], $compenzationDiscount, $discountWithVat);

            $this->insertRealizationAgreement($compenzationId, $compenzationData['compenzationAmount'], $compenzationCommission);

            return $compenzation;
        });

        return $compenzation;

        //return Compenzation::create($data);

    }

    public function insertCompenzationEntities($compenzationId, $entities)
    {
        foreach ($entities as $entity) {
            CompenzationEntity::create([
                'id_compenzation' => $compenzationId,
                'id_entity' => $entity['key'],
                'num' => null // or some default/calculated value if needed
            ]);
        }
    }

    public function insertCompenzationProposal($compenzationId) {
        return CompenzationProposal::create([
            'id_compenzation' => $compenzationId
        ]);
    }

    public function insertImplementationAgreement($compenzationId, $compenzationAmount, $compenzationDiscount, $discountWithVat) {

        $discount = $this->calculationsService->calculateDiscount($compenzationAmount, $compenzationDiscount, $discountWithVat);

        return ImplementationAgreement::create([
            'id_compenzation'       => $compenzationId,
            'discount'              => $compenzationDiscount,
            'with_ddv'              => $discountWithVat,
            'discount_amount'       => $discount['discountAmount'],
            'discount_ddv_amount'   => $discount['netDicountAmount'],
            'net_amount'            => $discount['amountWithOutDDV'],
            'transfer_amount'       => $discount['transferAmount'],
        ]);
    }

    public function insertRealizationAgreement($compenzationId, $compenzationAmount, $compenzationCommission) {

        $commission = $this->calculationsService->calculateCompenzation($compenzationAmount, $compenzationCommission);

        return RealizationAgreement::create([
            'id_compenzation'       => $compenzationId,
            'commission'            => $compenzationCommission,
            'commission_amount'     => $commission['comissionAmount'],
            'commission_ddv_amount' => $commission['commisionAmountDDV'],
            'transfer_amount'       => $commission['transferAmount'],
        ]);
    }
}