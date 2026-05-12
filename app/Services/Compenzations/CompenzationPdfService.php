<?php

namespace App\Services\Compenzations;

use App\Models\Compenzation;
use App\Models\CompenzationProposal;
use App\Services\PDF\PDFService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

/**
 * Generates and persists the three PDF documents tied to a compenzation:
 *   - proposal           (predlog kompenzacije)
 *   - implementation     (pogodba o izvedbi)
 *   - realization        (pogodba o unovčenju)
 *
 * The same logic is consumed by:
 *   - the AddCompenzationEvent listener (on create / update),
 *   - the on-demand controller fallback (when a download is requested but the
 *     file is missing on disk, e.g. for compenzations created before the
 *     listener was wired up),
 *   - the `compenzations:regenerate-pdfs` Artisan command (bulk backfill).
 */
class CompenzationPdfService
{
    public function __construct(private PDFService $pdfService)
    {
    }

    /**
     * Regenerate all three PDFs for a compenzation. Failures are recorded per
     * document so that one broken template never blocks the others.
     *
     * Returns a per-type report:
     *   [
     *     'proposal'       => ['status' => 'ok'|'skipped'|'failed', 'path' => ?string, 'error' => ?string],
     *     'implementation' => [...],
     *     'realization'    => [...],
     *   ]
     *
     * @return array<string, array<string, mixed>>
     */
    public function generateAll(Compenzation $compenzation): array
    {
        $compenzation->load([
            'compenzationEntity.entity',
            'implementationAgreement',
            'realizationAgreement',
            'proposal',
        ]);

        $report = [
            'proposal' => $this->safeRun('proposal', $compenzation, fn () => $this->generateProposal($compenzation)),
            'implementation' => ['status' => 'skipped', 'path' => null, 'error' => null],
            'realization' => ['status' => 'skipped', 'path' => null, 'error' => null],
        ];

        if ($compenzation->implementationAgreement && $compenzation->implementationAgreement->exists) {
            $report['implementation'] = $this->safeRun('implementation', $compenzation, fn () => $this->generateImplementation($compenzation));
        }

        if ($compenzation->realizationAgreement && $compenzation->realizationAgreement->exists) {
            $report['realization'] = $this->safeRun('realization', $compenzation, fn () => $this->generateRealization($compenzation));
        }

        return $report;
    }

    /**
     * Generate exactly one of the three PDF types and return its storage path.
     *
     * @param  string  $type  one of: proposal | implementation | realization
     */
    public function generateOne(Compenzation $compenzation, string $type): string
    {
        $compenzation->load([
            'compenzationEntity.entity',
            'implementationAgreement',
            'realizationAgreement',
            'proposal',
        ]);

        return match ($type) {
            'proposal' => $this->generateProposal($compenzation),
            'implementation' => $this->generateImplementation($compenzation),
            'realization' => $this->generateRealization($compenzation),
            default => throw new \InvalidArgumentException("Unknown PDF type: {$type}"),
        };
    }

    /**
     * Resolve the storage path for a given PDF type, regenerating it if the
     * file is missing on disk. Returns null only if the underlying agreement
     * row does not exist (e.g. realization is optional in some flows).
     */
    public function resolvePath(Compenzation $compenzation, string $type): ?string
    {
        [$relation, $defaultDir] = match ($type) {
            'proposal' => ['proposal', 'proposals'],
            'implementation' => ['implementationAgreement', 'agreements/implementation'],
            'realization' => ['realizationAgreement', 'agreements/realization'],
            default => throw new \InvalidArgumentException("Unknown PDF type: {$type}"),
        };

        $compenzation->loadMissing([$relation]);
        $row = $compenzation->{$relation};

        if (!$row || ($type !== 'proposal' && !$row->exists)) {
            return null;
        }

        $path = $row->file_path ?? null;

        if ($path && Storage::disk('local')->exists($path)) {
            return $path;
        }

        return $this->generateOne($compenzation, $type);
    }

    private function generateProposal(Compenzation $compenzation): string
    {
        $pdf = $this->pdfService->generateFromView(
            'pdfs.compenzation-proposal',
            ['compenzation' => $compenzation]
        );

        $filename = "kompenzacija{$compenzation->id}_{$compenzation->year}.pdf";
        $filePath = "proposals/{$filename}";

        // Delete before write so that files created by root (via artisan) can be replaced
        // by the web-server user. Deletion only requires directory write permission.
        Storage::disk('local')->delete($filePath);
        Storage::disk('local')->put($filePath, $pdf->output());

        $proposal = CompenzationProposal::firstOrNew(['id_compenzation' => $compenzation->id]);
        $proposal->fill([
            'file_path' => $filePath,
            'file_name' => $filename,
        ])->save();

        Log::info("Compenzation proposal PDF generated: {$filePath}");

        return $filePath;
    }

    private function generateImplementation(Compenzation $compenzation): string
    {
        $pdf = $this->pdfService->generateFromView(
            'pdfs.implementation-agreement',
            [
                'compenzation' => $compenzation,
                'agreement' => $compenzation->implementationAgreement,
            ]
        );

        $filename = "pogodba_o_izvedbi{$compenzation->id}_{$compenzation->year}.pdf";
        $filePath = "agreements/implementation/{$filename}";

        Storage::disk('local')->delete($filePath);
        Storage::disk('local')->put($filePath, $pdf->output());

        $compenzation->implementationAgreement->update([
            'file_path' => $filePath,
            'file_name' => $filename,
        ]);

        Log::info("Implementation Agreement PDF generated: {$filePath}");

        return $filePath;
    }

    private function generateRealization(Compenzation $compenzation): string
    {
        $pdf = $this->pdfService->generateFromView(
            'pdfs.realization-agreement',
            [
                'compenzation' => $compenzation,
                'agreement' => $compenzation->realizationAgreement,
            ]
        );

        $filename = "pogodba_o_unovcenju{$compenzation->id}_{$compenzation->year}.pdf";
        $filePath = "agreements/realization/{$filename}";

        Storage::disk('local')->delete($filePath);
        Storage::disk('local')->put($filePath, $pdf->output());

        $compenzation->realizationAgreement->update([
            'file_path' => $filePath,
            'file_name' => $filename,
        ]);

        Log::info("Realization Agreement PDF generated: {$filePath}");

        return $filePath;
    }

    /**
     * Run a single generation step and capture its result + any failure.
     *
     * @return array{status: 'ok'|'failed', path: ?string, error: ?string}
     */
    private function safeRun(string $type, Compenzation $compenzation, \Closure $callback): array
    {
        try {
            $path = $callback();

            return ['status' => 'ok', 'path' => $path, 'error' => null];
        } catch (\Throwable $e) {
            $message = $e->getMessage().' @ '.$e->getFile().':'.$e->getLine();
            Log::error("PDF generation failed [{$type}] for compenzation {$compenzation->id}: ".$message, [
                'exception' => $e,
            ]);

            return ['status' => 'failed', 'path' => null, 'error' => $message];
        }
    }
}
