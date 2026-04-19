<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostNumber extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'post_numbers';

    protected $fillable = ['code', 'postname'];

    /**
     * Get entities with this postal code
     */
    public function entities()
    {
        return $this->hasMany(Entity::class, 'post_num', 'code');
    }
}
