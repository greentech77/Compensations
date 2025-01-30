<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Compenzation extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'compenzations';

    protected $fillable = ['name', 'date', 'year', 'amount', 'vat', 'commission', 'date_finished', 'date_payed', 'storno', 'finished', 'with_ddv'];

    public function realizationAgreement()
    {
        return $this->hasOne(RealizationAgreement::class, 'id_compenzation', 'id')
            ->select('id', 'id_compenzation', 'commission', 'commission_amount', 'commission_ddv_amount', 'transfer_amount')
            ->withDefault([
                'commission' => 0, // or null, depending on your preference
            ]); // This ensures a null object is returned instead of null
    }

    public function implementationAgreement()
    {
        return $this->hasOne(ImplementationAgreement::class, 'id_compenzation', 'id')
            ->select('id', 'id_compenzation', 'discount', 'with_ddv', 'discount_amount', 'discount_ddv_amount', 'net_amount', 'transfer_amount')
            ->withDefault([
                'discount' => 0, // or null, depending on your preference
            ]); // This ensures a null object is returned instead of null
    }

    public function compenzationEntity()
    {
        return $this->hasMany(CompenzationEntity::class, 'id_compenzation', 'id')
                ->select('id', 'id_compenzation', 'id_entity')
                ->with('entity'); // This eager loads the entity relationship
    }
}
