<?php

namespace App\Controllers;

use App\Models\Obdobie;
use App\Models\Skupina;
use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

class SkupinaController extends BaseController
{
    public function index(Request $request): Response
    {
        $skupiny = Skupina::getAll();
        $obdobia = Obdobie::getAll();

        // Pomocná mapa: id_obdobie => názov obdobia (aby sa vo view nevolalo getOne() v cykle)
        $obdobiaMap = [];
        foreach ($obdobia as $o) {
            $obdobiaMap[$o->getId()] = $o->getNazov();
        }

        return $this->html([
            'skupiny' => $skupiny,
            'obdobiaMap' => $obdobiaMap,
        ]);
    }

    public function create(Request $request): Response
    {
        $errors = [];
        $skupina = new Skupina();

        // Potrebujeme zoznam období na výber vo formulári
        $obdobia = Obdobie::getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->fillAndValidate($request, $skupina, $errors);

            // Ak zatiaľ neexistuje žiadne obdobie, skupinu nie je kam zaradiť
            if (empty($obdobia)) {
                $errors['global'] = 'Najprv je potrebné vytvoriť obdobie. Prejdi do sekcie Obdobia a vytvor aspoň jedno obdobie.';
                $errors['id_obdobie'] = 'Nie je možné vybrať obdobie, kým neexistuje.';
            }

            if (empty($errors)) {
                $skupina->save();
                return $this->redirect($this->url('skupina.index'));
            }
        }

        return $this->html([
            'skupina' => $skupina,
            'obdobia' => $obdobia,
            'errors' => $errors,
            'formAction' => 'create',
        ], 'form');
    }

    public function edit(Request $request): Response
    {
        $id_skupina = (int)$request->value('id_skupina');
        $skupina = Skupina::getOne($id_skupina);

        if ($skupina === null) {
            throw new \Exception('Skupina nenájdená.');
        }

        $errors = [];
        $obdobia = Obdobie::getAll();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->fillAndValidate($request, $skupina, $errors);

            if (empty($obdobia)) {
                $errors['global'] = 'Najprv je potrebné vytvoriť obdobie. Prejdi do sekcie Obdobia a vytvor aspoň jedno obdobie.';
                $errors['id_obdobie'] = 'Nie je možné vybrať obdobie, kým neexistuje.';
            }

            if (empty($errors)) {
                $skupina->save();
                return $this->redirect($this->url('skupina.index'));
            }
        }

        return $this->html([
            'skupina' => $skupina,
            'obdobia' => $obdobia,
            'errors' => $errors,
            'formAction' => 'edit',
        ], 'form');
    }

    public function delete(Request $request): Response
    {
        $id_skupina = (int)$request->value('id_skupina');
        $skupina = Skupina::getOne($id_skupina);

        if ($skupina !== null) {
            $skupina->delete();
        } else {
            throw new \Exception('Skupina nenájdená.');
        }

        return $this->redirect($this->url('skupina.index'));
    }

    /**
     * Spoločné naplnenie modelu + server-side validácia
     */
    private function fillAndValidate(Request $request, Skupina $skupina, array &$errors): void
    {
        $nazov = trim((string)$request->value('nazov'));
        $idObdobieRaw = $request->value('id_obdobie');
        $popis = trim((string)$request->value('popis'));

        // --- Server-side validácia ---

        if ($nazov === '') {
            $errors['nazov'] = 'Názov je povinný.';
        } elseif (mb_strlen($nazov) > 100) {
            $errors['nazov'] = 'Názov môže mať max. 100 znakov.';
        }

        // id_obdobie musí byť číslo a musí existovať v DB
        if ($idObdobieRaw === null || $idObdobieRaw === '') {
            $errors['id_obdobie'] = 'Obdobie je povinné.';
            $idObdobie = null;
        } elseif (!ctype_digit((string)$idObdobieRaw)) {
            $errors['id_obdobie'] = 'Neplatná hodnota obdobia.';
            $idObdobie = null;
        } else {
            $idObdobie = (int)$idObdobieRaw;
            if (Obdobie::getOne($idObdobie) === null) {
                $errors['id_obdobie'] = 'Vybrané obdobie neexistuje. Obnov stránku a vyber platné obdobie.';
            }
        }

        if ($popis !== '' && mb_strlen($popis) > 1000) {
            $errors['popis'] = 'Popis môže mať max. 1000 znakov.';
        }

        // Napln model aj pri chybách (aby sa hodnoty vrátili do formulára)
        $skupina->setNazov($nazov);
        if ($idObdobie !== null) {
            $skupina->setIdObdobie($idObdobie);
        }
        $skupina->setPopis($popis === '' ? null : $popis);
    }
}
