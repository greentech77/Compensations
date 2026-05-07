<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\HandlesExportFiles;
use App\Services\Exports\ContractsExportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;

class ContractsExportController extends Controller
{
    use HandlesExportFiles;

    public const CONTRACTS_EXPORT_DIR = 'exports/contracts';

    public function __construct()
    {
        $this->middleware('auth:web');
    }

    public function contracts()
    {
        return Inertia::render('Exports/Contracts', [
            'files' => $this->listExportFiles(self::CONTRACTS_EXPORT_DIR, 'exports.contracts.file'),
            'breadcrumb' => [
                ['label' => 'Izvozi', 'route' => route('exports.index')],
                ['label' => 'Izvoz pogodb'],
            ],
        ]);
    }

    /**
     * Download a previously generated contracts export by filename.
     */
    public function downloadContractsFile(string $filename)
    {
        return $this->downloadExportFile(self::CONTRACTS_EXPORT_DIR, $filename);
    }

    public function exportContracts(Request $request, ContractsExportService $contractsExportService)
    {
        $request->validate([
            'format' => 'nullable|in:xml',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
        ]);

        try {
            $rows = $contractsExportService->rows($request->input('date_from'), $request->input('date_to'));

            return $this->exportXml($rows, $request->input('date_from'), $request->input('date_to'));
        } catch (\Throwable $exception) {
            Log::error('Contracts export failed', [
                'date_from' => $request->input('date_from'),
                'date_to' => $request->input('date_to'),
                'format' => $request->input('format'),
                'message' => $exception->getMessage(),
            ]);

            return redirect()->back()->withErrors([
                'export' => 'Izvoz pogodb ni uspel. Poskusite znova.',
            ]);
        }
    }

    private function exportCsv(array $rows, string $dateFrom, string $dateTo)
    {
        $filename = "izvoz-pogodb_{$dateFrom}_{$dateTo}.csv";
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ];

        $callback = function () use ($rows) {
            $file = fopen('php://output', 'w');
            fprintf($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'Naziv_partnerja',
                'Naslov_partnerja',
                'Davcna_stevilka_partnerja',
                'Stevilka_pogodbe',
                'Datum_pogodbe',
                'Znesek_provizije',
            ], ';');

            foreach ($rows as $row) {
                fputcsv($file, [
                    $row['naziv_partnerja'],
                    $row['naslov_partnerja'],
                    $row['davcna_stevilka_partnerja'],
                    $row['stevilka_pogodbe'],
                    $row['datum_pogodbe'],
                    $row['znesek_provizije'],
                ], ';');
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    private function exportXml(array $rows, string $dateFrom, string $dateTo)
    {
        $exportDate = date('d.m.Y');
        $monthName = Carbon::parse($dateFrom)->format('F');
        $filename = "izvoz-pogodb_{$exportDate}_{$monthName}.xml";
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><Prenos></Prenos>');
        $glava = $xml->addChild('Glava');
        $glava->addChild('Program', 'OpPIS');
        $glava->addChild('Program_verzija', '1.0.0.487');
        $glava->addChild('Program_avtor', 'Opal d.o.o.');
        $glava->addChild('Verzija_xml', '0.1');

        $telo = $xml->addChild('Telo');

        foreach ($rows as $row) {
            $dokument = $telo->addChild('Dokument');
            $dokument->addChild('Dokument', 'Pogodbe');
            $dokument->addChild('Naziv_partnerja', htmlspecialchars($row['naziv_partnerja'], ENT_XML1, 'UTF-8'));
            $dokument->addChild('Naslov_partnerja', htmlspecialchars($row['naslov_partnerja'], ENT_XML1, 'UTF-8'));
            $dokument->addChild('Davcna_stevilka_partnerja', htmlspecialchars($row['davcna_stevilka_partnerja'], ENT_XML1, 'UTF-8'));
            $dokument->addChild('Stevilka_pogodbe', $row['stevilka_pogodbe']);
            $dokument->addChild('Datum_pogodbe', $row['datum_pogodbe']);
            $dokument->addChild('Znesek_provizije', $row['znesek_provizije']);
        }

        $dom = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput = true;
        $dom->loadXML($xml->asXML());

        $xmlContent = $dom->saveXML();

        $this->persistExportFile(self::CONTRACTS_EXPORT_DIR, $filename, $xmlContent);

        return Response::make($xmlContent, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }
}
