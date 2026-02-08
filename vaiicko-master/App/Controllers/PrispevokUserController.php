<?php

namespace App\Controllers;

use App\Models\Prispevok;
use App\Models\Skupina;
use App\Models\Udalost;
use Framework\Http\Request;
use Framework\Http\Responses\Response;
use App\Models\PrispevokSubor;


class PrispevokUserController extends UserBaseController
{
    public function index(Request $request): Response
    {
        $activeOsoba = $this->requireActiveOsoba();
        if ($activeOsoba === null) {
            return $this->redirect($this->url('osoba.index'));
        }

        $idOsoba = (int)$activeOsoba->getId();
        $idObdobie = (int)$this->requireActiveObdobieId();

        $prispevky = Prispevok::getAllForOsobaNonPublic($idOsoba, $idObdobie);


        // Pre pekné zobrazenie: názvy skupín/udalostí (bez N+1)
        $skupinaIds = [];
        $udalostIds = [];

        foreach ($prispevky as $p) {
            if ($p->getViditelnost() === 'skupina' && $p->getIdSkupina()) {
                $skupinaIds[] = (int)$p->getIdSkupina();
            }
            if ($p->getViditelnost() === 'udalost' && $p->getIdUdalost()) {
                $udalostIds[] = (int)$p->getIdUdalost();
            }
        }

        $skupinaMap = $this->loadSkupinaMap($skupinaIds);
        $udalostMap = $this->loadUdalostMap($udalostIds);

        return $this->html([
            'prispevky' => $prispevky,
            'activeOsoba' => $activeOsoba,
            'skupinaMap' => $skupinaMap,
            'udalostMap' => $udalostMap,
        ]);
    }

    public function show(Request $request): Response
    {
        $activeOsoba = $this->requireActiveOsoba();
        if ($activeOsoba === null) {
            return $this->redirect($this->url('osoba.index'));
        }

        $idOsoba = (int)$activeOsoba->getId();
        $idObdobie = (int)$this->requireActiveObdobieId();

        $id = (int)$request->value('id_prispevok');
        if ($id <= 0) {
            return $this->redirect($this->url('prispevokUser.index'));
        }

        $p = Prispevok::getOneForOsoba($id, $idOsoba, $idObdobie);
        if ($p === null) {
            // ochrana: user sa snaží dostať k cudziemu príspevku
            return $this->redirect($this->url('prispevokUser.index'));
        }

        $subory = PrispevokSubor::getAllForPrispevok((int)$p->getId());

        return $this->html([
            'prispevok' => $p,
            'activeOsoba' => $activeOsoba,
            'subory' => $subory,
        ]);

    }

    private function loadSkupinaMap(array $ids): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if (empty($ids)) {
            return [];
        }

        // bezpečne cez placeholders
        $placeholders = [];
        $params = [];
        foreach ($ids as $i => $id) {
            $k = 's' . $i;
            $placeholders[] = ':' . $k;
            $params[$k] = $id;
        }

        $rows = Skupina::getAll(
            'id_skupina IN (' . implode(',', $placeholders) . ')',
            $params,
            'nazov ASC'
        );


        $map = [];
        foreach ($rows as $s) {
            $map[(int)$s->getId()] = (string)$s->getNazov();
        }
        return $map;
    }

    private function loadUdalostMap(array $ids): array
    {
        $ids = array_values(array_unique(array_filter(array_map('intval', $ids))));
        if (empty($ids)) {
            return [];
        }

        $placeholders = [];
        $params = [];
        foreach ($ids as $i => $id) {
            $k = 'u' . $i;
            $placeholders[] = ':' . $k;
            $params[$k] = $id;
        }
        $rows = Udalost::getAll(
            'id_udalost IN (' . implode(',', $placeholders) . ')',
            $params,
            'zaciatok DESC'
        );


        $map = [];
        foreach ($rows as $u) {
            $map[(int)$u->getId()] = (string)$u->getNazov();
        }
        return $map;
    }
}
