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

        $mode = (string)($request->get('mode') ?? 'global');
        $returnTo = $request->get('return_to');
        if (!is_string($returnTo) || trim($returnTo) === '') {
            $returnTo = null;
        }

        $contextParams = [];
        $skupina = null;
        $udalost = null;

        // --- FILTER: skupina ---
        if ($mode === 'skupina') {
            $idSkupina = (int)$request->get('id_skupina');
            if ($idSkupina <= 0) {
                return $this->redirect($this->url('prispevokUser.index'));
            }

            $skupina = Skupina::getOne($idSkupina);
            if ($skupina === null) {
                throw new \Exception('Skupina nenájdená.');
            }

            // ochrana: user musí byť členom skupiny
            $membership = $skupina->hasMember($idOsoba);
            if (empty($membership)) {
                return $this->redirect($this->url('prispevokUser.index'));
            }

            // príspevky len pre túto skupinu
            $prispevky = Prispevok::getAll(
                "viditelnost = 'skupina' AND id_skupina = :s",
                ['s' => $idSkupina],
                'created_at DESC'
            );

            $contextParams = ['mode' => 'skupina', 'id_skupina' => $idSkupina];
        }
        // --- FILTER: udalosť ---
        elseif ($mode === 'udalost') {
            $idUdalost = (int)$request->get('id_udalost');
            if ($idUdalost <= 0) {
                return $this->redirect($this->url('prispevokUser.index'));
            }

            // ochrana: user musí mať k udalosti prístup (cez Udalost::getOneForOsoba)
            $uRow = Udalost::getOneForOsoba($idUdalost, $idOsoba, $idObdobie);
            if ($uRow === null) {
                return $this->redirect($this->url('prispevokUser.index'));
            }

            // aby si mala názov udalosti vo view, načítame aj model (ak chceš, môžeš si spraviť helper “getNazovForOsoba”)
            $udalost = Udalost::getOne($idUdalost);
            if ($udalost === null) {
                throw new \Exception('Udalosť nenájdená.');
            }

            $prispevky = Prispevok::getAll(
                "viditelnost = 'udalost' AND id_udalost = :u",
                ['u' => $idUdalost],
                'created_at DESC'
            );

            $contextParams = ['mode' => 'udalost', 'id_udalost' => $idUdalost];
        }
        // --- DEFAULT: všetky non-public pre osobu ---
        else {
            $mode = 'global';
            $prispevky = Prispevok::getAllForOsobaNonPublic($idOsoba, $idObdobie);
            $contextParams = [];
        }

        // mapy (na badge texty) – hodí sa v global režime
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

        // selfUrl (dôležité pre “Späť” a return_to)
        $selfUrlParams = $contextParams;
        if (!empty($returnTo)) {
            $selfUrlParams['return_to'] = $returnTo;
        }
        $selfUrl = $this->url('prispevokUser.index', $selfUrlParams);

        return $this->html([
            'prispevky' => $prispevky,
            'activeOsoba' => $activeOsoba,
            'skupinaMap' => $skupinaMap,
            'udalostMap' => $udalostMap,

            'mode' => $mode,
            'contextParams' => $contextParams,
            'skupina' => $skupina,
            'udalost' => $udalost,
            'returnTo' => $returnTo,
            'selfUrl' => $selfUrl,
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

        $returnTo = $request->value('return_to');
        if (!is_string($returnTo) || trim($returnTo) === '' || strpos($returnTo, '://') !== false || strpos($returnTo, '//') === 0) {
            $returnTo = null;
        }

        return $this->html([
            'prispevok' => $p,
            'activeOsoba' => $activeOsoba,
            'subory' => $subory,
            'returnTo' => $returnTo,
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

    private function getFilterType(Request $request): ?string
    {
        $t = $request->get('filter');
        if (!is_string($t)) return null;
        $t = trim($t);
        return in_array($t, ['skupina', 'udalost'], true) ? $t : null;
    }

    private function userHasSkupina(int $idOsoba, int $idSkupina, int $idObdobie): bool
    {
        // user môže vidieť príspevky skupiny len ak je v nej a skupina je v aktívnom období
        $rows = Skupina::getAll(
            'id_skupina = :s AND id_obdobie = :o AND EXISTS (
            SELECT 1 FROM osoba_skupina os
            WHERE os.id_skupina = :s AND os.id_osoba = :os
        )',
            ['s' => $idSkupina, 'o' => $idObdobie, 'os' => $idOsoba],
            null,
            1
        );
        return !empty($rows);
    }

}
