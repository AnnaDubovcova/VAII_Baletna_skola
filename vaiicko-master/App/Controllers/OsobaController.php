<?php

namespace App\Controllers;

use App\Models\Osoba;
use Framework\Http\HttpException;
use Framework\Http\Request;
use Framework\Http\Responses\Response;
use Framework\Http\Session;

class OsobaController extends OsobaBaseController
{
    /**
     * Zoznam osôb prihláseného používateľa.
     */
    public function index(Request $request): Response
    {
        $idPouzivatel = $this->identity()->getIdPouzivatel();

        $osoby = Osoba::getAll(
            "id_pouzivatel = :id",
            ['id' => $idPouzivatel],
            "created_at DESC"
        );

        $session = new Session();
        $activeOsobaId = $session->get('active_osoba_id');

        return $this->html([
            'osoby' => $osoby,
            'activeOsobaId' => $activeOsobaId,
        ]);
    }

    /**
     * Vytvorenie novej osoby.
     */
    public function create(Request $request): Response
    {
        $errors = [];
        $osoba = new Osoba();

        if ($request->isPost()) {
            $this->fillAndValidate($request, $osoba, $errors);

            if (empty($errors)) {
                $osoba->setIdPouzivatel($this->identity()->getIdPouzivatel());
                $osoba->save();

                return $this->redirect($this->url('osoba.index'));
            }
        }

        return $this->html([
            'osoba' => $osoba,
            'errors' => $errors,
            'formAction' => 'create',
        ], 'form');
    }

    /**
     * Úprava údajov osoby.
     */
    public function edit(Request $request): Response
    {
        $idOsoba = (int)$request->value('id_osoba');
        if ($idOsoba <= 0) {
            throw new HttpException(400, 'Neplatné ID osoby.');
        }

        $osoba = $this->requireOwnedOsoba($idOsoba);
        $errors = [];

        if ($request->isPost()) {
            $this->fillAndValidate($request, $osoba, $errors);

            if (empty($errors)) {
                $osoba->save();
                return $this->redirect($this->url('osoba.index'));
            }
        }

        return $this->html([
            'osoba' => $osoba,
            'errors' => $errors,
            'formAction' => 'edit',
        ], 'form');
    }

    /**
     * Zmazanie osoby používateľa.
     */
    public function delete(Request $request): Response
    {
        $idOsoba = (int)$request->value('id_osoba');
        if ($idOsoba <= 0) {
            throw new HttpException(400, 'Neplatné ID osoby.');
        }

        $osoba = $this->requireOwnedOsoba($idOsoba);
        $osoba->delete();

        // Ak bola zmazaná aktívna osoba, odstránime ju zo session
        $session = new Session();
        if ((int)$session->get('active_osoba_id') === $idOsoba) {
            $session->set('active_osoba_id', null);
        }

        return $this->redirect($this->url('osoba.index'));
    }

    /**
     * Zobrazenie detailu osoby.
     * Admin môže zobraziť ľubovoľnú osobu, používateľ len vlastnú.
     */
    public function show(Request $request): Response
    {
        $idOsoba = (int)$request->value('id_osoba');
        if ($idOsoba <= 0) {
            throw new HttpException(400, 'Neplatné ID osoby.');
        }

        $osoba = Osoba::getOne($idOsoba);
        if ($osoba === null) {
            throw new HttpException(404, 'Osoba nebola nájdená.');
        }

        $identity = $this->identity();

        if (!$identity->isAdmin() &&
            (int)$osoba->getIdPouzivatel() !== (int)$identity->getIdPouzivatel()) {
            throw new HttpException(403, 'Nemáte oprávnenie zobraziť túto osobu.');
        }

        return $this->html([
            'osoba' => $osoba,
            'canEdit' => !$identity->isAdmin(),
            'user' => $identity,
            'returnTo' => (string)$request->value('return_to'),
        ]);
    }

    /**
     * Nastavenie aktívnej osoby do session.
     */
    public function select(Request $request): Response
    {
        $idOsoba = (int)$request->value('id_osoba');
        if ($idOsoba <= 0) {
            throw new HttpException(400, 'Neplatné ID osoby.');
        }

        $osoba = $this->requireOwnedOsoba($idOsoba);

        $session = new Session();
        $session->set('active_osoba_id', (int)$osoba->getId());

        return $this->redirect($this->url('osoba.index'));
    }

    /**
     * Naplnenie modelu z requestu a validácia vstupov.
     */
    private function fillAndValidate(Request $request, Osoba $osoba, array &$errors): void
    {
        $meno = trim((string)$request->value('meno'));
        $priezvisko = trim((string)$request->value('priezvisko'));
        $datumNarodenia = trim((string)$request->value('datum_narodenia'));

        $email = trim((string)$request->value('email'));
        $telefon = trim((string)$request->value('telefon'));

        $zMeno = trim((string)$request->value('zastupca_meno'));
        $zPriezvisko = trim((string)$request->value('zastupca_priezvisko'));
        $zEmail = trim((string)$request->value('zastupca_email'));
        $zTelefon = trim((string)$request->value('zastupca_telefon'));

        if ($meno === '' || mb_strlen($meno) > 80) {
            $errors['meno'] = 'Meno je povinné a maximálne 80 znakov.';
        } else {
            $osoba->setMeno($meno);
        }

        if ($priezvisko === '' || mb_strlen($priezvisko) > 80) {
            $errors['priezvisko'] = 'Priezvisko je povinné a maximálne 80 znakov.';
        } else {
            $osoba->setPriezvisko($priezvisko);
        }

        if ($datumNarodenia === '') {
            $errors['datum_narodenia'] = 'Dátum narodenia je povinný.';
        } else {
            $osoba->setDatumNarodenia($datumNarodenia);
        }

        $osoba->setEmail($email === '' ? null : $email);
        $osoba->setTelefon($telefon === '' ? null : $telefon);

        $osoba->setZastupcaMeno($zMeno === '' ? null : $zMeno);
        $osoba->setZastupcaPriezvisko($zPriezvisko === '' ? null : $zPriezvisko);
        $osoba->setZastupcaEmail($zEmail === '' ? null : $zEmail);
        $osoba->setZastupcaTelefon($zTelefon === '' ? null : $zTelefon);

        $this->validateContactsByAge(
            $datumNarodenia,
            $email,
            $telefon,
            $zEmail,
            $zTelefon,
            $errors
        );
    }

    /**
     * Kontrola povinných kontaktných údajov podľa veku osoby.
     */
    private function validateContactsByAge(
        string $datumNarodenia,
        string $email,
        string $telefon,
        string $zEmail,
        string $zTelefon,
        array &$errors
    ): void {
        $isAdult = $this->isAdult($datumNarodenia);

        if ($isAdult) {
            if ($email === '' && $telefon === '') {
                $errors['global'] =
                    'Plnoletý študent musí mať vyplnený email alebo telefón.';
            }
        } else {
            if ($zEmail === '' && $zTelefon === '') {
                $errors['global'] =
                    'Neplnoletý študent musí mať vyplnený email alebo telefón zákonného zástupcu.';
            }
        }
    }

    private function isAdult(string $datumNarodenia): bool
    {
        try {
            $birth = new \DateTime($datumNarodenia);
            $today = new \DateTime();
            return $today->diff($birth)->y >= 18;
        } catch (\Exception $e) {
            return false;
        }
    }
}
