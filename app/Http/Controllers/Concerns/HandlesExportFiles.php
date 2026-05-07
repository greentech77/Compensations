<?php

namespace App\Http\Controllers\Concerns;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * Shared helpers for controllers that persist generated export files
 * (e.g. OpPIS XML for bills/contracts/compenzations) and serve them
 * for re-download from the corresponding export pages.
 */
trait HandlesExportFiles
{
    /**
     * Build a sorted list (newest first) of previously generated export files
     * inside a directory on the `local` disk.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function listExportFiles(string $directory, string $downloadRouteName): array
    {
        if (!Storage::disk('local')->exists($directory)) {
            return [];
        }

        return collect(Storage::disk('local')->files($directory))
            ->map(function (string $path) use ($downloadRouteName) {
                $filename = basename($path);
                $modifiedAt = Storage::disk('local')->lastModified($path);

                return [
                    'name' => $filename,
                    'size' => Storage::disk('local')->size($path),
                    'modified_at' => $modifiedAt,
                    'modified_at_human' => Carbon::createFromTimestamp($modifiedAt)->format('d.m.Y H:i'),
                    'url' => route($downloadRouteName, ['filename' => $filename]),
                ];
            })
            ->sortByDesc('modified_at')
            ->values()
            ->all();
    }

    /**
     * Persist a generated XML export to disk so it can be re-downloaded later
     * from the matching export listing.
     */
    protected function persistExportFile(string $directory, string $filename, string $contents): void
    {
        Storage::disk('local')->put($directory.'/'.$filename, $contents);
    }

    /**
     * Securely stream a previously generated export file back to the user.
     *
     * Filename is constrained at the route level via a regex `where`, but we
     * still defensively validate against directory-traversal before reading.
     */
    protected function downloadExportFile(string $directory, string $filename)
    {
        if ($filename === '' || $filename !== basename($filename) || str_contains($filename, '..')) {
            abort(404);
        }

        $path = $directory.'/'.$filename;

        if (!Storage::disk('local')->exists($path)) {
            abort(404, 'Datoteka izvoza ne obstaja.');
        }

        return Storage::disk('local')->download($path, $filename, [
            'Content-Type' => 'application/xml; charset=UTF-8',
        ]);
    }
}
