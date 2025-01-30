<?php

namespace App\Services\PDF;

class PDF {

    private $mpdf;

    public function __construct($mpdf) {
        $this->mpdf = $mpdf;
    }

    public function output() {
        return $this->mpdf->OutputBinaryData();
    }

    public function download($name) {
        return $this->mpdf->OutputHttpDownload($name);
    }

    public function downloadInline() {
        return $this->mpdf->OutputHttpInline();
    }

}