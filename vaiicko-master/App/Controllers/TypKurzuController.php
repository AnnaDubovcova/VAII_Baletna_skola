<?php

namespace App\Controllers;
use App\Models\TypKurzu;

use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;

class TypKurzuController extends AdminController
{


    public function index(Request $request): Response
    {

        $typy_kurzu = TypKurzu::getAll();

        $error = (string)$request->value('error');
        if ($error === '') $error = null;

        return $this->html([
            'typy_kurzu' => $typy_kurzu,
            'error' => $error,
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
        $typ = TypKurzu::getOne($id_typ_kurzu);

        if ($typ === null) {
            throw new \Exception('Typ kurzu nenájdený.');
        }

        try {
            $typ->delete();
            return $this->redirect($this->url('typKurzu.index'));
        } catch (\Throwable $e) {

            // Zisti SQLSTATE kód (niekedy je v previous)
            $prev = $e->getPrevious();
            $sqlState = null;

            if ($e instanceof \PDOException) {
                $sqlState = $e->getCode();
            } elseif ($prev instanceof \PDOException) {
                $sqlState = $prev->getCode();
            }

            $isFkViolation =
                ($sqlState === '23000') ||
                str_contains((string)$e->getMessage(), 'SQLSTATE[23000]') ||
                ($prev && str_contains((string)$prev->getMessage(), 'SQLSTATE[23000]'));

            if ($isFkViolation) {
                return $this->redirect($this->url('typKurzu.index', [
                    'error' => 'Typ kurzu nie je možné zmazať, pretože existujú kurzy, ktoré naň odkazujú.'
                ]));
            }


            // iné chyby nech idú ďalej
            throw $e;
        }
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