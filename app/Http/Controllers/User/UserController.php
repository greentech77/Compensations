<?php

namespace App\Http\Controllers\User;

use Inertia\Inertia;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
        $search = $request->input('search');
        $entities = $entityService->entities($search);
        
        return Inertia::render('Entities', [
            'entities' => $entities,
            'filters' => [
                'search' => $search
            ],
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

    public function downloadCompenzationPdf(Request $request, $entityId, $compenzationId, $type)
    {
        $compenzation = \App\Models\Compenzation::with([
            'proposal',
            'implementationAgreement',
            'realizationAgreement'
        ])->findOrFail($compenzationId);

        // Verify that the entity is part of this compenzation
        $entityExists = \App\Models\CompenzationEntity::where('id_compenzation', $compenzationId)
            ->where('id_entity', $entityId)
            ->exists();

        if (!$entityExists) {
            abort(403, 'Entity is not part of this compenzation');
        }

        $filePath = null;
        $fileName = null;

        switch ($type) {
            case 'proposal':
                $filePath = $compenzation->proposal->file_path ?? null;
                $fileName = $compenzation->proposal->file_name ?? "kompenzacija{$compenzationId}.pdf";
                break;
            case 'implementation':
                $filePath = $compenzation->implementationAgreement->file_path ?? null;
                $fileName = $compenzation->implementationAgreement->file_name ?? "pogodba_o_izvedbi{$compenzationId}.pdf";
                break;
            case 'realization':
                $filePath = $compenzation->realizationAgreement->file_path ?? null;
                $fileName = $compenzation->realizationAgreement->file_name ?? "pogodba_o_unovcenju{$compenzationId}.pdf";
                break;
            default:
                abort(404, 'Invalid PDF type');
        }

        if (!$filePath || !\Storage::disk('local')->exists($filePath)) {
            abort(404, 'PDF file not found');
        }

        return \Storage::disk('local')->download($filePath, $fileName);
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
