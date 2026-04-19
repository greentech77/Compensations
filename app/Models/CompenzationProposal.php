<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompenzationProposal extends Model
{
    use HasFactory;

    protected $table = 'compenzations_proposals';

    protected $fillable = [
        'id_compenzation',
        'file_path',
        'file_name',
        'status',
        'sent_date',
        'response_date',
        'notes',
        'approved_by'
    ];

    protected $casts = [
        'sent_date' => 'date',
        'response_date' => 'date',
    ];

    public function compenzation()
    {
        return $this->belongsTo(Compenzation::class, 'id_compenzation', 'id');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
    
}
