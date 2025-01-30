<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillCompenzation extends Model
{
    use HasFactory;

    protected $fillable = ['id_bill', 'id_compenzation', 'id_entity'];


    // Relationship to Compenzation
    public function bill()
    {
        return $this->belongsTo(Bill::class, 'id_bill', 'id');
    }


    // Relationship to Compenzation
    public function compenzation()
    {
        return $this->belongsTo(Compenzation::class, 'id_compenzation', 'id');
    }

    // Relationship to Entity (if you have a Entity model)
    public function entity()
    {
        return $this->belongsTo(Entity::class, 'id_entity', 'id');
    }



}
