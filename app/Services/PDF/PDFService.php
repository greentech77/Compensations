<?php

namespace App\Services\PDF;

use Mpdf\Mpdf;
use App\Services\PDF\PDF;
use Illuminate\Support\Facades\View;

class PDFService {

    /**
     * Generira PDF
     *
     * @return PDFFacade
     */
    public function generate($html, $overrides = []) {

        $mpdf = new Mpdf([
            'tempDir' => $this->resolveTempDir(),
            'default_font' => 'dejavusans',
            'margin_left' => 15,
            'margin_right' => 15,
            'margin_top' => 6,
            'margin_bottom' => 20,
            'margin_header' => 0,
            'setAutoBottomMargin' => 'pad',
            'autoMarginPadding' => 5,
            ...$overrides
        ]);

        $mpdf->SetDisplayMode('fullpage');

        $mpdf->WriteHTML($html);

        return new PDF($mpdf);
    }

    /**
     * Keep mPDF's scratch files inside `storage/` instead of the default
     * `vendor/mpdf/mpdf/tmp/`. The vendor path is wiped on every
     * `composer install` and, more importantly, ends up owned by whichever
     * user first ran a PDF job (often `root` via an artisan backfill),
     * which then locks the web-server user out and silently breaks all
     * future PDF generation.
     */
    private function resolveTempDir(): string
    {
        $tempDir = storage_path('app/mpdf-tmp');

        if (!is_dir($tempDir)) {
            @mkdir($tempDir, 0775, true);
        }

        return $tempDir;
    }

    /**
     * Generira PDF iz viewa
     *
     * @return PDFFacade
     */
    public function generateFromView($view, $data, $overrides = []) {
        $view = View::make($view, $data);
        $html = $view->render();
        return $this->generate($html, $overrides);
    }

}