<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class ExportController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth:web');
    }

    /**
     * Show exports entry page.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        return Inertia::render('Exports/Index', [
            'breadcrumb' => [
                [
                    'label' => 'Izvozi',
                ]
            ]
        ]);
    }

    /**
     * Show the bills export page.
     *
     * @return \Inertia\Response
     */
    public function bills()
    {
        return Inertia::render('Exports/Bills', [
            'breadcrumb' => [
                [
                    'label' => 'Izvozi',
                    'route' => route('exports.index'),
                ],
                [
                    'label' => 'Izvoz računov',
                ]
            ]
        ]);
    }

    /**
     * Show the compenzations export and statistics page.
     *
     * @return \Inertia\Response
     */
    public function compenzations()
    {
        return Inertia::render('Exports/Compenzations', [
            'breadcrumb' => [
                [
                    'label' => 'Izvozi',
                    'route' => route('exports.index'),
                ],
                [
                    'label' => 'Kompenzacije',
                ]
            ]
        ]);
    }

    /**
     * Export bills as CSV.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function exportBillsCsv(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $query = Bill::with(['entity', 'compenzations']);
        $query->whereDate('date', '>=', $request->input('date_from'))
            ->whereDate('date', '<=', $request->input('date_to'));

        $bills = $query->get();

        $filename = 'racuni_' . $request->input('date_from') . '_' . $request->input('date_to') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        // Add BOM for UTF-8 to ensure proper encoding in Excel
        $output = "\xEF\xBB\xBF";

        $callback = function() use ($bills) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, [
                'ID',
                'Stranka',
                'Znesek',
                'Leto',
                'Datum',
                'Kompenzacije',
            ], ';');

            foreach ($bills as $bill) {
                $compenzations = $bill->compenzations->pluck('name')->join(', ');
                
                fputcsv($file, [
                    $bill->id,
                    $bill->entity ? $bill->entity->company_name : 'N/A',
                    number_format((float)$bill->amount, 2, ',', '.'),
                    $bill->year,
                    $bill->date,
                    $compenzations ?: 'Ni kompenzacij',
                ], ';');
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Export bills as XML.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function exportBillsXml(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        $query = Bill::with(['entity', 'compenzations']);
        $query->whereDate('date', '>=', $request->input('date_from'))
            ->whereDate('date', '<=', $request->input('date_to'));

        $bills = $query->get();

        $exportDate = date('d.m.Y');
        $monthName = Carbon::parse($request->input('date_from'))->format('F');
        $filename = 'izvoz-racunov_' . $exportDate . '_' . $monthName . '.xml';

        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><Prenos></Prenos>');
        $glava = $xml->addChild('Glava');
        $glava->addChild('Program', 'OpPIS');
        $glava->addChild('Program_verzija', '1.0.0.487');
        $glava->addChild('Program_avtor', 'Opal d.o.o.');
        $glava->addChild('Verzija_xml', '0.1');

        $telo = $xml->addChild('Telo');
        $issueDate = Carbon::parse($request->input('date_to'))->format('d.m.Y');

        foreach ($bills as $bill) {
            $serviceDate = Carbon::parse($bill->date)->format('d.m.Y');
            $entity = $bill->entity;
            $vatNumber = $entity ? preg_replace('/^SI/i', '', (string) $entity->vat_num) : '';

            $dokument = $telo->addChild('Dokument');
            $dokument->addChild('Dokument', 'Izdani_racun');
            $dokument->addChild('Naziv_partnerja', htmlspecialchars($entity?->company_name ?? 'N/A', ENT_XML1, 'UTF-8'));
            $dokument->addChild('Naslov_partnerja', htmlspecialchars($entity?->address ?? '', ENT_XML1, 'UTF-8'));
            $dokument->addChild('Davcna_stevilka_partnerja', $vatNumber);
            $dokument->addChild('Stevilka_racuna', (string) $bill->id);
            $dokument->addChild('Datum_izdaje_racuna', $issueDate);
            $dokument->addChild('Datum_opravljene_storitve', $serviceDate);
            $dokument->addChild('Datum_zapadlosti_racuna', $serviceDate);
            $dokument->addChild('Znesek', rtrim(rtrim(number_format((float) $bill->amount, 2, '.', ''), '0'), '.'));
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());

        return Response::make($dom->saveXML(), 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /**
     * Export bills (handles both CSV and XML based on format parameter).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function exportBills(Request $request)
    {
        $request->validate([
            'format' => 'nullable|in:xml',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        return $this->exportBillsXml($request);
    }
}

