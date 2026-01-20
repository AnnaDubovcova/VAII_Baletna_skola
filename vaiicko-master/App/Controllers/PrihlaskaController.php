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

        /*
        $prihlasky = PrihlaskaKurz::getAll(
            "id_osoba = :id",
            ['id' => $activeOsobaId],
            "created_at DESC"
        );

        // (voliteľné) môžeme neskôr doplniť join na kurz a zobraziť názvy
        return $this->html([
            'prihlasky' => $prihlasky,
            'activeOsobaId' => $activeOsobaId,
        ]);*/
        $activeOsoba = Osoba::getOne($activeOsobaId);
        if ($activeOsoba === null || $activeOsoba->getIdPouzivatel() !== $this->user->getIdPouzivatel()) {
            throw new \Exception('Neplatná aktívna osoba.');
        }

        $prihlasky = PrihlaskaKurz::getAll(
            'id_osoba = :id',
            ['id' => $activeOsobaId],
            'created_at DESC'
        );

// mapovanie: id_kurz => Kurz
        $kurzById = [];
        $kurzIds = [];
        foreach ($prihlasky as $p) {
            if ($p->getIdKurz() !== null) {
                $kurzIds[] = (int)$p->getIdKurz();
            }
        }
        $kurzIds = array_values(array_unique($kurzIds));

        if (!empty($kurzIds)) {
            $placeholders = [];
            $params = [];
            foreach ($kurzIds as $i => $id) {
                $key = 'k' . $i;
                $placeholders[] = ':' . $key;
                $params[$key] = $id;
            }

            $kurzy = Kurz::getAll('id_kurz IN (' . implode(',', $placeholders) . ')', $params);
            foreach ($kurzy as $k) {
                $kurzById[(int)$k->getId()] = $k;
            }
        }

        return $this->html([
            'prihlasky' => $prihlasky,
            'activeOsobaId' => $activeOsobaId,
            'activeOsoba' => $activeOsoba,
            'kurzById' => $kurzById,
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
            /** @var PrihlaskaKurz $ex */
            $ex = $existing[0];

            // ak je zrušená, reaktivujeme
            if ($ex->getStav() === 'zrusena') {
                $ex->setStav('nova');

                // aktualizuj snapshot zástupcu podľa aktuálnych údajov v osobe
                $ex->setZastupcaMeno($osoba->getZastupcaMeno());
                $ex->setZastupcaPriezvisko($osoba->getZastupcaPriezvisko());
                $ex->setZastupcaEmail($osoba->getZastupcaEmail());
                $ex->setZastupcaTelefon($osoba->getZastupcaTelefon());

                $ex->save();
            }

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

    public function show(Request $request): Response
    {
        $id = (int)$request->value('id');

        if ($id <= 0) {
            throw new \Exception('Neplatné ID prihlášky.');
        }

        $activeOsobaId = $this->getActiveOsobaId();
        if (!$activeOsobaId) {
            return $this->redirect($this->url('osoba.index'));
        }

        $activeOsoba = Osoba::getOne($activeOsobaId);
        if ($activeOsoba === null || $activeOsoba->getIdPouzivatel() !== $this->user->getIdPouzivatel()) {
            throw new \Exception('Neplatná aktívna osoba.');
        }

        $prihlaska = PrihlaskaKurz::getOne($id);
        if ($prihlaska === null) {
            throw new \Exception('Prihláška neexistuje.');
        }

        // prihláška musí patriť aktívnej osobe (a tým pádom userovi)
        if ((int)$prihlaska->getIdOsoba() !== (int)$activeOsobaId) {
            throw new \Exception('K tejto prihláške nemáte prístup.');
        }

        $kurz = Kurz::getOne((int)$prihlaska->getIdKurz());

        return $this->html([
            'prihlaska' => $prihlaska,
            'kurz' => $kurz,
            'activeOsoba' => $activeOsoba,
        ]);
    }

    public function cancel(Request $request): Response
    {
        $id = (int)$request->value('id');

        if ($id <= 0) {
            throw new \Exception('Neplatné ID prihlášky.');
        }

        $activeOsobaId = $this->getActiveOsobaId();
        if (!$activeOsobaId) {
            return $this->redirect($this->url('osoba.index'));
        }

        $prihlaska = PrihlaskaKurz::getOne($id);
        if ($prihlaska === null) {
            throw new \Exception('Prihláška neexistuje.');
        }

        if ((int)$prihlaska->getIdOsoba() !== (int)$activeOsobaId) {
            throw new \Exception('K tejto prihláške nemáte prístup.');
        }

        if ($prihlaska->getStav() !== 'nova') {
            // už schválené/zamietnuté/zrušené -> user nesmie
            return $this->redirect($this->url('prihlaska.index'));
        }

        $prihlaska->setStav('zrusena');
        $prihlaska->save();

        return $this->redirect($this->url('prihlaska.index'));
    }


}
