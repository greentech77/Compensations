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
            'default_font' => 'dejavusans',
            'margin_left' => 22,
            'margin_right' => 22,
            'margin_top' => 35,
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