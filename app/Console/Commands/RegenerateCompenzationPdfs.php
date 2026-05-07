<?php

namespace App\Console\Commands;

use App\Models\Compenzation;
use App\Services\Compenzations\CompenzationPdfService;
use App\Services\Compenzations\CompenzationService;
use Illuminate\Console\Command;

class RegenerateCompenzationPdfs extends Command
{
    /**
     * Re-generate the three PDF documents (proposal, implementation, realization)
     * for one or all compenzations. Used to backfill records that were created
     * before the AddCompenzationEvent listener was wired up, or whose stored
     * files have been removed from disk.
     */
    protected $signature = 'compenzations:regenerate-pdfs
                            {--id= : Regenerate PDFs only for the compenzation with this ID}
                            {--missing-only : Only regenerate when at least one PDF is missing on disk}
                            {--create-missing-rows : Create implementation/realization agreement rows if missing in DB before generating PDFs}';

    protected $description = 'Regenerate proposal/implementation/realization PDFs for one or all compenzations.';

    public function handle(CompenzationPdfService $pdfs, CompenzationService $service): int
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

        $missingOnly = (bool) $this->option('missing-only');
        $createMissingRows = (bool) $this->option('create-missing-rows');
        $compenzations = $query->orderBy('id')->get();

        if ($compenzations->isEmpty()) {
            $this->warn('Ni najdenih kompenzacij za regeneracijo.');

            return self::SUCCESS;
        }

        $regenerated = 0;
        $partial = 0;
        $skippedAll = 0;
        $failed = 0;
        $errors = [];
        $rows = [];

        $disk = \Storage::disk('local');
        $diskRoot = method_exists($disk, 'path') ? $disk->path('') : storage_path('app');
        $this->line('Storage disk root: '.$diskRoot);

        $shortStatus = [
            'ok' => 'OK',
            'failed' => 'NAPAKA',
            'skipped' => 'preskočeno (ni zapisa v bazi)',
        ];

        foreach ($compenzations as $compenzation) {
            try {
                if ($missingOnly && !$this->isAnyMissing($compenzation)) {
                    $skippedAll++;
                    $rows[] = [
                        "#{$compenzation->id}",
                        'preskočeno (vse že obstaja)',
                        '-',
                        '-',
                    ];
                    continue;
                }

                if ($createMissingRows) {
                    $created = $this->ensureAgreementRows($compenzation, $service);
                    foreach ($created as $type) {
                        $this->line("  ↳ #{$compenzation->id}: ustvarjen manjkajoči zapis v bazi za '{$type}'");
                    }
                }

                $report = $pdfs->generateAll($compenzation);
                $okCount = 0;
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
        $this->table(
            ['ID', 'Predlog', 'Pogodba o izvedbi', 'Pogodba o unovčenju'],
            $rows
        );

        $this->newLine();
        $this->info("V celoti regenerirano: {$regenerated}");
        if ($partial) {
            $this->warn("Delno regenerirano (en ali več PDF-jev je padel): {$partial}");
        }
        if ($missingOnly && $skippedAll) {
            $this->line("Preskočenih (vsi PDF-ji prisotni): {$skippedAll}");
        }
        if ($failed) {
            $this->error("Popolnoma neuspelih: {$failed}");
        }

        if (!empty($errors)) {
            $this->newLine();
            $this->error('Napake:');
            foreach ($errors as $err) {
                $this->line('  - '.$err);
            }
        }

        // Always show a sanity check by sampling the first compenzation we attempted
        $first = $compenzations->first();
        $proposalPath = "proposals/kompenzacija{$first->id}_{$first->year}.pdf";
        $exists = $disk->exists($proposalPath);
        $absolute = method_exists($disk, 'path') ? $disk->path($proposalPath) : storage_path('app/'.$proposalPath);
        $this->newLine();
        $this->line('Preverjam vzorec: '.$proposalPath);
        $this->line('  absolutna pot: '.$absolute);
        $this->line('  obstaja na disku: '.($exists ? 'DA' : 'NE'));

        return ($failed === 0 && $partial === 0) ? self::SUCCESS : self::FAILURE;
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
