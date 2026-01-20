<?php

namespace App\Controllers;

use App\Models\Kurz;
use App\Models\Obdobie;
use App\Models\TypKurzu;
use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

class KurzController extends AdminController
{
    public function index(Request $request): Response
    {
        $kurzy = Kurz::getAll();
        $obdobia = Obdobie::getAll();
        $typy = TypKurzu::getAll();

        // Mapa ID -> názov, aby sa vo view nerobili DB dotazy v cykle
        $obdobiaMap = [];
        foreach ($obdobia as $o) {
            $obdobiaMap[$o->getId()] = $o->getNazov();
        }

        $typyMap = [];
        foreach ($typy as $t) {
            $typyMap[$t->getId()] = $t->getNazov();
        }

        return $this->html([
            'kurzy' => $kurzy,
            'obdobiaMap' => $obdobiaMap,
            'typyMap' => $typyMap,
        ]);
    }

    public function create(Request $request): Response
    {
        $errors = [];
        $kurz = new Kurz();

        $obdobia = Obdobie::getAll();
        $typy = TypKurzu::getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->fillAndValidate($request, $kurz, $errors);

            // Bez obdobia a typu nemá kurz význam
            if (empty($obdobia)) {
                $errors['global'] = 'Najprv je potrebné vytvoriť obdobie. Prejdi do sekcie Obdobia a vytvor aspoň jedno obdobie.';
                $errors['id_obdobie'] = 'Nie je možné vybrať obdobie, kým neexistuje.';
            }
            if (empty($typy)) {
                $errors['global2'] = 'Najprv je potrebné vytvoriť typ kurzu. Prejdi do sekcie Typy kurzov a vytvor aspoň jeden typ.';
                $errors['id_typ_kurzu'] = 'Nie je možné vybrať typ kurzu, kým neexistuje.';
            }

            if (empty($errors)) {
                $kurz->save();
                return $this->redirect($this->url('kurz.index'));
            }
        }

        return $this->html([
            'kurz' => $kurz,
            'obdobia' => $obdobia,
            'typy' => $typy,
            'errors' => $errors,
            'formAction' => 'create',
        ], 'form');
    }

    public function edit(Request $request): Response
    {
        $id = (int)$request->value('id_kurz');
        $kurz = Kurz::getOne($id);

        if ($kurz === null) {
            throw new \Exception('Kurz nenájdený.');
        }

        $errors = [];
        $obdobia = Obdobie::getAll();
        $typy = TypKurzu::getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->fillAndValidate($request, $kurz, $errors);

            if (empty($obdobia)) {
                $errors['global'] = 'Najprv je potrebné vytvoriť obdobie. Prejdi do sekcie Obdobia a vytvor aspoň jedno obdobie.';
                $errors['id_obdobie'] = 'Nie je možné vybrať obdobie, kým neexistuje.';
            }
            if (empty($typy)) {
                $errors['global2'] = 'Najprv je potrebné vytvoriť typ kurzu. Prejdi do sekcie Typy kurzov a vytvor aspoň jeden typ.';
                $errors['id_typ_kurzu'] = 'Nie je možné vybrať typ kurzu, kým neexistuje.';
            }

            if (empty($errors)) {
                $kurz->save();
                return $this->redirect($this->url('kurz.index'));
            }
        }

        return $this->html([
            'kurz' => $kurz,
            'obdobia' => $obdobia,
            'typy' => $typy,
            'errors' => $errors,
            'formAction' => 'edit',
        ], 'form');
    }

    public function delete(Request $request): Response
    {
        $id = (int)$request->value('id_kurz');
        $kurz = Kurz::getOne($id);

        if ($kurz !== null) {
            $kurz->delete();
        } else {
            throw new \Exception('Kurz nenájdený.');
        }

        return $this->redirect($this->url('kurz.index'));
    }

    /**
     * Naplnenie modelu + server-side validácia podľa DB
     */
    private function fillAndValidate(Request $request, Kurz $kurz, array &$errors): void
    {
        $nazov = trim((string)$request->value('nazov'));
        $idTypRaw = $request->value('id_typ_kurzu');
        $idObdRaw = $request->value('id_obdobie');
        $popis = trim((string)$request->value('popis'));
        $cenaRaw = trim((string)$request->value('cena'));
        $otvoreneRaw = $request->value('prihlasovanie_otvorene');

        // Názov
        if ($nazov === '') {
            $errors['nazov'] = 'Názov je povinný.';
        } elseif (mb_strlen($nazov) > 100) {
            $errors['nazov'] = 'Názov môže mať max. 100 znakov.';
        }

        // Typ kurzu (FK musí existovať)
        $idTyp = null;
        if ($idTypRaw === null || $idTypRaw === '') {
            $errors['id_typ_kurzu'] = 'Typ kurzu je povinný.';
        } elseif (!ctype_digit((string)$idTypRaw)) {
            $errors['id_typ_kurzu'] = 'Neplatná hodnota typu kurzu.';
        } else {
            $idTyp = (int)$idTypRaw;
            if (TypKurzu::getOne($idTyp) === null) {
                $errors['id_typ_kurzu'] = 'Vybraný typ kurzu neexistuje. Obnov stránku a vyber platnú hodnotu.';
            }
        }

        // Obdobie (FK musí existovať)
        $idObd = null;
        if ($idObdRaw === null || $idObdRaw === '') {
            $errors['id_obdobie'] = 'Obdobie je povinné.';
        } elseif (!ctype_digit((string)$idObdRaw)) {
            $errors['id_obdobie'] = 'Neplatná hodnota obdobia.';
        } else {
            $idObd = (int)$idObdRaw;
            if (Obdobie::getOne($idObd) === null) {
                $errors['id_obdobie'] = 'Vybrané obdobie neexistuje. Obnov stránku a vyber platnú hodnotu.';
            }
        }

        // Popis
        if ($popis !== '' && mb_strlen($popis) > 1000) {
            $errors['popis'] = 'Popis môže mať max. 1000 znakov.';
        }

        // Cena (DECIMAL(8,2) -> validujeme číslo a max. 2 desatinné miesta)
        $cena = null;
        if ($cenaRaw !== '') {
            // Povolené: 120, 120.5, 120.50
            if (!preg_match('/^\d+(\.\d{1,2})?$/', $cenaRaw)) {
                $errors['cena'] = 'Cena musí byť číslo s max. 2 desatinnými miestami (napr. 120.00).';
            } else {
                $cena = (float)$cenaRaw;
            }
        }

        // Prihlasovanie otvorené (checkbox)
        $otvorene = ($otvoreneRaw !== null); // ak checkbox prišiel, je zaškrtnutý

        // Naplnenie modelu aj pri chybách (aby sa hodnoty vrátili do formulára)
        $kurz->setNazov($nazov);
        if ($idTyp !== null) {
            $kurz->setIdTypKurzu($idTyp);
        }
        if ($idObd !== null) {
            $kurz->setIdObdobie($idObd);
        }
        $kurz->setPopis($popis === '' ? null : $popis);
        $kurz->setCena($cena);
        $kurz->setPrihlasovanieOtvorene($otvorene);
    }
}
