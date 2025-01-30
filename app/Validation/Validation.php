<?php

namespace App\Validation;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use App\Models\Entity;

class Validation {

    /**
     * Generira rules za naslov
     *
     * @param string $prepend
     * @param boolean|string $required
     * @return array
     */
    private function address($prepend = null, $required = true) {
        $rules = [
            'address' => ['required', 'string'],
            'postNum' => ['required', 'numeric'],
            'postTown' => ['required', 'string']
        ];
        /*if ($required) {
            $rules = array_map(function ($item) use ($required) { 
                array_unshift($item, is_string($required) ? "required_if:$required" : 'required');
                return $item;
            }, $rules);
        }*/
        return $this->prependKeys($prepend, $rules);
    }

    /**
     * Generira rules za bančni račun
     *
     * @return array
     */
    private function bankAccount($prepend = null) {
        $rules = [
            'bankAccount' => ['required', 'string'],
            'bankBIC' => ['required', 'string'],
            'bankName' => ['required', 'string']
        ];
        return $this->prependKeys($prepend, $rules);
    }

    /**
     * Prependa keye v arrayu z stringom
     *
     * @param string $prepend
     * @param array $array
     * @return array
     */
    public function prependKeys($prepend, $array) {
        if ($prepend == null) {
            return $array;
        }
        return array_combine(
            array_map(function($key) use ($prepend) { 
                return "$prepend.$key"; 
            }, array_keys($array)),
            $array
        );
    }

    /**
     * Generira validacijo za 1. step enterprise registracije / entity data
     *
     * @return void
     */
    public function entityData($prepend = null) {
        $validation = array_merge([
            'companyName' => ['required', 'string'],
            'vatNum' => ['required', 'string'],
            'registrationNum' => ['required', 'string'],
            'name' => ['required', 'string'],
            'lastname' => ['required', 'string'],
            
        ], $this->address(), $this->bankAccount());

        return $this->prependKeys($prepend, $validation);
    }

    /**
     * Generira rules za stranke kompenzacije
     *
     * @return array
     */
    private function compenzationEntities($prepend = null) {
        $rules = [
            'compenzationEntities.*' => ['required']
        ];
        return $this->prependKeys($prepend, $rules);
    }

    /**
     * Generira validacijo za 1. step dodajanja kompenzacij / compenzation data
     *
     * @return void
     */
    public function CompenzationData($prepend = null) {
        $validation = array_merge([
            'compenzationDate' => ['required', 'string'],
            'compenzationAmount' => ['required'],
            'compenzationDiscount' => ['required'],
            'compenzationCommission' => ['required'],
        ], $this->compenzationEntities());

        return $this->prependKeys($prepend, $validation);
    }



}