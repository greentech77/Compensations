<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompenzationEntity extends Model
{
    use HasFactory;

    protected $table = 'compenzation_entities';

     // Define the fillable fields
     protected $fillable = ['id_compenzation', 'id_entity', 'num'];

     // Relationship to Compenzation
     public function compenzation()
     {
         return $this->belongsTo(Compenzation::class, 'id_compenzation', 'id');
     }
 
     // Relationship to Entity (if you have a Entity model)
     public function entity()
     {
         return $this->belongsTo(Entity::class, 'id_entity', 'id')
                    ->select('id', 'company_name');
     }
}
