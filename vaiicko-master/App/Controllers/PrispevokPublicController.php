<?php

namespace App\Controllers;

use App\Models\Prispevok;
use App\Models\PrispevokSubor;

use Framework\Http\Request;
use Framework\Http\Responses\Response;

class PrispevokPublicController extends AppController
{
    public function index(Request $request): Response
    {
        $prispevky = Prispevok::getAllPublic();

        return $this->html([
            'prispevky' => $prispevky,
        ]);
    }

    public function show(Request $request): Response
    {
        $id = (int)$request->value('id_prispevok');
        if ($id <= 0) {
            return $this->redirect($this->url('prispevokPublic.index'));
        }

        $p = Prispevok::getOnePublic($id);
        if ($p === null) {
            return $this->redirect($this->url('prispevokPublic.index'));
        }

        $subory = PrispevokSubor::getAllForPrispevok((int)$p->getId());

        return $this->html([
            'prispevok' => $p,
            'subory' => $subory,
        ]);

    }
}
