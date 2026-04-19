<?php

namespace App\Http\Controllers\Bill;

use App\Http\Controllers\Controller;
use App\Services\Bills\BillService;
use App\Services\PDF\PDFService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BillController extends Controller
{
    protected $billService;

    public function __construct(BillService $billService)
    {
        $this->middleware('auth:web');
        $this->billService = $billService;
    }

    /**
     * Display a listing of bills
     *
     * @param Request $request
     * @return \Inertia\Response
     */
    public function getBills(Request $request)
    {
        $entityId = $request->input('entity_id');
        $dateFrom = $request->input('date_from');
        $dateTo = $request->input('date_to');

        $bills = $this->billService->bills($entityId, $dateFrom, $dateTo);
        $entities = $this->billService->getEntities();

        return Inertia::render('Bills', [
            'bills' => $bills,
            'entities' => $entities,
            'filters' => [
                'entity_id' => $entityId,
                'date_from' => $dateFrom,
                'date_to' => $dateTo,
            ],
            'breadcrumb' => [
                [
                    'label' => 'Računi',
                ]
            ]
        ]);
    }

    /**
     * Create specification (specifikacija) for bills
     * This creates a new bill and links compenzations to it
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createSpecification(Request $request, PDFService $pdfService)
    {
        try {
            $request->validate([
                'entity_id' => 'required|exists:entities,id',
                'date_from' => 'required|date',
                'date_to' => 'required|date|after_or_equal:date_from',
            ]);

            $entityId = $request->input('entity_id');
            $dateFrom = $request->input('date_from');
            $dateTo = $request->input('date_to');

            // Create bill and link compenzations
            $bill = $this->billService->createSpecification($entityId, $dateFrom, $dateTo);

            // Generate PDF
            $pdfPath = null;
            $pdfFilename = null;
            try {
                $pdfPath = $this->generateBillPdf($bill, $pdfService, $dateFrom, $dateTo);
                $pdfFilename = "racun_{$bill->id}_{$bill->year}.pdf";
                \Log::info('PDF generated successfully for bill ' . $bill->id);
            } catch (\Exception $pdfException) {
                \Log::error('PDF generation failed for bill ' . $bill->id . ': ' . $pdfException->getMessage());
                \Log::error('PDF error stack: ' . $pdfException->getTraceAsString());
                throw $pdfException; // Re-throw if PDF generation fails
            }

            // Return PDF download
            if ($pdfPath && Storage::disk('local')->exists($pdfPath)) {
                return Storage::disk('local')->download($pdfPath, $pdfFilename);
            } else {
                throw new \Exception('PDF datoteka ni bila najdena.');
            }

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Error creating specification: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Generate PDF for bill and specification
     *
     * @param \App\Models\Bill $bill
     * @param PDFService $pdfService
     * @param string $dateFrom
     * @param string $dateTo
     * @return string File path
     */
    protected function generateBillPdf($bill, $pdfService, $dateFrom, $dateTo)
    {
        // Load all necessary relationships
        $bill->load([
            'entity',
            'compenzations.realizationAgreement',
            'compenzations' => function($query) {
                $query->orderBy('date_payed', 'asc');
            }
        ]);

        $pdf = $pdfService->generateFromView(
            'pdfs.bill-specification',
            [
                'bill' => $bill,
                'dateFrom' => $dateFrom,
                'dateTo' => $dateTo,
            ]
        );

        $filename = "racun_{$bill->id}_{$bill->year}.pdf";
        $filePath = "bills/{$filename}";

        Storage::disk('local')->put($filePath, $pdf->output());

        return $filePath;
    }

    /**
     * Download bill PDF
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function downloadBillPdf($id)
    {
        $bill = $this->billService->bill($id);
        
        if (!$bill) {
            abort(404);
        }

        $filename = "racun_{$bill->id}_{$bill->year}.pdf";
        $filePath = "bills/{$filename}";

        if (!Storage::disk('local')->exists($filePath)) {
            abort(404, 'PDF datoteka ne obstaja.');
        }

        return Storage::disk('local')->download($filePath, $filename);
    }
}

