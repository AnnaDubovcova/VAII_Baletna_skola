<?php

namespace App\Controllers;

use App\Models\Kurz;
use App\Models\PrihlaskaKurz;
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

        // otvorené kurzy
        $kurzy = Kurz::getAll(
            "prihlasovanie_otvorene = 1",
            [],
            "nazov ASC"
        );

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
        ]);
    }
}

