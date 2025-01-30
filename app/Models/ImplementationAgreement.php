<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImplementationAgreement extends Model
{
    use HasFactory;

    protected $table = 'implementation_agreement';

    protected $fillable = ['id_compenzation', 'discount', 'with_ddv', 'discount_amount', 'discount_ddv_amount', 'net_amount', 'transfer_amount'];

    // Relationship to Compenzation
    public function compenzation()
    {
        return $this->belongsTo(Compenzation::class, 'id_compenzation', 'id');
    }
}
