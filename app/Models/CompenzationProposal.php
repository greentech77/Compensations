<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompenzationProposal extends Model
{
    use HasFactory;

    protected $table = 'compenzations_proposals';

    protected $fillable = ['id_compenzation'];

    public function compenzation()
    {
        return $this->belongsTo(Compenzation::class, 'id_compenzation', 'id');
    }
    
}
