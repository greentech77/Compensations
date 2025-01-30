<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RealizationAgreement extends Model
{
    use HasFactory;

    protected $table = 'realization_agreement';

    protected $fillable = ['id_compenzation', 'commission', 'commission_amount', 'commission_ddv_amount', 'transfer_amount'];

    // Relationship to Compenzation
    public function compenzation()
    {
        return $this->belongsTo(Compenzation::class, 'id_compenzation', 'id');
    }
}
