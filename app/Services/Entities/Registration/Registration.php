<?php

namespace App\Services\Entities\Registration;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Exceptions\KompenzacijeModalException;
use App\Models\Entity;


class Registration {

    /**
     * Registrira podjetje. Glej podatkovni model.
     *
     * @param array $data
     * @return Entity
     */
    public function registerEntity($data) 
    {
        [
            'entityData' => $entityData,
        ] = $data;

        // preveri, če že obstaja entiteta za to firmo
        if ($this->entityExists($entityData['vatNum']))
        {
            throw new KompenzacijeModalException(__('modals.register.entity_exists'), 409);
        }

        $entity = DB::transaction(function() use ($entityData) 
        {
            $entity = Entity::make([
                'company_name' => $entityData['companyName'],
                'vat_num' => $entityData['vatNum'],
                'registration_num' => $entityData['registrationNum'],
                'name' => $entityData['name'],
                'lastname' => $entityData['lastname'],
                'email' => $entityData['email'],
                'phone' => $entityData['phone'],
                'address' => $entityData['address'],
                'post_num' => $entityData['postNum'],
                'post_town' => $entityData['postTown'],
                'bank_account' => $entityData['bankAccount'],
                'bank_bic' => $entityData['bankBIC'],
                'bank_name' => $entityData['bankName']
            ]);
            $entity->save();

            return $entity;
        });

        return $entity;
    }



    /**
     * Preveri, če obstaja podjetje z davčno številko.
     *
     * @param string $vatNum
     * @return Boolean
     */
    private function entityExists($vatNum) {
        return Entity::where('vat_num', $vatNum)->exists();
    }


}