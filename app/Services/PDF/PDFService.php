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
            'fontDir' => [
                storage_path('fonts')
            ],
            'fontdata' => [ 
                'roboto' => [
                    'R' => 'Roboto-Regular.ttf',
                    'I' => 'Roboto-Italic.ttf',
                    'L' => 'Roboto-Light.ttf',
                    'LI' => 'Roboto-LightItalic.ttf',
                    'B' => 'Roboto-Bold.ttf',
                    'BI' => 'Roboto-BoldItalic.ttf',
                ]
            ],
            'default_font' => 'roboto',
            'margin_left' => 22,
            'margin_right' => 22,
            'margin_top' => 35,
            'margin_bottom' => 20,
            'margin_header' => 0,
            'setAutoBottomMargin' => 'pad',
            'autoMarginPadding' => 5,
            ...$overrides
        ]);

        $mpdf->AddFontDirectory(storage_path('fonts'));
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