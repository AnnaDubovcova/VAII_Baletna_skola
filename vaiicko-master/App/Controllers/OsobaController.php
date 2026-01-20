<?php

namespace App\Controllers;

use App\Models\Osoba;
use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

class OsobaController extends BaseController
{
    public function authorize(Request $request, string $action): bool
    {
        if (!$this->user->isLoggedIn()) return false;

        if ($this->user->isAdmin()) {
            return in_array($action, ['show'], true); // admin len show
        }

        return in_array($action, ['index','create','edit','delete','show'], true);
    }


    public function index(Request $request): Response
    {
        $id_pouzivatel = $this->user->getIdPouzivatel();

        $osoby = Osoba::getAll(
            "id_pouzivatel = :id",
            ['id' => $id_pouzivatel],
            "created_at DESC"
        );

        return $this->html([
            'osoby' => $osoby,
        ]);
    }

    public function create(Request $request): Response
    {
        $errors = [];
        $osoba = new Osoba();

        if ($request->isPost()) {
            $this->fillAndValidate($request, $osoba, $errors);

            if (empty($errors)) {
                $osoba->setIdPouzivatel($this->user->getIdPouzivatel());
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

    public function edit(Request $request): Response
    {
        $id_osoba = (int)$request->value('id_osoba');
        $osoba = Osoba::getOne($id_osoba);

        if ($osoba === null) {
            throw new \Exception('Osoba nebola najdena.');
        }

        // Overime, ze osoba patri prihlasenemu pouzivatelovi.
        if ($osoba->getIdPouzivatel() !== $this->user->getIdPouzivatel()) {
            throw new \Exception('Nemate opravnenie upravovat tuto osobu.');
        }

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

    public function delete(Request $request): Response
    {
        $id_osoba = (int)$request->value('id_osoba');
        $osoba = Osoba::getOne($id_osoba);

        if ($osoba === null) {
            throw new \Exception('Osoba nebola najdena.');
        }

        // Overime, ze osoba patri prihlasenemu pouzivatelovi.
        if ($osoba->getIdPouzivatel() !== $this->user->getIdPouzivatel()) {
            throw new \Exception('Nemate opravnenie zmazat tuto osobu.');
        }

        $osoba->delete();
        return $this->redirect($this->url('osoba.index'));
    }

    public function show(Request $request): Response
    {
        $id_osoba = (int)$request->value('id_osoba');
        $osoba = Osoba::getOne($id_osoba);

        if ($osoba === null) {
            throw new \Exception('Osoba nebola najdena.');
        }

        // Overime opravnenia na zobrazenie osoby - len admin alebo vlastnik.
        if (!$this->user->isAdmin() &&
            $osoba->getIdPouzivatel() !== $this->user->getIdPouzivatel()) {
            throw new \Exception('Nemate opravnenie zobrazit tuto osobu.');
        }


        $canEdit = !$this->user->isAdmin();

        return $this->html([
            'osoba' => $osoba,
            'canEdit' => $canEdit,
        ]);

    }


    private function fillAndValidate(Request $request, Osoba $osoba, array &$errors): void
    {
        $meno = trim((string)$request->value('meno'));
        $priezvisko = trim((string)$request->value('priezvisko'));
        $datum_narodenia = trim((string)$request->value('datum_narodenia'));

        $email = trim((string)$request->value('email'));
        $telefon = trim((string)$request->value('telefon'));

        $z_meno = trim((string)$request->value('zastupca_meno'));
        $z_priezvisko = trim((string)$request->value('zastupca_priezvisko'));
        $z_email = trim((string)$request->value('zastupca_email'));
        $z_telefon = trim((string)$request->value('zastupca_telefon'));

        if ($meno === '' || mb_strlen($meno) > 80) {
            $errors['meno'] = 'Meno je povinne a maximalne 80 znakov.';
        } else {
            $osoba->setMeno($meno);
        }

        if ($priezvisko === '' || mb_strlen($priezvisko) > 80) {
            $errors['priezvisko'] = 'Priezvisko je povinne a maximalne 80 znakov.';
        } else {
            $osoba->setPriezvisko($priezvisko);
        }

        if ($datum_narodenia === '') {
            $errors['datum_narodenia'] = 'Datum narodenia je povinny.';
        } else {
            $osoba->setDatumNarodenia($datum_narodenia);
        }

        // Kontakt studenta - volitelne, ale ak je plnolety, budeme vyzadovat aspon jedno.
        $osoba->setEmail($email === '' ? null : $email);
        $osoba->setTelefon($telefon === '' ? null : $telefon);

        // Udaje zastupcu - volitelne, ale ak je neplnolety, budeme vyzadovat aspon jedno.
        $osoba->setZastupcaMeno($z_meno === '' ? null : $z_meno);
        $osoba->setZastupcaPriezvisko($z_priezvisko === '' ? null : $z_priezvisko);
        $osoba->setZastupcaEmail($z_email === '' ? null : $z_email);
        $osoba->setZastupcaTelefon($z_telefon === '' ? null : $z_telefon);

        $this->validateContactsByAge($datum_narodenia, $email, $telefon, $z_email, $z_telefon, $errors);
    }

    private function validateContactsByAge(
        string $datum_narodenia,
        string $email,
        string $telefon,
        string $z_email,
        string $z_telefon,
        array &$errors
    ): void {
        $isAdult = $this->isAdult($datum_narodenia);

        if ($isAdult) {
            if ($email === '' && $telefon === '') {
                $errors['global'] = 'Plnolety student musi mat vyplneny email alebo telefon.';
            }
        } else {
            if ($z_email === '' && $z_telefon === '') {
                $errors['global'] = 'Neplnolety student musi mat vyplneny email alebo telefon zakonneho zastupcu.';
            }
        }
    }

    private function isAdult(string $datum_narodenia): bool
    {
        try {
            $birth = new \DateTime($datum_narodenia);
            $today = new \DateTime();
            $age = $today->diff($birth)->y;
            return $age >= 18;
        } catch (\Exception $e) {
            // Ak datum nie je validny, nech sa to zachyti vo validacii povinneho datumu.
            return false;
        }
    }
}
