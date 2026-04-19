<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RealizationAgreement extends Model
{
    use HasFactory;

    protected $table = 'realization_agreement';

    protected $fillable = [
        'id_compenzation',
        'commission',
        'commission_amount',
        'commission_ddv_amount',
        'transfer_amount',
        'signed_date',
        'valid_from',
        'valid_until',
        'status',
        'reference_number',
        'payment_date',
        'payment_status',
        'notes',
        'signed_by',
        'file_path',
        'file_name'
    ];

    protected $casts = [
        'signed_date' => 'date',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'payment_date' => 'date',
    ];

    // Relationship to Compenzation
    public function compenzation()
    {
        return $this->belongsTo(Compenzation::class, 'id_compenzation', 'id');
    }
}
