<?php

namespace App\Http\Controllers\Compenzation;

use Inertia\Inertia;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Validation\Validation;
use App\Models\Compenzation;
use App\Services\Compenzations\CompenzationService;
use App\Services\Compenzations\Events\AddCompenzationEvent;
use App\Services\Entities\EntityService;


class CompenzationController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:web'); // Ensures all routes require authentication
    }
    
    public function getCompenzations(Request $request, CompenzationService $compenzationsService)
    {
        $compenzations = $compenzationsService->compenzations();

        //dd($compenzations);
        return Inertia::render('Compenzations', [
            'compenzations' =>$compenzations,
            'breadcrumb' =>[
                [
                    'label' => 'Kompenzacije',
                ]
            ]
        ]);
    }


    public function getCompenzation(Request $request, CompenzationService $compenzationService, $id) 
    {
        $compenzation =  $compenzationService->compenzation($id);

         // Debug to see exactly what's being passed
        /*\Log::info('Compenzation data:', [
            'id' => $compenzation->id,
            'name' => $compenzation->name,
            'full_model' => $compenzation->toArray()
        ]);*/


        //dump($compenzation->implementationAgreement->with_ddv);


        $entities = $compenzation->compenzationEntity->map(function($entity) {
            return [
                'value' => $entity->id_entity,
                'label' => $entity->entity->company_name,
            ];
        })->toArray();

        //dump ($entities);

        return Inertia::render('Compenzation', [
            'compenzation' => $compenzation,
            'entities' => $entities,
            'breadcrumb' =>[
                [
                    'label' => 'Kompenzacije',
                    'route' => route('entities')
                ], [
                    'label' => $compenzation->name,
                ]
            ]
        ]);
    }

    public function patchCompenzation(Request $request, CompenzationService $compenzationService, $id) 
    {
        $data = $request->except('action');

        //dd($data);
        //print_r($data);
        switch ($request->action) {
            case 'update':
                $compenzationService->patchCompenzation($data['id'], Arr::except($data, 'id'));
                break;
        }

        return redirect()->back();
    }

    public function addCompenzation(EntityService $entityService) 
    {
        $entities = $entityService->getEntitiesIdName();
        return Inertia::render('AddCompenzation', [
            'entities' => $entities,
            'breadcrumb' =>[
                [
                    'label' => 'Dodaj kompenzacijo',
                ]
            ]
        ]);
    }


    public function postCompenzation(Request $request, Validation $validation, CompenzationService $compenzation) 
    {

        $input = $request->input();

        $compenzation = $compenzation->addCompenzation($input);

        AddCompenzationEvent::dispatch($compenzation);

        //dd(session()->all());

        session()->forget('compenzation');

        return redirect()->route('compenzations')->with([
            'modal' => [
                'title' => __('modals.compenation.title'),
                'content' => __('modals.compenation.success'),
                'status' => 'success',
                'actions' => [[
                    'action' => [
                        'type' => 'close'
                    ],
                    'text' => __('modals.common.confirm')
                ]]
            ]
        ]);
    }

    /**
     * Post za compenzation / Data step validacija.
     */
    public function postCompenzationData(Request $request, Validation $validation) 
    {
        $request->validate($validation->CompenzationData());
        return redirect()->back();
    }

}