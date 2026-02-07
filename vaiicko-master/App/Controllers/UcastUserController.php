<?php

namespace App\Controllers;

use App\Models\Udalost;
use App\Models\UdalostUcast;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

class UcastUserController extends UserBaseController
{

    /**
     * Povinné kvôli UserBaseController.
     * Účasť nemá vlastný index → presmerujeme na rozvrh.
     */
    public function index(Request $request): Response
    {
        return $this->redirect($this->url('rozvrhUser.index'));
    }

    public function react(Request $request): Response
    {
        if (!$request->isPost()) {
            return $this->redirect($this->url('rozvrhUser.index'));
        }

        $activeOsoba = $this->requireActiveOsoba();
        $idObdobie = (int)$this->requireActiveObdobieId();

        $idUdalost = (int)$request->value('id_udalost');
        $stav = (string)$request->value('stav'); // 'ucast'|'neucast'|'clear'

        if ($idUdalost <= 0) {
            return $this->redirect($this->url('rozvrhUser.index'));
        }

        // autorizácia: udalosť musí patriť userovi (cez skupiny) a byť v aktívnom období
        $udalost = Udalost::getOneForOsoba(
            $idUdalost,
            (int)$activeOsoba->getId(),
            $idObdobie
        );
        if ($udalost === null) {
            return $this->redirect($this->url('rozvrhUser.index'));
        }

        $vyzaduje = !empty($udalost['vyzaduje_reakciu']);

        if ($stav === 'clear') {
            // clear má význam len ak NEvyžaduje reakciu (výnimky)
            // pri "pozvánke" clear znamená "bez reakcie" => zmažeme záznam tiež
            UdalostUcast::deleteFor($idUdalost, (int)$activeOsoba->getId());
        } elseif ($stav === 'ucast' || $stav === 'neucast') {
            // ak vyžaduje reakciu => ukladáme odpoveď (ucast/neucast)
            // ak nevyžaduje reakciu => ukladáme len výnimku, takže 'ucast' vlastne znamená clear
            if (!$vyzaduje && $stav === 'ucast') {
                UdalostUcast::deleteFor($idUdalost, (int)$activeOsoba->getId());
            } else {
                UdalostUcast::setStav($idUdalost, (int)$activeOsoba->getId(), $stav);
            }
        }

        return $this->redirect($this->url('udalostUser.show', ['id_udalost' => $idUdalost]));
    }
}
