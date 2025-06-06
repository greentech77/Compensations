<?php

namespace App\Services\Entities;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use App\Models\Entity;

class EntityService {

    public function entities() 
    {
        return Entity::paginate(5);
    }

    public function entity($id)
    {
        $entity = Entity::find($id);
        
        return $entity;
    }

    public function getEntitiesIdName()
    {
        $entities = Entity::get(['id', 'company_name']);
        
        return $entities;
    }

    public function patchEntity($id, $data) {
        return Entity::find($id)->update($data);
    }
}