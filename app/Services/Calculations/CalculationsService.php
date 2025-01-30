<?php

namespace App\Services\Calculations;

class CalculationsService
{
    public function calculateDiscount($compenzationAmount, $compenzationDiscount, $discountWithVat) {

        $compenzationDiscount   = $discountWithVat ? $compenzationDiscount + ($compenzationDiscount * 0.22) : $compenzationDiscount;

        $amountWithOutDDV       = round((($compenzationDiscount / 100) / 1.22) * $compenzationAmount, 2);

        $discountAmount         = round($compenzationAmount *   ($compenzationDiscount  /   100), 2);

        $netDicountAmount       = round($discountAmount - $amountWithOutDDV, 2);

        $transferAmount         = round($compenzationAmount - $discountAmount, 2);

        return compact('netDicountAmount', 'amountWithOutDDV', 'discountAmount', 'transferAmount');

    }

    public function calculateCompenzation($compenzationAmount, $compenzationComission) {

        $comissionAmount    =   round($compenzationAmount   *   ($compenzationComission  /   100));

        $commisionAmountDDV =   round($comissionAmount  *   0.22);

        $transferAmount     =   round($compenzationAmount - $comissionAmount - $commisionAmountDDV);

        return compact('comissionAmount', 'commisionAmountDDV', 'transferAmount');

    }

}
