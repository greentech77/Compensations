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
        $compenzationId = $event->model->id;

        try {
            $report = $this->pdfs->generateAll($event->model);
        } catch (\Throwable $e) {
            // generateAll() is supposed to swallow per-type failures, but
            // guard against unexpected fatals (DB lookup, missing model, ...).
            Log::error("Failed to generate PDFs for compenzation {$compenzationId}: ".$e->getMessage());
            return;
        }

        $okTypes = [];
        $failedTypes = [];
        $skippedTypes = [];

        foreach ($report as $type => $result) {
            switch ($result['status'] ?? null) {
                case 'ok':
                    $okTypes[] = $type;
                    break;
                case 'failed':
                    $failedTypes[] = $type;
                    Log::error("PDF [{$type}] failed for compenzation {$compenzationId}: ".($result['error'] ?? 'unknown error'));
                    break;
                case 'skipped':
                    $skippedTypes[] = $type;
                    break;
            }
        }

        if (!empty($failedTypes)) {
            Log::warning(sprintf(
                'PDF generation partial for compenzation %d: ok=[%s] failed=[%s] skipped=[%s]',
                $compenzationId,
                implode(',', $okTypes),
                implode(',', $failedTypes),
                implode(',', $skippedTypes)
            ));
            return;
        }

        Log::info(sprintf(
            'All PDFs generated for compenzation %d (ok=[%s] skipped=[%s])',
            $compenzationId,
            implode(',', $okTypes),
            implode(',', $skippedTypes)
        ));
    }
}
