<?php

namespace App\Http\Controllers\User;

use Inertia\Inertia;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Validation\Validation;
use App\Services\Entities\EntityService;
use App\Models\Entity;
use App\Services\Entities\Events\RegistrationEvent;
use App\Services\Entities\Registration\Registration;

class UserController extends Controller
{
    public function getDashboard(Request $request, EntityService $entityService) 
    {
        //$pendingEntities = $entityService->pendingRegistrations();
        return Inertia::render('Dashboard', [
            //'pendingEntities' => $pendingEntities,
            'breadcrumb' =>[
                [
                    'label' => 'Nadzorna plošča',
                ]
            ]
        ]);


    }

    public function getEntities(Request $request, EntityService $entityService) 
    {
        $entities = $entityService->entities();
        return Inertia::render('Entities', [
            'entities' => $entities,
            'breadcrumb' =>[
                [
                    'label' => 'Podjetja',
                ]
            ]
        ]);
    }

    public function getEntity(Request $request, EntityService $entityService, $id) 
    {
        $entity =  $entityService->entity($id);

        return Inertia::render('Entity', [
            'entity' => $entity,
            'breadcrumb' =>[
                [
                    'label' => 'Podjetja',
                    'route' => route('entities')
                ], [
                    'label' => $entity->company_name,
                ]
            ]
        ]);
    }

    public function patchEntity(Request $request, EntityService $entityService, $id) 
    {
        $data = $request->except('action');
        //print_r($data);
        switch ($request->action) {
            case 'update':
                $entityService->patchEntity($data['id'], Arr::except($data, 'id'));
                break;
        }

        return redirect()->back();
    }

    public function RegisterEntity() 
    {
        return Inertia::render('RegisterEntity', [
            'breadcrumb' =>[
                [
                    'label' => 'Dodaj podjetje',
                ]
            ]
        ]);
    }

    /**
     * Post za entity registracijo / final step.
     * 
     * @param Request $request
     * @param Validation $validation
     * @param Registration $registration
     * @return RedirectResponse
     */
    public function postEntity(Request $request, Validation $validation, Registration $registration) {

        // manjka session data iz registration route
        /*if (session()->missing('registration')) {
            return redirect()->route('register');
        }*/

        $request->validate(array_merge(
            $validation->entityData('entityData')
        ));

        $input = $request->input();

        $entity = $registration->registerEntity($input);

        RegistrationEvent::dispatch($entity);

        //dd(session()->all());

        session()->forget('registration');

        return redirect()->route('entities')->with([
            'modal' => [
                'title' => __('modals.register.title'),
                'content' => __('modals.register.success'),
                'status' => 'success',
                'actions' => [[
                    'action' => [
                        // 'type' => 'redirect',
                        'type' => 'close',
                        //'url' => route('login')
                    ],
                    'text' => __('modals.common.confirm')
                ]]
            ]
        ]);
        
    }

    /**
     * Post za enterprise registration / Data step validacija.
     */
    public function postEntityData(Request $request, Validation $validation) 
    {
        $request->validate($validation->entityData());
        return redirect()->back();
    }
}
