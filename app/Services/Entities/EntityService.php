<?php

namespace App\Services\Entities;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;
use App\Models\Entity;

class EntityService {

    public function entities($search = null) 
    {
        $query = Entity::query();
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('vat_num', 'like', "%{$search}%")
                  ->orWhere('registration_num', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('post_town', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
        }
        
        return $query->paginate(15)->withQueryString();
    }

    public function entity($id)
    {
        $entity = Entity::with([
            'compenzations.proposal',
            'compenzations.implementationAgreement',
            'compenzations.realizationAgreement'
        ])->find($id);
        
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