<?php

namespace App\Controllers;

use App\Models\Kurz;
use App\Models\PrihlaskaKurz;
use App\Models\Osoba;
use App\Models\TypKurzu;
use App\Models\Obdobie;

use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

class KurzyUserController extends BaseController
{
    public function authorize(Request $request, string $action): bool
    {
        return $this->user->isLoggedIn() && !$this->user->isAdmin();
    }

    public function index(Request $request): Response
    {
        $activeOsobaId = $this->getActiveOsobaId();

        if (!$activeOsobaId) {
            // najprv si vyber osobu
            return $this->redirect($this->url('osoba.index'));
        }


        // bezpečnostná kontrola: aktívna osoba musí patriť userovi
        $activeOsoba = Osoba::getOne($activeOsobaId);

        if ($activeOsoba === null || $activeOsoba->getIdPouzivatel() !== $this->user->getIdPouzivatel()) {
            throw new \Exception('Neplatná aktívna osoba.');
        }

        // otvorené kurzy
        $kurzy = Kurz::getAll(
            "prihlasovanie_otvorene = 1",
            [],
            "nazov ASC"
        );

        $typy = TypKurzu::getAll();
        $typById = [];
        foreach ($typy as $t) {
            $typById[(int)$t->getId()] = $t;
        }

        $obdobia = Obdobie::getAll();
        $obdobieById = [];
        foreach ($obdobia as $o) {
            $obdobieById[(int)$o->getId()] = $o;
        }


        // existujúce prihlášky aktívnej osoby (kvôli zobrazeniu stavu)
        $prihlasky = PrihlaskaKurz::getAll(
            "id_osoba = :id",
            ['id' => $activeOsobaId]
        );

        // mapovanie: id_kurz => stav
        $stavByKurzId = [];
        foreach ($prihlasky as $p) {
            if ($p->getIdKurz() !== null) {
                $stavByKurzId[(int)$p->getIdKurz()] = (string)$p->getStav();
            }
        }

        return $this->html([
            'kurzy' => $kurzy,
            'stavByKurzId' => $stavByKurzId,
            'activeOsobaId' => $activeOsobaId,
            'activeOsoba' => $activeOsoba,
            'typById' => $typById,
            'obdobieById' => $obdobieById,

        ]);
    }

    public function show(Request $request): Response
    {
        $activeOsobaId = $this->getActiveOsobaId();
        if (!$activeOsobaId) {
            return $this->redirect($this->url('osoba.index'));
        }

        $activeOsoba = Osoba::getOne($activeOsobaId);
        if ($activeOsoba === null || $activeOsoba->getIdPouzivatel() !== $this->user->getIdPouzivatel()) {
            throw new \Exception('Neplatná aktívna osoba.');
        }

        $idKurz = (int)$request->value('id_kurz');
        if ($idKurz <= 0) {
            throw new \Exception('Neplatné ID kurzu.');
        }

        $kurz = Kurz::getOne($idKurz);
        if ($kurz === null) {
            throw new \Exception('Kurz neexistuje.');
        }

        // stav prihlášky (ak existuje)
        $existing = PrihlaskaKurz::getAll(
            "id_osoba = :o AND id_kurz = :k",
            ['o' => $activeOsobaId, 'k' => $idKurz]
        );
        $stav = !empty($existing) ? (string)$existing[0]->getStav() : null;

        return $this->html([
            'kurz' => $kurz,
            'activeOsoba' => $activeOsoba,
            'stav' => $stav,
        ]);
    }

}

