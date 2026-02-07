<?php

namespace App\Controllers;

use App\Models\Udalost;
use App\Models\UdalostUcast;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

class UdalostUserController extends UserBaseController
{
    /**
     * Povinná metóda kvôli UserBaseController.
     * User nemá samostatný zoznam udalostí → presmerujeme na rozvrh.
     * @param Request $request
     */
    public function index(Request $request): Response
    {
        return $this->redirect($this->url('rozvrhUser.index'));
    }

    public function show(Request $request): Response
    {
        $activeOsoba = $this->requireActiveOsoba();
        $idObdobie = (int)$this->requireActiveObdobieId();

        $idUdalost = (int)$request->get('id_udalost');
        if ($idUdalost <= 0) {
            return $this->redirect($this->url('rozvrhUser.index'));
        }

        $udalost = Udalost::getOneForOsoba(
            $idUdalost,
            (int)$activeOsoba->getId(),
            $idObdobie
        );

        if ($udalost === null) {
            // ochrana: user sa snaží dostať k cudzej udalosti
            return $this->redirect($this->url('rozvrhUser.index'));
        }

        $stav = UdalostUcast::getStav($idUdalost, (int)$activeOsoba->getId()); // null|'ucast'|'neucast'

        return $this->html([
            'udalost' => $udalost,
            'activeOsoba' => $activeOsoba,
            'stavUcasti' => $stav,
        ]);
    }
}
