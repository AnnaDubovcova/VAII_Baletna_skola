<?php

namespace App\Controllers;

use App\Models\Prispevok;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

class PrispevokController extends AppController
{
    /**
     * Verejný controller – povolený pre každého.
     */
    public function authorize(Request $request, string $action): bool
    {
        return true;
    }

    /**
     * Verejný výpis oznamov.
     */
    public function index(Request $request): Response
    {
        $prispevky = Prispevok::getPublic();

        return $this->html([
            'prispevky' => $prispevky,
        ]);
    }
}
