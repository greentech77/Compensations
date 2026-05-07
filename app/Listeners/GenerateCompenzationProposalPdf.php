<?php

namespace App\Listeners;

use App\Services\Compenzations\CompenzationPdfService;
use App\Services\Compenzations\Events\AddCompenzationEvent;
use Illuminate\Support\Facades\Log;

class GenerateCompenzationProposalPdf
{
    public function __construct(private CompenzationPdfService $pdfs)
    {
    }

    public function handle(AddCompenzationEvent $event): void
    {
        try {
            $this->pdfs->generateAll($event->model);
            Log::info("All PDFs generated successfully for compenzation {$event->model->id}");
        } catch (\Throwable $e) {
            Log::error("Failed to generate PDFs for compenzation {$event->model->id}: ".$e->getMessage());
        }
    }
}
