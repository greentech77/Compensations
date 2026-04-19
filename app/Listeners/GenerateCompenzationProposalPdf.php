<?php

namespace App\Listeners;

use App\Services\Compenzations\Events\AddCompenzationEvent;
use App\Services\PDF\PDFService;
use App\Models\CompenzationProposal;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class GenerateCompenzationProposalPdf
{
    protected $pdfService;

    /**
     * Create the event listener.
     */
    public function __construct(PDFService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    /**
     * Handle the event.
     */
    public function handle(AddCompenzationEvent $event): void
    {
        try {
            $compenzation = $event->model;
            
            // Load relationships
            $compenzation->load([
                'compenzationEntity.entity',
                'implementationAgreement',
                'realizationAgreement',
                'proposal'
            ]);

            // 1. Generate Compenzation Proposal PDF
            $this->generateProposalPdf($compenzation);

            // 2. Generate Implementation Agreement PDF
            if ($compenzation->implementationAgreement) {
                $this->generateImplementationAgreementPdf($compenzation);
            }

            // 3. Generate Realization Agreement PDF
            if ($compenzation->realizationAgreement) {
                $this->generateRealizationAgreementPdf($compenzation);
            }

            Log::info("All PDFs generated successfully for compenzation {$compenzation->id}");

        } catch (\Exception $e) {
            Log::error("Failed to generate PDFs for compenzation {$event->model->id}: " . $e->getMessage());
            // Don't throw exception to prevent breaking the flow
        }
    }

    /**
     * Generate Compenzation Proposal PDF
     */
    protected function generateProposalPdf($compenzation): void
    {
        $pdf = $this->pdfService->generateFromView(
            'pdfs.compenzation-proposal',
            ['compenzation' => $compenzation]
        );

        $filename = "kompenzacija{$compenzation->id}_{$compenzation->year}.pdf";
        $filePath = "proposals/{$filename}";

        Storage::disk('local')->put($filePath, $pdf->output());

        // Update CompenzationProposal with file path
        $proposal = CompenzationProposal::where('id_compenzation', $compenzation->id)->first();
        if ($proposal) {
            $proposal->update([
                'file_path' => $filePath,
                'file_name' => $filename
            ]);
        }

        Log::info("Proposal PDF generated: {$filePath}");
    }

    /**
     * Generate Implementation Agreement PDF
     */
    protected function generateImplementationAgreementPdf($compenzation): void
    {
        $pdf = $this->pdfService->generateFromView(
            'pdfs.implementation-agreement',
            [
                'compenzation' => $compenzation,
                'agreement' => $compenzation->implementationAgreement
            ]
        );

        $filename = "pogodba_o_izvedbi{$compenzation->id}_{$compenzation->year}.pdf";
        $filePath = "agreements/implementation/{$filename}";

        Storage::disk('local')->put($filePath, $pdf->output());

        // Update ImplementationAgreement with file path
        if ($compenzation->implementationAgreement) {
            $compenzation->implementationAgreement->update([
                'file_path' => $filePath,
                'file_name' => $filename
            ]);
        }

        Log::info("Implementation Agreement PDF generated: {$filePath}");
    }

    /**
     * Generate Realization Agreement PDF
     */
    protected function generateRealizationAgreementPdf($compenzation): void
    {
        $pdf = $this->pdfService->generateFromView(
            'pdfs.realization-agreement',
            [
                'compenzation' => $compenzation,
                'agreement' => $compenzation->realizationAgreement
            ]
        );

        $filename = "pogodba_o_unovcenju{$compenzation->id}_{$compenzation->year}.pdf";
        $filePath = "agreements/realization/{$filename}";

        Storage::disk('local')->put($filePath, $pdf->output());

        // Update RealizationAgreement with file path
        if ($compenzation->realizationAgreement) {
            $compenzation->realizationAgreement->update([
                'file_path' => $filePath,
                'file_name' => $filename
            ]);
        }

        Log::info("Realization Agreement PDF generated: {$filePath}");
    }
}
