<?php

namespace App\Http\Controllers\Compenzation;

use Inertia\Inertia;
use Illuminate\Support\Arr;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Log;
use App\Validation\Validation;
use App\Models\Compenzation;
use App\Services\Compenzations\CompenzationPdfService;
use App\Services\Compenzations\CompenzationService;
use App\Services\Compenzations\CompenzationStatsService;
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
        $search = $request->input('search');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $sort = $request->input('sort');
        $direction = $request->input('direction');

        // Normalize against the service whitelist so the frontend always
        // receives the effective sort that was actually applied.
        $effectiveSort = array_key_exists($sort, CompenzationService::SORTABLE_COLUMNS)
            ? $sort
            : CompenzationService::DEFAULT_SORT;
        $effectiveDirection = strtolower((string) $direction) === 'desc' ? 'desc' : 'asc';

        $compenzations = $compenzationsService->compenzations(
            $search,
            $dateFrom,
            $dateTo,
            $effectiveSort,
            $effectiveDirection
        );

        return Inertia::render('Compenzations', [
            'compenzations' => $compenzations,
            'filters' => [
                'search' => $search,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
                'sort' => $effectiveSort,
                'direction' => $effectiveDirection,
            ],
            'breadcrumb' => [
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
                $compenzationService->patchCompenzation($id, $data);
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


    public function postCompenzation(Request $request, Validation $validation, CompenzationService $compenzationService) 
    {
        $input = $request->input();
    
        $compenzation = $compenzationService->addCompenzation($input);
    
        AddCompenzationEvent::dispatch($compenzation);
    
        //dd(session()->all());
    
        session()->forget('compenzation');
    
        return redirect()->route('compenzations')->with([
            'modal' => [
                'title' => __('modals.compenzation.title'),
                'content' => $compenzation->name . ' dodana.',
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

    public function downloadCompenzationPdf(Request $request, CompenzationPdfService $pdfs, $id, $type)
    {
        if (!in_array($type, ['proposal', 'implementation', 'realization'], true)) {
            abort(404, 'Invalid PDF type');
        }

        $compenzation = Compenzation::with([
            'proposal',
            'implementationAgreement',
            'realizationAgreement',
            'compenzationEntity.entity',
        ])->findOrFail($id);

        // Resolve the path; if the file is missing on disk, the service
        // will (re)generate it on the fly and persist it for next time.
        try {
            $filePath = $pdfs->resolvePath($compenzation, $type);
        } catch (\Throwable $e) {
            Log::error("On-demand PDF generation failed for compenzation {$id} ({$type}): ".$e->getMessage());
            abort(500, 'PDF dokument ni na voljo. Poskusite znova ali stopite v stik s podporo.');
        }

        if (!$filePath || !Storage::disk('local')->exists($filePath)) {
            abort(404, 'PDF file not found');
        }

        $defaultName = match ($type) {
            'proposal' => "kompenzacija{$id}.pdf",
            'implementation' => "pogodba_o_izvedbi{$id}.pdf",
            'realization' => "pogodba_o_unovcenju{$id}.pdf",
        };

        $fileName = match ($type) {
            'proposal' => $compenzation->proposal->file_name ?? $defaultName,
            'implementation' => $compenzation->implementationAgreement->file_name ?? $defaultName,
            'realization' => $compenzation->realizationAgreement->file_name ?? $defaultName,
        };

        return Storage::disk('local')->download($filePath, $fileName);
    }

    /**
     * Force-regenerate all three PDFs for a single compenzation.
     */
    public function regeneratePdfs(Request $request, CompenzationPdfService $pdfs, $id)
    {
        $compenzation = Compenzation::with([
            'proposal',
            'implementationAgreement',
            'realizationAgreement',
            'compenzationEntity.entity',
        ])->findOrFail($id);

        try {
            $pdfs->generateAll($compenzation);

            return redirect()->back()->with([
                'modal' => [
                    'title' => 'PDF dokumenti',
                    'content' => 'PDF dokumenti so bili uspešno regenerirani.',
                    'status' => 'success',
                    'actions' => [[
                        'action' => ['type' => 'close'],
                        'text' => __('modals.common.confirm'),
                    ]],
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error("Manual PDF regeneration failed for compenzation {$id}: ".$e->getMessage());

            return redirect()->back()->withErrors([
                'pdf' => 'Regeneracija PDF dokumentov ni uspela. Poskusite znova.',
            ]);
        }
    }

    public function stats(Request $request, CompenzationStatsService $compenzationStatsService)
    {
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        return Inertia::render('CompenzationStats', [
            'stats' => $compenzationStatsService->stats($dateFrom, $dateTo),
            'filters' => [
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
            'breadcrumb' => [
                [
                    'label' => 'Kompenzacije',
                    'route' => route('compenzations'),
                ],
                [
                    'label' => 'Statistika',
                ],
            ],
        ]);
    }

    public function exportStats(Request $request, CompenzationStatsService $compenzationStatsService)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
        ]);

        $stats = $compenzationStatsService->stats($request->input('date_from'), $request->input('date_to'));
        $filename = 'kompenzacije-statistika_'.date('Y-m-d').'.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($stats) {
            $file = fopen('php://output', 'w');
            fprintf($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'Kompenzacija',
                'Prva stranka',
                'Druga stranka',
                'Znesek',
                'Popust',
                'Provizija',
                'Razlika %',
                'Razlika zneskov',
            ], ';');

            foreach ($stats['rows'] as $row) {
                fputcsv($file, [
                    $row['name'],
                    $row['first_entity'],
                    $row['second_entity'],
                    number_format((float) $row['amount'], 2, ',', '.'),
                    number_format((float) $row['discount'], 2, ',', '.'),
                    number_format((float) $row['commission'], 2, ',', '.'),
                    number_format((float) $row['percent_diff'], 2, ',', '.'),
                    number_format((float) $row['amount_diff'], 2, ',', '.'),
                ], ';');
            }

            fputcsv($file, []);
            fputcsv($file, [
                'Povprecna razlika %',
                number_format((float) $stats['summary']['avg_percent_diff'], 2, ',', '.'),
            ], ';');
            fputcsv($file, [
                'Skupna razlika zneskov',
                number_format((float) $stats['summary']['sum_amount_diff'], 2, ',', '.'),
            ], ';');

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    public function exportCompenzations(Request $request, CompenzationService $compenzationsService)
    {
        $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'format' => 'nullable|in:xml',
        ]);

        try {
            $format = $request->input('format', 'xml');
            $dateFrom = $request->input('date_from');
            $dateTo = $request->input('date_to');

            $query = Compenzation::with(['realizationAgreement', 'implementationAgreement', 'compenzationEntity.entity']);

            if ($dateFrom) {
                $query->whereDate('date', '>=', \Carbon\Carbon::parse($dateFrom)->format('Y-m-d'));
            }

            if ($dateTo) {
                $query->whereDate('date', '<=', \Carbon\Carbon::parse($dateTo)->format('Y-m-d'));
            }

            $compenzations = $query->orderBy('date', 'desc')->get();

            return $this->exportCompenzationsXml($compenzations, $dateFrom, $dateTo);
        } catch (\Throwable $exception) {
            Log::error('Compenzations export failed', [
                'date_from' => $request->input('date_from'),
                'date_to' => $request->input('date_to'),
                'format' => $request->input('format', 'xml'),
                'message' => $exception->getMessage(),
            ]);

            return redirect()->back()->withErrors([
                'export' => 'Izvoz kompenzacij ni uspel. Poskusite znova.',
            ]);
        }
    }

    protected function exportCompenzationsCsv($compenzations, ?string $dateFrom, ?string $dateTo)
    {
        $dateRange = '';
        if ($dateFrom && $dateTo) {
            $dateRange = '_' . $dateFrom . '_' . $dateTo;
        } elseif ($dateFrom) {
            $dateRange = '_od_' . $dateFrom;
        } elseif ($dateTo) {
            $dateRange = '_do_' . $dateTo;
        }

        $filename = 'kompenzacije' . $dateRange . '_' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($compenzations) {
            $file = fopen('php://output', 'w');
            fprintf($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'ID',
                'Naziv kompenzacije',
                'Datum',
                'Leto',
                'Znesek',
                'Diskont (%)',
                'Provizija (%)',
                'Stranke',
            ], ';');

            foreach ($compenzations as $compenzation) {
                $entities = $compenzation->compenzationEntity->map(function($ce) {
                    return $ce->entity ? $ce->entity->company_name : '';
                })->filter()->join(', ');

                $discount = $compenzation->implementationAgreement ?
                    number_format((float)$compenzation->implementationAgreement->discount, 2, ',', '.') :
                    '0,00';

                $commission = $compenzation->realizationAgreement ?
                    number_format((float)$compenzation->realizationAgreement->commission, 2, ',', '.') :
                    '0,00';

                $date = $compenzation->date ?
                    \Carbon\Carbon::parse($compenzation->date)->format('d.m.Y') :
                    '';

                fputcsv($file, [
                    $compenzation->id,
                    $compenzation->name,
                    $date,
                    $compenzation->year,
                    number_format((float)$compenzation->amount, 2, ',', '.'),
                    $discount,
                    $commission,
                    $entities ?: 'Ni strank',
                ], ';');
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    protected function exportCompenzationsXml($compenzations, ?string $dateFrom, ?string $dateTo)
    {
        $dateRange = '';
        if ($dateFrom && $dateTo) {
            $dateRange = '_' . $dateFrom . '_' . $dateTo;
        } elseif ($dateFrom) {
            $dateRange = '_od_' . $dateFrom;
        } elseif ($dateTo) {
            $dateRange = '_do_' . $dateTo;
        }

        $filename = 'kompenzacije' . $dateRange . '_' . date('Y-m-d') . '.xml';
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><kompenzacije></kompenzacije>');

        foreach ($compenzations as $compenzation) {
            $entities = $compenzation->compenzationEntity->map(function ($ce) {
                return $ce->entity ? $ce->entity->company_name : null;
            })->filter()->values();

            $row = $xml->addChild('kompenzacija');
            $row->addChild('id', $compenzation->id);
            $row->addChild('naziv', htmlspecialchars($compenzation->name, ENT_XML1, 'UTF-8'));
            $row->addChild('datum', optional($compenzation->date)->format('d.m.Y') ?? '');
            $row->addChild('leto', (string) $compenzation->year);
            $row->addChild('znesek', number_format((float) $compenzation->amount, 2, ',', '.'));
            $row->addChild('diskont', number_format((float) ($compenzation->implementationAgreement->discount ?? 0), 2, ',', '.'));
            $row->addChild('provizija', number_format((float) ($compenzation->realizationAgreement->commission ?? 0), 2, ',', '.'));

            $entitiesNode = $row->addChild('stranke');
            foreach ($entities as $entityName) {
                $entitiesNode->addChild('stranka', htmlspecialchars($entityName, ENT_XML1, 'UTF-8'));
            }
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());

        $xmlContent = $dom->saveXML();

        Storage::disk('local')->put(\App\Http\Controllers\ExportController::COMPENZATIONS_EXPORT_DIR.'/'.$filename, $xmlContent);

        return Response::make($xmlContent, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

}