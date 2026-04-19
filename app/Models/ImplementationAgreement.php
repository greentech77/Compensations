<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImplementationAgreement extends Model
{
    use HasFactory;

    protected $table = 'implementation_agreement';

    protected $fillable = [
        'id_compenzation',
        'discount',
        'with_ddv',
        'discount_amount',
        'discount_ddv_amount',
        'net_amount',
        'transfer_amount',
        'signed_date',
        'valid_from',
        'valid_until',
        'status',
        'reference_number',
        'notes',
        'signed_by',
        'file_path',
        'file_name'
    ];

    protected $casts = [
        'signed_date' => 'date',
        'valid_from' => 'date',
        'valid_until' => 'date',
    ];

    // Relationship to Compenzation
    public function compenzation()
    {
        return $this->belongsTo(Compenzation::class, 'id_compenzation', 'id');
    }
}
