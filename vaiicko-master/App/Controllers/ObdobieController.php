<?php

namespace App\Controllers;
use App\Models\Obdobie;

use Framework\Core\BaseController;
use Framework\Http\Request;
use Framework\Http\Responses\Response;




class ObdobieController extends AdminController
{
    public function index(Request $request): Response
    {
        $obdobia = Obdobie::getAll();

        return $this->html([
            'obdobia' => $obdobia,
        ]);
    }


    public function create(Request $request): Response
    {
        $errors = [];
        $obdobie = new Obdobie();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->fillAndValidate($request, $obdobie, $errors);

            if (empty($errors)) {
                $obdobie->save();
                // po uložení presmeruj na zoznam
                return $this->redirect($this->url('obdobie.index'));
            }
        }

        return $this->html([
            'obdobie' => $obdobie,
            'errors' => $errors,
            'formAction' => 'create',
        ], 'form'); // použije form.view.php
    }

    public function edit(Request $request): Response
    {
        $id_obdobie = (int)$request->value('id_obdobie');
        $obdobie = Obdobie::getOne($id_obdobie);

        if ($obdobie === null) {

            throw new \Exception('Obdobie nenájdené.');
        }

        $errors = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->fillAndValidate($request, $obdobie, $errors);

            if (empty($errors)) {
                $obdobie->save();
                return $this->redirect($this->url('obdobie.index'));
            }
        }

        return $this->html([
            'obdobie' => $obdobie,
            'errors' => $errors,
            'formAction' => 'edit',
        ], 'form');
    }

    public function delete(Request $request): Response
    {
        $id_obdobie = (int)$request->value('id_obdobie');
        $obdobie = Obdobie::getOne($id_obdobie);
        if ($obdobie !== null) {
            $obdobie->delete();
        } else {
            throw new \Exception('Obdobie nenájdené.');
        }

        return $this->redirect($this->url('obdobie.index'));

    }

    /**
     * Spoločné naplnenie modelu + server-side validácia
     */
    private function fillAndValidate(Request $request, Obdobie $obdobie, array &$errors): void
    {
        $nazov = trim((string)$request->value('nazov'));
        $datumOd = trim((string)$request->value('datum_od'));
        $datumDo = trim((string)$request->value('datum_do'));
        $popis = trim((string)$request->value('popis'));

        // --- Server-side validácia ---

        if ($nazov === '') {
            $errors['nazov'] = 'Názov je povinný.';
        } elseif (mb_strlen($nazov) > 100) {
            $errors['nazov'] = 'Názov môže mať max. 100 znakov.';
        }

        if ($datumOd === '') {
            $errors['datum_od'] = 'Dátum od je povinný.';
        } elseif (!$this->isValidDate($datumOd)) {
            $errors['datum_od'] = 'Dátum od musí byť vo formáte RRRR-MM-DD.';
        }

        if ($datumDo === '') {
            $errors['datum_do'] = 'Dátum do je povinný.';
        } elseif (!$this->isValidDate($datumDo)) {
            $errors['datum_do'] = 'Dátum do musí byť vo formáte RRRR-MM-DD.';
        }

        if ($datumOd !== '' && $datumDo !== '' && $this->isValidDate($datumOd) && $this->isValidDate($datumDo)) {
            if (strtotime($datumDo) < strtotime($datumOd)) {
                $errors['datum_do'] = 'Dátum do musí byť po dátume od.';
            }
        }

        if ($popis !== '' && mb_strlen($popis) > 1000) {
            $errors['popis'] = 'Popis môže mať max. 1000 znakov.';
        }

        // Napln model len, keď chceme pokračovať (aj pri chybách, aby sa hodnoty vrátili do formulára)
        $obdobie->setNazov($nazov);
        $obdobie->setDatumOd($datumOd);
        $obdobie->setDatumDo($datumDo);
        $obdobie->setPopis($popis === '' ? null : $popis);
    }

    private function isValidDate(string $value): bool
    {
        // očakávame formát RRRR-MM-DD (HTML input type="date")
        $dt = \DateTime::createFromFormat('Y-m-d', $value);
        return $dt !== false && $dt->format('Y-m-d') === $value;
    }
}
