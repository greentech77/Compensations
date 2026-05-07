<?php

namespace App\Http\Controllers\User;

use Inertia\Inertia;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Validation\Validation;
use App\Services\Compenzations\CompenzationPdfService;
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

    public function downloadCompenzationPdf(Request $request, CompenzationPdfService $pdfs, $entityId, $compenzationId, $type)
    {
        if (!in_array($type, ['proposal', 'implementation', 'realization'], true)) {
            abort(404, 'Invalid PDF type');
        }

        // Verify that the entity is part of this compenzation
        $entityExists = \App\Models\CompenzationEntity::where('id_compenzation', $compenzationId)
            ->where('id_entity', $entityId)
            ->exists();

        if (!$entityExists) {
            abort(403, 'Entity is not part of this compenzation');
        }

        $compenzation = \App\Models\Compenzation::with([
            'proposal',
            'implementationAgreement',
            'realizationAgreement',
            'compenzationEntity.entity',
        ])->findOrFail($compenzationId);

        try {
            $filePath = $pdfs->resolvePath($compenzation, $type);
        } catch (\Throwable $e) {
            \Log::error("On-demand PDF generation failed for compenzation {$compenzationId} ({$type}): ".$e->getMessage());
            abort(500, 'PDF dokument ni na voljo. Poskusite znova ali stopite v stik s podporo.');
        }

        if (!$filePath || !\Storage::disk('local')->exists($filePath)) {
            abort(404, 'PDF file not found');
        }

        $defaultName = match ($type) {
            'proposal' => "kompenzacija{$compenzationId}.pdf",
            'implementation' => "pogodba_o_izvedbi{$compenzationId}.pdf",
            'realization' => "pogodba_o_unovcenju{$compenzationId}.pdf",
        };

        $fileName = match ($type) {
            'proposal' => $compenzation->proposal->file_name ?? $defaultName,
            'implementation' => $compenzation->implementationAgreement->file_name ?? $defaultName,
            'realization' => $compenzation->realizationAgreement->file_name ?? $defaultName,
        };

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
