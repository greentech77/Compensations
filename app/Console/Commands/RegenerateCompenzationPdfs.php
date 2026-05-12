<?php

namespace App\Console\Commands;

use App\Models\Bill;
use App\Models\Compenzation;
use App\Services\Bills\BillService;
use App\Services\Compenzations\CompenzationPdfService;
use App\Services\Compenzations\CompenzationService;
use App\Services\PDF\PDFService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class RegenerateCompenzationPdfs extends Command
{
    /**
     * Re-generate PDFs for compenzations (proposal, implementation, realization)
     * and bills (bill-specification). Used to backfill records or refresh
     * files after template changes.
     */
    protected $signature = 'compenzations:regenerate-pdfs
                            {--id= : Regenerate PDFs only for the compenzation with this ID}
                            {--bill-id= : Regenerate PDF only for the bill with this ID}
                            {--missing-only : Only regenerate when at least one PDF is missing on disk}
                            {--create-missing-rows : Create implementation/realization agreement rows if missing in DB before generating PDFs}
                            {--skip-bills : Skip bill PDF regeneration}
                            {--bills-only : Only regenerate bill PDFs, skip compenzation PDFs}';

    protected $description = 'Regenerate proposal/implementation/realization PDFs for compenzations and bill PDFs.';

    public function handle(CompenzationPdfService $pdfs, CompenzationService $service, PDFService $pdfService): int
    {
        $disk = Storage::disk('local');
        $diskRoot = method_exists($disk, 'path') ? $disk->path('') : storage_path('app');
        $this->line('Storage disk root: '.$diskRoot);

        $billsOnly = (bool) $this->option('bills-only');
        $skipBills = (bool) $this->option('skip-bills');
        $overallFailed = 0;

        // ── Kompenzacije ──────────────────────────────────────────────────────
        if (!$billsOnly) {
            $overallFailed += $this->regenerateCompenzations($pdfs, $service, $disk);
        }

        // ── Računi ────────────────────────────────────────────────────────────
        if (!$skipBills) {
            $overallFailed += $this->regenerateBills($pdfService, $disk);
        }

        return ($overallFailed === 0) ? self::SUCCESS : self::FAILURE;
    }

    private function regenerateCompenzations(CompenzationPdfService $pdfs, CompenzationService $service, $disk): int
    {
        $query = Compenzation::query()->with([
            'proposal',
            'implementationAgreement',
            'realizationAgreement',
            'compenzationEntity.entity',
        ]);

        if ($id = $this->option('id')) {
            $query->whereKey($id);
        }

        $missingOnly    = (bool) $this->option('missing-only');
        $createMissing  = (bool) $this->option('create-missing-rows');
        $compenzations  = $query->orderBy('id')->get();

        if ($compenzations->isEmpty()) {
            $this->warn('Ni najdenih kompenzacij za regeneracijo.');
            return 0;
        }

        $regenerated = 0;
        $partial     = 0;
        $skippedAll  = 0;
        $failed      = 0;
        $errors      = [];
        $rows        = [];

        $shortStatus = [
            'ok'      => 'OK',
            'failed'  => 'NAPAKA',
            'skipped' => 'preskočeno (ni zapisa)',
        ];

        foreach ($compenzations as $compenzation) {
            try {
                if ($missingOnly && !$this->isAnyMissing($compenzation)) {
                    $skippedAll++;
                    $rows[] = ["#{$compenzation->id}", 'preskočeno (vse že obstaja)', '-', '-'];
                    continue;
                }

                if ($createMissing) {
                    foreach ($this->ensureAgreementRows($compenzation, $service) as $type) {
                        $this->line("  ↳ #{$compenzation->id}: ustvarjen manjkajoči zapis '{$type}'");
                    }
                }

                $report   = $pdfs->generateAll($compenzation);
                $okCount  = 0;
                $failCount = 0;

                foreach ($report as $type => $r) {
                    if ($r['status'] === 'ok') {
                        $okCount++;
                    } elseif ($r['status'] === 'failed') {
                        $failCount++;
                        $errors[] = "#{$compenzation->id} {$type}: {$r['error']}";
                    }
                }

                $rows[] = [
                    "#{$compenzation->id}",
                    $shortStatus[$report['proposal']['status']] ?? $report['proposal']['status'],
                    $shortStatus[$report['implementation']['status']] ?? $report['implementation']['status'],
                    $shortStatus[$report['realization']['status']] ?? $report['realization']['status'],
                ];

                if ($failCount === 0 && $okCount > 0) {
                    $regenerated++;
                } elseif ($okCount > 0) {
                    $partial++;
                } else {
                    $failed++;
                }
            } catch (\Throwable $e) {
                $failed++;
                $errors[] = "#{$compenzation->id} fatal: ".$e->getMessage().' @ '.$e->getFile().':'.$e->getLine();
                $rows[] = ["#{$compenzation->id}", 'FATAL', 'FATAL', 'FATAL'];
            }
        }

        $this->newLine();
        $this->info('── Kompenzacije ──');
        $this->table(['ID', 'Predlog', 'Pogodba o izvedbi', 'Pogodba o unovčenju'], $rows);
        $this->info("V celoti regenerirano: {$regenerated}");
        if ($partial)   { $this->warn("Delno regenerirano: {$partial}"); }
        if ($skippedAll){ $this->line("Preskočenih (vsi PDF-ji prisotni): {$skippedAll}"); }
        if ($failed)    { $this->error("Neuspelih: {$failed}"); }
        if (!empty($errors)) {
            $this->newLine();
            $this->error('Napake (kompenzacije):');
            foreach ($errors as $err) { $this->line('  - '.$err); }
        }

        // Sanity check on first compenzation
        $first = $compenzations->first();
        $proposalPath = "proposals/kompenzacija{$first->id}_{$first->year}.pdf";
        $exists   = $disk->exists($proposalPath);
        $absolute = method_exists($disk, 'path') ? $disk->path($proposalPath) : storage_path('app/'.$proposalPath);
        $this->newLine();
        $this->line('Vzorec: '.$proposalPath.' → '.($exists ? 'DA' : 'NE').' ('.$absolute.')');

        return $failed;
    }

    private function regenerateBills(PDFService $pdfService, $disk): int
    {
        $query = Bill::with([
            'entity',
            'compenzations' => fn ($q) => $q->orderBy('date_payed', 'asc'),
            'compenzations.realizationAgreement',
        ]);

        if ($billId = $this->option('bill-id')) {
            $query->whereKey($billId);
        }

        $bills = $query->orderBy('id')->get();

        if ($bills->isEmpty()) {
            $this->warn('Ni najdenih računov za regeneracijo.');
            return 0;
        }

        $regenerated = 0;
        $failed      = 0;
        $errors      = [];
        $rows        = [];

        foreach ($bills as $bill) {
            try {
                $compenzations = $bill->compenzations;
                $dateFrom = $compenzations->min('date_payed') ?? $bill->date;
                $dateTo   = $compenzations->max('date_payed') ?? $bill->date;

                $pdf      = $pdfService->generateFromView('pdfs.bill-specification', [
                    'bill'     => $bill,
                    'dateFrom' => $dateFrom,
                    'dateTo'   => $dateTo,
                ]);

                $filename = "racun_{$bill->id}_{$bill->year}.pdf";
                $filePath = "bills/{$filename}";

                $disk->delete($filePath);
                $disk->put($filePath, $pdf->output());

                $regenerated++;
                $rows[] = ["#{$bill->id}", $bill->entity->company_name ?? '—', 'OK'];
            } catch (\Throwable $e) {
                $failed++;
                $errors[] = "#{$bill->id} fatal: ".$e->getMessage().' @ '.$e->getFile().':'.$e->getLine();
                $rows[] = ["#{$bill->id}", $bill->entity->company_name ?? '—', 'NAPAKA'];
            }
        }

        $this->newLine();
        $this->info('── Računi ──');
        $this->table(['ID', 'Stranka', 'Status'], $rows);
        $this->info("Regeneriranih računov: {$regenerated}");
        if ($failed) { $this->error("Neuspelih računov: {$failed}"); }
        if (!empty($errors)) {
            $this->newLine();
            $this->error('Napake (računi):');
            foreach ($errors as $err) { $this->line('  - '.$err); }
        }

        return $failed;
    }

    /**
     * Backfill missing implementation_agreement / realization_agreement rows
     * for a compenzation using sensible defaults derived from the compenzation
     * itself. Only acts when the row is genuinely missing in DB; existing rows
     * are never modified.
     *
     * @return array<int, string> List of relation names that were created.
     */
    private function ensureAgreementRows(Compenzation $compenzation, CompenzationService $service): array
    {
        $created = [];

        // `withDefault()` on the relation returns an "empty" model when the
        // row does not exist, so we must check `->exists` (not just truthiness).
        if (!$compenzation->implementationAgreement || !$compenzation->implementationAgreement->exists) {
            $service->insertImplementationAgreement(
                $compenzation->id,
                $compenzation->amount ?? 0,
                0,                         // discount % – unknown for old records
                (bool) ($compenzation->with_ddv ?? false)
            );
            $created[] = 'implementation_agreement';
        }

        if (!$compenzation->realizationAgreement || !$compenzation->realizationAgreement->exists) {
            $service->insertRealizationAgreement(
                $compenzation->id,
                $compenzation->amount ?? 0,
                $compenzation->commission ?? 0
            );
            $created[] = 'realization_agreement';
        }

        if (!empty($created)) {
            // Refresh the relations so the subsequent generateAll() call sees the new rows.
            $compenzation->load(['implementationAgreement', 'realizationAgreement']);
        }

        return $created;
    }

    private function isAnyMissing(Compenzation $compenzation): bool
    {
        $disk = \Storage::disk('local');

        $proposalPath = $compenzation->proposal->file_path ?? null;
        if (!$proposalPath || !$disk->exists($proposalPath)) {
            return true;
        }

        if ($compenzation->implementationAgreement && $compenzation->implementationAgreement->exists) {
            $path = $compenzation->implementationAgreement->file_path;
            if (!$path || !$disk->exists($path)) {
                return true;
            }
        }

        if ($compenzation->realizationAgreement && $compenzation->realizationAgreement->exists) {
            $path = $compenzation->realizationAgreement->file_path;
            if (!$path || !$disk->exists($path)) {
                return true;
            }
        }

        return false;
    }
}
