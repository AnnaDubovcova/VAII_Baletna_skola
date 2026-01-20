<?php

namespace App\Controllers;

use App\Models\Kurz;
use App\Models\Osoba;
use App\Models\PrihlaskaKurz;
use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

class PrihlaskaController extends BaseController
{
    public function authorize(Request $request, string $action): bool
    {
        return $this->user->isLoggedIn() && !$this->user->isAdmin();
    }

    public function index(Request $request): Response
    {
        $activeOsobaId = $this->getActiveOsobaId();

        if (!$activeOsobaId) {
            return $this->redirect($this->url('osoba.index'));
        }

        $prihlasky = PrihlaskaKurz::getAll(
            "id_osoba = :id",
            ['id' => $activeOsobaId],
            "created_at DESC"
        );

        // (voliteľné) môžeme neskôr doplniť join na kurz a zobraziť názvy
        return $this->html([
            'prihlasky' => $prihlasky,
            'activeOsobaId' => $activeOsobaId,
        ]);
    }

    public function create(Request $request): Response
    {
        $idKurz = (int)$request->value('id_kurz');
        $activeOsobaId = $this->getActiveOsobaId();

        if (!$activeOsobaId) {
            return $this->redirect($this->url('osoba.index'));
        }

        // bezpečnostná kontrola: aktívna osoba musí patriť userovi
        $osoba = Osoba::getOne($activeOsobaId);
        if ($osoba === null || $osoba->getIdPouzivatel() !== $this->user->getIdPouzivatel()) {
            throw new \Exception('Neplatná aktívna osoba.');
        }

        // kurz musí existovať a byť otvorený
        $kurz = Kurz::getOne($idKurz);
        if ($kurz === null) {
            throw new \Exception('Kurz nebol nájdený.');
        }
        if (!$kurz->isPrihlasovanieOtvorene()) {
            throw new \Exception('Prihlasovanie na tento kurz nie je otvorené.');
        }

        // skontrolujeme, či už neexistuje prihláška (kvôli UX, DB to aj tak chráni UNIQUE)
        $existing = PrihlaskaKurz::getAll(
            "id_osoba = :o AND id_kurz = :k",
            ['o' => $activeOsobaId, 'k' => $idKurz]
        );
        if (!empty($existing)) {
            return $this->redirect($this->url('kurzyUser.index'));
        }

        $p = new PrihlaskaKurz();
        $p->setIdOsoba($activeOsobaId);
        $p->setIdKurz($idKurz);
        $p->setStav('nova');

        // snapshot zákonného zástupcu v čase podania prihlášky
        $p->setZastupcaMeno($osoba->getZastupcaMeno());
        $p->setZastupcaPriezvisko($osoba->getZastupcaPriezvisko());
        $p->setZastupcaEmail($osoba->getZastupcaEmail());
        $p->setZastupcaTelefon($osoba->getZastupcaTelefon());

        $p->save();

        return $this->redirect($this->url('kurzyUser.index'));
    }
}
