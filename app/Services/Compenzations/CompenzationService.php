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

    public function compenzations() 
    {
        return Compenzation::with(['realizationAgreement', 'implementationAgreement'])
                                    ->paginate(7);
    }

    public function compenzation($id)
    {
        $compenzation = Compenzation::with(['realizationAgreement', 'implementationAgreement', 'compenzationEntity.entity'])->find($id);

        return $compenzation;
    }

    public function patchCompenzation($id, $data)
    {
        // Find the Compenzation
        $compenzation = Compenzation::findOrFail($id);

        // Separate data for different models
        $compenzationData = Arr::except($data, ['realization_agreement', 'implementation_agreement']);
        $realizationAgreementData = $data['realization_agreement'] ?? [];
        $implementationAgreementData = $data['implementation_agreement'] ?? [];

        // Update Compenzation
        //dd($compenzationData);
        $compenzation->update($compenzationData);

        // Update Realization Agreement
        if ($compenzation->realizationAgreement)
        {
            $compenzation->realizationAgreement->update($realizationAgreementData);
        }
        else
        {
            $compenzation->realizationAgreement()->create($realizationAgreementData);
        }

        // Update Implementation Agreement
        if ($compenzation->implementationAgreement)
        {
            $compenzation->implementationAgreement->update($implementationAgreementData);
        }
        else
        {
            $compenzation->implementationAgreement()->create($implementationAgreementData);
        }

        // Refresh the model to ensure all updates are reflected
        $compenzation->refresh();

        return $compenzation;
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
                'date'          => Carbon::parse($compenzationData['compenzationDate'])->format('Y-m-d H:i:s'),
                'year'          => date('Y'),
                'amount'        => $compenzationData['compenzationAmount'],
                'date_finished' => Carbon::parse($compenzationData['compenzationDate'])->format('Y-m-d H:i:s'),
                'date_payed'    => Carbon::parse($compenzationData['compenzationDate'])->format('Y-m-d H:i:s'),
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