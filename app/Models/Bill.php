<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'bills';

    protected $fillable = ['id_entity', 'amount', 'year', 'date'];

    /**
     * Get the entity (client) that owns the bill.
     */
    public function entity()
    {
        return $this->belongsTo(Entity::class, 'id_entity', 'id');
    }

    /**
     * Get the compensations associated with this bill.
     */
    public function compenzations()
    {
        return $this->belongsToMany(
            Compenzation::class,
            'bills_compenzations',
            'id_bill',
            'id_compenzation'
        )->withTimestamps();
    }
}

