<?php

namespace App\Services;

use Illuminate\Contracts\View\Factory as ViewFactory;

class PdfGenerator
{
    public function loadView(string $view, array $data = []): object
    {
        return app('dompdf.wrapper')->loadView($view, $data);
    }
}
