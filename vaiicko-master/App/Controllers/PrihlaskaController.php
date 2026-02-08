<?php

namespace App\Controllers;

use App\Models\Kurz;
use App\Models\PrihlaskaKurz;
use Framework\Http\Request;
use Framework\Http\Responses\Response;
use Framework\Http\HttpException;

class PrihlaskaController extends UserBaseController
{
    public function index(Request $request): Response
    {
        // Guard: active osoba must be selected and must belong to the logged-in user
        $activeOsoba = $this->requireActiveOsoba();
        if ($activeOsoba === null) {
            return $this->redirect($this->url('osoba.index'));
        }
        $activeOsobaId = (int)$activeOsoba->getId();

        $prihlasky = PrihlaskaKurz::getAll(
            'id_osoba = :id',
            ['id' => $activeOsobaId],
            'created_at DESC'
        );

        // Map: id_kurz => Kurz
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

            $kurzy = Kurz::getAll(
                'id_kurz IN (' . implode(',', $placeholders) . ')',
                $params
            );

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
        if (!$request->isPost()) {
            throw new HttpException(405, 'Method Not Allowed');
        }

        $idKurz = (int)$request->value('id_kurz');

        // Guard: active osoba must be selected and must belong to the logged-in user
        $osoba = $this->requireActiveOsoba();
        if ($osoba === null) {
            return $this->redirect($this->url('osoba.index'));
        }
        $activeOsobaId = (int)$osoba->getId();

        // Course must exist and be open
        $kurz = Kurz::getOne($idKurz);
        if ($kurz === null) {
            throw new HttpException(404, 'Kurz nebol nájdený.');
        }
        if (!$kurz->isPrihlasovanieOtvorene()) {
            throw new HttpException(403, 'Prihlasovanie na tento kurz nie je otvorené.');
        }

        // Check if application already exists (UX; DB should also enforce UNIQUE)
        $existing = PrihlaskaKurz::getAll(
            "id_osoba = :o AND id_kurz = :k",
            ['o' => $activeOsobaId, 'k' => $idKurz]
        );

        if (!empty($existing)) {
            /** @var PrihlaskaKurz $ex */
            $ex = $existing[0];

            // If canceled, reactivate
            if ($ex->getStav() === 'zrusena') {
                $ex->setStav('nova');

                // Update representative snapshot from current osoba data
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

        // Snapshot of representative at submission time
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
            throw new HttpException(400, 'Neplatné ID prihlášky.');
        }

        // Guard: active osoba must be selected and must belong to the logged-in user
        $activeOsoba = $this->requireActiveOsoba();
        if ($activeOsoba === null) {
            return $this->redirect($this->url('osoba.index'));
        }
        $activeOsobaId = (int)$activeOsoba->getId();

        $prihlaska = PrihlaskaKurz::getOne($id);
        if ($prihlaska === null) {
            throw new HttpException(404, 'Prihláška neexistuje.');
        }

        // Application must belong to active osoba (and therefore to the logged-in user)
        if ((int)$prihlaska->getIdOsoba() !== (int)$activeOsobaId) {
            throw new HttpException(403, 'K tejto prihláške nemáte prístup.');
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
        if (!$request->isPost()) {
            throw new HttpException(405, 'Method Not Allowed');
        }

        $id = (int)$request->value('id');
        if ($id <= 0) {
            throw new HttpException(400, 'Neplatné ID prihlášky.');
        }

        // Guard: active osoba must be selected and must belong to the logged-in user
        $activeOsoba = $this->requireActiveOsoba();
        if ($activeOsoba === null) {
            return $this->redirect($this->url('osoba.index'));
        }
        $activeOsobaId = (int)$activeOsoba->getId();

        $prihlaska = PrihlaskaKurz::getOne($id);
        if ($prihlaska === null) {
            throw new HttpException(404, 'Prihláška neexistuje.');
        }

        if ((int)$prihlaska->getIdOsoba() !== (int)$activeOsobaId) {
            throw new HttpException(403, 'K tejto prihláške nemáte prístup.');
        }

        if ($prihlaska->getStav() !== 'nova') {
            // Approved/rejected/canceled -> user cannot cancel
            return $this->redirect($this->url('prihlaska.index'));
        }

        $prihlaska->setStav('zrusena');
        $prihlaska->save();

        return $this->redirect($this->url('prihlaska.index'));
    }
}
