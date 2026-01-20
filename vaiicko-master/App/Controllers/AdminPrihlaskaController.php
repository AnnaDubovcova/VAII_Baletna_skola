<?php

namespace App\Controllers;

use App\Models\Kurz;
use App\Models\Osoba;
use App\Models\PrihlaskaKurz;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

class AdminPrihlaskaController extends AdminController
{
    public function index(Request $request): Response
    {
        $stav = trim((string)$request->value('stav'));
        $idKurz = (int)$request->value('id_kurz');

        $where = [];
        $params = [];

        if ($stav !== '') {
            $where[] = 'stav = :stav';
            $params['stav'] = $stav;
        }
        if ($idKurz > 0) {
            $where[] = 'id_kurz = :k';
            $params['k'] = $idKurz;
        }

        $cond = empty($where) ? '1=1' : implode(' AND ', $where);

        $prihlasky = PrihlaskaKurz::getAll($cond, $params, 'created_at DESC');

        // Mapy pre zobrazenie názvov
        $kurzy = Kurz::getAll('', [], 'nazov ASC');
        $kurzById = [];
        foreach ($kurzy as $k) {
            $kurzById[(int)$k->getId()] = $k;
        }

        $osoby = Osoba::getAll();
        $osobaById = [];
        foreach ($osoby as $o) {
            $osobaById[(int)$o->getId()] = $o;
        }

        return $this->html([
            'prihlasky' => $prihlasky,
            'stav' => $stav,
            'idKurz' => $idKurz,
            'kurzy' => $kurzy,
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
        $id = (int)$request->value('id');
        $p = PrihlaskaKurz::getOne($id);
        if ($p === null) {
            throw new \Exception('Prihláška neexistuje.');
        }

        // odporúčané: meniť len z "nova"
        if ($p->getStav() === 'nova') {
            $p->setStav('schvalena');
            $p->save();
        }

        $returnTo = (string)$request->value('return_to');
        if ($returnTo !== '') {
            return $this->redirect($returnTo);
        }
        return $this->redirect($this->url('adminPrihlaska.index'));


    }

    public function reject(Request $request): Response
    {
        $id = (int)$request->value('id');
        $p = PrihlaskaKurz::getOne($id);
        if ($p === null) {
            throw new \Exception('Prihláška neexistuje.');
        }

        if ($p->getStav() === 'nova') {
            $p->setStav('zamietnuta');
            $p->save();
        }

        $returnTo = (string)$request->value('return_to');
        if ($returnTo !== '') {
            return $this->redirect($returnTo);
        }
        return $this->redirect($this->url('adminPrihlaska.index'));

    }
}
