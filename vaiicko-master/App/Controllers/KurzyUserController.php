<?php

namespace App\Controllers;

use App\Models\Kurz;
use App\Models\PrihlaskaKurz;
use App\Models\TypKurzu;
use App\Models\Obdobie;
use Framework\Http\Request;
use Framework\Http\Responses\Response;
use Framework\Http\HttpException;

class KurzyUserController extends UserBaseController
{
    public function index(Request $request): Response
    {
        // Guard: active osoba must be selected and must belong to the logged-in user
        $activeOsoba = $this->requireActiveOsoba();
        if ($activeOsoba === null) {
            return $this->redirect($this->url('osoba.index'));
        }
        $activeOsobaId = (int)$activeOsoba->getId();

        // Open courses
        $kurzy = Kurz::getAll(
            "prihlasovanie_otvorene = 1",
            [],
            "nazov ASC"
        );

        // Map course types by ID
        $typy = TypKurzu::getAll();
        $typById = [];
        foreach ($typy as $t) {
            $typById[(int)$t->getId()] = $t;
        }

        // Map periods by ID
        $obdobia = Obdobie::getAll();
        $obdobieById = [];
        foreach ($obdobia as $o) {
            $obdobieById[(int)$o->getId()] = $o;
        }

        // Existing applications for active person (for displaying state)
        $prihlasky = PrihlaskaKurz::getAll(
            "id_osoba = :id",
            ['id' => $activeOsobaId]
        );

        // Map: id_kurz => stav
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
        // Guard: active osoba must be selected and must belong to the logged-in user
        $activeOsoba = $this->requireActiveOsoba();
        if ($activeOsoba === null) {
            return $this->redirect($this->url('osoba.index'));
        }
        $activeOsobaId = (int)$activeOsoba->getId();

        $idKurz = (int)$request->value('id_kurz');
        if ($idKurz <= 0) {
            throw new HttpException(400, 'Neplatné ID kurzu.');
        }

        $kurz = Kurz::getOne($idKurz);
        if ($kurz === null) {
            throw new HttpException(404, 'Kurz neexistuje.');
        }

        // Application state (if exists)
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
