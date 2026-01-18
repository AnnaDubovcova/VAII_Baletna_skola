<?php

namespace App\Controllers;
use App\Models\TypKurzu;

use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

class TypKurzuController extends BaseController
{


    public function index(Request $request): Response
    {

        $typy_kurzu = TypKurzu::getAll();
        return $this->html([
            'typy_kurzu' => $typy_kurzu,
        ]);
    }

    public function create(Request $request): Response
    {
        $errors = [];
        $typ_kurzu = new TypKurzu();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->fillAndValidate($request, $typ_kurzu, $errors);

            if (empty($errors)) {
                $typ_kurzu->save();

                //presmeruj na zoznam typov kurzov
                return $this->redirect($this->url('typKurzu.index'));
            }
        }

        return $this->html([
            'typ_kurzu' => $typ_kurzu,
            'errors' => $errors,
            'formAction' => 'create',
        ], 'form');
    }

    public function edit(Request $request): Response
    {
        $id_typ_kurzu = (int)$request->value('id_typ_kurzu');
        $typ_kurzu = TypKurzu::getOne($id_typ_kurzu);

        if ($typ_kurzu === null) {
            throw new \Exception('Typ kurzu nenájdený.');
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->fillAndValidate($request, $typ_kurzu, $errors);

            if (empty($errors)) {
                $typ_kurzu->save();

                //presmeruj na zoznam typov kurzov
                return $this->redirect($this->url('typKurzu.index'));
            }
        }

        return $this->html([
            'typ_kurzu' => $typ_kurzu,
            'errors' => $errors,
            'formAction' => 'edit',
        ], 'form');
    }

    public function delete(Request $request): Response
    {
        $id_typ_kurzu = (int)$request->value('id_typ_kurzu');
        $typ_kurzu = TypKurzu::getOne($id_typ_kurzu);

        if ($typ_kurzu !== null) {
            $typ_kurzu->delete();
        } else {
            throw new \Exception('Typ kurzu nenajdeny.');
        }

        return $this->redirect($this->url('typKurzu.index'));
    }

    private function fillAndValidate(Request $request, TypKurzu $typ_kurzu, array &$errors): void
    {
        $nazov = trim((string)$request->value('nazov'));
        $popis = trim((string)$request->value('popis'));

        if ($nazov === '') {
            $errors['nazov'] = 'Názov je povinný.';
        } elseif (mb_strlen($nazov) > 100) {
            $errors['nazov'] = 'Názov môže mať maximálne 100 znakov.';

        } else {
            $typ_kurzu->setNazov($nazov);
        }

        if ($popis === '') {
            $typ_kurzu->setPopis(null);
        } else {
            $typ_kurzu->setPopis($popis);
        }

    }
}