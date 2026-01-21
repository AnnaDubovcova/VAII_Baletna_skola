<?php

namespace App\Controllers;

use App\Models\Kurz;
use App\Models\Obdobie;
use App\Models\Osoba;
use App\Models\PrihlaskaKurz;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

class AdminPrihlaskaController extends AdminController
{
    public function index(Request $request): Response
    {
        $stav = trim((string)$request->value('stav'));
        $idObdobie = (int)$request->value('id_obdobie');

        $where = [];
        $params = [];

        if ($stav !== '') {
            $where[] = 'stav = :stav';
            $params['stav'] = $stav;
        }

        // filter podľa obdobia cez kurzy v období
        if ($idObdobie > 0) {
            $kurzyVObdobi = Kurz::getAll('id_obdobie = :o', ['o' => $idObdobie]);

            $ids = [];
            foreach ($kurzyVObdobi as $kk) {
                $ids[] = (int)$kk->getId();
            }

            if (empty($ids)) {
                $where[] = '1=0';
            } else {
                $ph = [];
                foreach ($ids as $i => $kid) {
                    $key = 'kid' . $i;
                    $ph[] = ':' . $key;
                    $params[$key] = $kid;
                }
                $where[] = 'id_kurz IN (' . implode(',', $ph) . ')';
            }
        }

        $cond = empty($where) ? '1=1' : implode(' AND ', $where);

        $prihlasky = PrihlaskaKurz::getAll($cond, $params, 'created_at DESC');

        // Mapy pre zobrazenie názvov
        $kurzy = Kurz::getAll('1=1', [], 'nazov ASC');
        $kurzById = [];
        foreach ($kurzy as $k) {
            $kurzById[(int)$k->getId()] = $k;
        }

        $osoby = Osoba::getAll('1=1');
        $osobaById = [];
        foreach ($osoby as $o) {
            $osobaById[(int)$o->getId()] = $o;
        }

        // obdobia do filtra
        $obdobia = Obdobie::getAll('1=1', [], 'datum_od DESC');

        if ((int)$request->value('ajax') === 1) {
            $rows = [];

            foreach ($prihlasky as $p) {
                $oid = (int)$p->getIdOsoba();
                $kid = (int)$p->getIdKurz();

                $osoba = $osobaById[$oid] ?? null;
                $kurz  = $kurzById[$kid] ?? null;

                $rows[] = [
                    'id' => (int)$p->getId(),
                    'stav' => (string)$p->getStav(),
                    'created_at' => (string)($p->getCreatedAt() ?? ''),
                    'osoba' => $osoba ? ($osoba->getMeno() . ' ' . $osoba->getPriezvisko()) : ('#' . $oid),
                    'kurz' => $kurz ? (string)$kurz->getNazov() : ('#' . $kid),
                    'can_decide' => ((string)$p->getStav() === 'nova'),

                    'url_show' => $this->url('adminPrihlaska.show', [
                        'id' => (int)$p->getId(),
                        'return_to' => $this->url('adminPrihlaska.index', [
                            'stav' => $stav,
                            'id_obdobie' => $idObdobie
                        ])
                    ]),

                    'url_approve' => $this->url('adminPrihlaska.approve', ['id' => (int)$p->getId()]),
                    'url_reject' => $this->url('adminPrihlaska.reject', ['id' => (int)$p->getId()]),
                ];
            }

            return $this->json([
                'ok' => true,
                'rows' => $rows,
            ]);
        }

        return $this->html([
            'prihlasky' => $prihlasky,
            'stav' => $stav,
            'idObdobie' => $idObdobie,
            'obdobia' => $obdobia,
            'kurzById' => $kurzById,
            'osobaById' => $osobaById,
        ]);
    }

    public function show(Request $request): Response
    {
        $id = (int)$request->value('id');
        if ($id <= 0) {
            throw new \Exception('Neplatné ID prihlášky.');
        }

        $p = PrihlaskaKurz::getOne($id);
        if ($p === null) {
            throw new \Exception('Prihláška neexistuje.');
        }

        $kurz = Kurz::getOne((int)$p->getIdKurz());
        $osoba = Osoba::getOne((int)$p->getIdOsoba());

        $returnTo = (string)$request->value('return_to');

        return $this->html([
            'prihlaska' => $p,
            'kurz' => $kurz,
            'osoba' => $osoba,
            'returnTo' => $returnTo,
        ]);
    }

    public function approve(Request $request): Response
    {
        return $this->decide($request, 'schvalena');
    }

    public function reject(Request $request): Response
    {
        return $this->decide($request, 'zamietnuta');
    }

    private function decide(Request $request, string $newStav): Response
    {
        if (!in_array($newStav, ['schvalena', 'zamietnuta'], true)) {
            throw new \Exception('Neplatný cieľový stav.');
        }

        $id = (int)$request->value('id');
        $p = PrihlaskaKurz::getOne($id);
        if ($p === null) {
            throw new \Exception('Prihláška neexistuje.');
        }

        if ($p->getStav() === 'nova') {
            $p->setStav($newStav);
            $p->save();
        }

        if ((int)$request->value('ajax') === 1) {
            return $this->json([
                'ok' => true,
                'id' => (int)$p->getId(),
                'stav' => (string)$p->getStav(),
            ]);
        }

        $returnTo = (string)$request->value('return_to');
        if ($returnTo !== '') {
            return $this->redirect($returnTo);
        }

        return $this->redirect($this->url('adminPrihlaska.index'));
    }
}
