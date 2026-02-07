<?php

namespace App\Controllers;

use App\Models\Skupina;
use App\Models\Udalost;
use Framework\Http\Request;
use Framework\Http\Responses\Response;
use Framework\DB\Connection;

class UdalostController extends AdminController
{
    /**
     * Zoznam udalostí – implicitne filtrovaný podľa aktívneho obdobia.
     */
    public function index(Request $request): Response
    {
        $obdobieId = $this->getActiveObdobieId();
        if ($obdobieId === null) {
            throw new \Exception('Nie je zvolené aktívne obdobie.');
        }

        $udalosti = Udalost::getAll(
            'id_obdobie = :o',
            ['o' => $obdobieId],
            'zaciatok DESC'
        );

        // Načítanie skupín pre všetky udalosti naraz (bez N+1 dotazov)
        $udalostIds = array_map(fn($u) => (int)$u->getId(), $udalosti);
        $skupinyByUdalost = $this->getSkupinyByUdalostIds($udalostIds);

        return $this->html([
            'udalosti' => $udalosti,
            'skupinyByUdalost' => $skupinyByUdalost,
        ]);
    }

    /**
     * Detail udalosti.
     */
    public function show(Request $request): Response
    {
        $id = (int)$request->value('id_udalost');
        $udalost = Udalost::getOne($id);

        if ($udalost === null) {
            throw new \Exception('Udalosť nenájdená.');
        }

        $skupiny = $this->getSkupinyForUdalost($id);

        $returnTo = $this->getSafeReturnTo($request);


        return $this->html([
            'udalost' => $udalost,
            'skupiny' => $skupiny,
            'returnTo' => $returnTo,
        ]);
    }


    /**
     * Vytvorenie novej udalosti – viazané na aktívne obdobie.
     */
    public function create(Request $request): Response
    {
        $obdobieId = $this->getActiveObdobieId();
        if ($obdobieId === null) {
            return $this->redirect($this->url('obdobie.create'));
        }


        $udalost = new Udalost();
        $udalost->setIdObdobie($obdobieId);

        $errors = [];

        // Admin môže vyberať len skupiny z aktívneho obdobia
        $skupiny = Skupina::getAll(
            'id_obdobie = :o',
            ['o' => $obdobieId],
            'nazov ASC'
        );

        if ($request->isPost()) {
            $selected = $this->selectedSkupinyFromRequest($request);
            $this->fillAndValidate($request, $udalost, $selected, $errors);

            if (empty($errors)) {
                $udalost->save();
                $udalost->syncSkupiny($selected);

                return $this->redirect($this->url('udalost.index'));
            }
        }

        return $this->html([
            'udalost' => $udalost,
            'errors' => $errors,
            'skupiny' => $skupiny,
            'selectedSkupiny' => [],
            'formAction' => 'create',
        ], 'form');
    }

    /**
     * Úprava existujúcej udalosti.
     * Obdobie sa nemení – skupiny musia patriť do rovnakého obdobia.
     */
    public function edit(Request $request): Response
    {
        $id = (int)$request->value('id_udalost');
        $udalost = Udalost::getOne($id);

        if ($udalost === null) {
            throw new \Exception('Udalosť nenájdená.');
        }

        $errors = [];
        $obdobieId = (int)$udalost->getIdObdobie();

        $skupiny = Skupina::getAll(
            'id_obdobie = :o',
            ['o' => $obdobieId],
            'nazov ASC'
        );

        $selected = $udalost->getSkupinaIds();

        $returnTo = $this->getSafeReturnTo($request);


        if ($request->isPost()) {
            $selected = $this->selectedSkupinyFromRequest($request);
            $this->fillAndValidate($request, $udalost, $selected, $errors);

            if (empty($errors)) {
                $udalost->save();
                $udalost->syncSkupiny($selected);


                if ($returnTo) {
                    return $this->redirect($returnTo);
                }
                return $this->redirect($this->url('udalost.index'));
            }
        }

        return $this->html([
            'udalost' => $udalost,
            'errors' => $errors,
            'skupiny' => $skupiny,
            'selectedSkupiny' => $selected,
            'formAction' => 'edit',
            'returnTo' => $returnTo,
        ], 'form');
    }

    /**
     * Zmazanie udalosti + väzieb na skupiny.
     */
    public function delete(Request $request): Response
    {
        $id = (int)$request->value('id_udalost');

        $udalost = Udalost::getOne($id);
        if ($udalost === null) {
            throw new \Exception('Udalosť nenájdená.');
        }

        $returnTo = $this->getSafeReturnTo($request);

        $udalost->syncSkupiny([]); // vymaže väzby

        $udalost->delete();

        if ($returnTo) {
            return $this->redirect($returnTo);
        }
        return $this->redirect($this->url('udalost.index'));
    }

    // =========================================================
    // Helper metódy – technická M:N logika (pivot tabuľka)
    // =========================================================

    /**
     * Získa ID skupín z requestu (checkboxy).
     */
    private function selectedSkupinyFromRequest(Request $request): array
    {
        $raw = $request->value('id_skupina');

        if (!is_array($raw)) {
            return [];
        }

        $ids = [];
        foreach ($raw as $v) {
            $id = (int)$v;
            if ($id > 0) {
                $ids[] = $id;
            }
        }

        return array_values(array_unique($ids));
    }

    /**
     * Vráti skupiny priradené ku konkrétnej udalosti.
     */
    private function getSkupinyForUdalost(int $idUdalost): array
    {
        $con = Connection::getInstance();
        $stmt = $con->prepare(
            'SELECT s.id_skupina, s.nazov
             FROM udalost_skupina us
             JOIN skupina s ON s.id_skupina = us.id_skupina
             WHERE us.id_udalost = :u
             ORDER BY s.nazov ASC'
        );
        $stmt->execute(['u' => $idUdalost]);

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Vráti mapu: id_udalost → [skupiny].
     */
    private function getSkupinyByUdalostIds(array $udalostIds): array
    {
        if (empty($udalostIds)) {
            return [];
        }

        $placeholders = [];
        $params = [];
        foreach ($udalostIds as $i => $id) {
            $key = 'u' . $i;
            $placeholders[] = ':' . $key;
            $params[$key] = (int)$id;
        }

        $sql = '
            SELECT us.id_udalost, s.id_skupina, s.nazov
            FROM udalost_skupina us
            JOIN skupina s ON s.id_skupina = us.id_skupina
            WHERE us.id_udalost IN (' . implode(',', $placeholders) . ')
            ORDER BY s.nazov ASC
        ';

        $con = Connection::getInstance();
        $stmt = $con->prepare($sql);
        $stmt->execute($params);

        $out = [];
        foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $r) {
            $out[(int)$r['id_udalost']][] = [
                'id' => (int)$r['id_skupina'],
                'nazov' => (string)$r['nazov'],
            ];
        }

        return $out;
    }

    /**
     * Validácia vstupov + naplnenie modelu.
     */
    private function fillAndValidate(
        Request $request,
        Udalost $udalost,
        array $selectedSkupiny,
        array &$errors
    ): void {
        $nazov = trim((string)$request->value('nazov'));
        $typ = trim((string)$request->value('typ'));
        $zaciatok = trim((string)$request->value('zaciatok'));
        $koniec = trim((string)$request->value('koniec'));
        $miesto = trim((string)$request->value('miesto'));
        $popis = trim((string)$request->value('popis'));

        if ($nazov === '' || mb_strlen($nazov) > 150) {
            $errors['nazov'] = 'Názov je povinný (max 150 znakov).';
        } else {
            $udalost->setNazov($nazov);
        }

        $allowedTypes = ['trening', 'nacvik', 'vystupenie', 'ine'];
        if ($typ === '' || !in_array($typ, $allowedTypes, true)) {
            $errors['typ'] = 'Neplatný typ udalosti.';
        } else {
            $udalost->setTyp($typ);
        }

        if ($zaciatok === '') {
            $errors['zaciatok'] = 'Začiatok je povinný.';
        } else {
            $udalost->setZaciatok($this->toDbDateTime($zaciatok));
        }

        if ($koniec === '') {
            $udalost->setKoniec(null);
        } else {
            $udalost->setKoniec($this->toDbDateTime($koniec));
        }

        $udalost->setMiesto($miesto === '' ? null : $miesto);
        $udalost->setPopis($popis === '' ? null : $popis);

    }

    private function toDbDateTime(string $datetimeLocal): string
    {
        $s = str_replace('T', ' ', $datetimeLocal);
        if (strlen($s) === 16) {
            $s .= ':00';
        }
        return $s;
    }

    private function getSafeReturnTo(Request $request): ?string
    {
        $returnTo = $request->value('return_to');

        if (!is_string($returnTo)) {
            return null;
        }

        $returnTo = trim($returnTo);
        if ($returnTo === '') {
            return null;
        }

        // blokuj externé URL (open redirect)
        if (strpos($returnTo, '://') !== false) {
            return null;
        }
        if (strpos($returnTo, '//') === 0) {
            return null;
        }

        return $returnTo;
    }


}